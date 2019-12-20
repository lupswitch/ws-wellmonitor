package glg_hmi;

import java.io.*;
import java.net.*;
import javax.imageio.*;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import java.awt.Image;
import java.awt.image.BufferedImage;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Timestamp;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Properties;


import com.genlogic.*;

public final class GlgProcessServlet_vetra extends HttpServlet 
   implements GlgErrorHandler
{
   static final String site = "vetra";
   static final long serialVersionUID = 0;

   static Connection connection = null;

   static List < GlgObject > lstViewport = new ArrayList < GlgObject > ();
   static List < String > lstIds = new ArrayList < String > ();
   static List < List < String > > lstTags = new ArrayList < List < String > > ();
   static List < List < String > > lstTagsType = new ArrayList < List < String > > ();
   int viewportIndex = 0;

   /////////////////////////////////////////////////////////////////
   // A wrapper around the main method, doGet2(), to properly handle
   // the access synchronization and unlocking on an error.
   /////////////////////////////////////////////////////////////////
   public void doGet(HttpServletRequest request,
       HttpServletResponse response)
   throws ServletException{
       try {
           doGet2(request, response); 
       } catch (Exception e) {
           // Unlock if was interrupted by the exception while locked.
           GlgObject.UnlockThread();

           throw new ServletException(e); // Re-throw to log an error
       }

       // Unlock just in case the code did not do it due to a programming error.
       GlgObject.UnlockThread();
   }

   /////////////////////////////////////////////////////////////////
   // Main servlet's method: everything is handled here.
   /////////////////////////////////////////////////////////////////
   // Supported actions (action parameter):
   //    GetImage - generates image of the monitored process
   //    ProcessEvent - processes object selection on MouseClick or
   //                   Tooltip event types.
   //    GetDialogData - returns data for requested dialogs.
   /////////////////////////////////////////////////////////////////
   public void doGet2(HttpServletRequest request,
       HttpServletResponse response) {
       InitGLG(); // Init the Toolkit

       String action = GetStringParameter(request, "action", "GetImage");
       if (action.equals("GetDialogData")) {
           // Simply return requested dialog data: no drawing required.
           ProcessDialogData(request, response);
           return;
       }

       // The rest of actions (GetImage, ProcessEvent) require a drawing -
       // load it (if first time) and update with data.

       // Get requested width/height of the image: need for all other actions.
       int width = GetIntegerParameter(request, "width", 800);
       int height = GetIntegerParameter(request, "height", 600);
       int devId = GetIntegerParameter(request, "devId", -1);
       int classId = GetIntegerParameter(request, "classId", -1);
       if(devId<0 || classId<0 ) return;
       String strId = (classId == 4) ? site + "_" + Integer.toString(devId) : Integer.toString(classId);

       if (connection==null){
    	   OpenConnection();
       }
       
       
       // Limit max. size to avoid running out of heap space creating an image.
       //if( width > 1000 ) width = 1000;
       //if( height > 1000 ) height = 1000;

       // This example reuses the same drawing between all servlets' threads.
       // Therefore lock to synchronize and prevent other servlets from
       // changing the drawing size, etc., before we are done.
       GlgObject.Lock();

       // Load the drawing just once and share it between all servlets and
       // threads. Alternatively, each servlet may load its own drawing.
       //
       if (!lstIds.contains(strId)) {
           lstViewport.add(LoadDrawing("/drawings/" + strId + ".g"));
           lstIds.add(strId);
           viewportIndex = lstIds.indexOf(strId);
           lstViewport.get(viewportIndex).SetImageSize(width, height);
           lstViewport.get(viewportIndex).SetupHierarchy(); // Setup to prepare to receive data
           GlgObject res_list = lstViewport.get(viewportIndex).CreateTagList(true);
	       int size = res_list.GetSize();
	       List < String > tags = new ArrayList < String > ();
	       List < String > tagsType = new ArrayList < String > ();
	       for( int i=0; i<size; ++i )
	       {
	          GlgObject object = (GlgObject) res_list.GetElement( i );
	          tags.add(object.GetSResource( "TagSource" ));
	          tagsType.add(Integer.toString(object.GetDataType()).trim());
	       }
	       lstTags.add(tags);
	       lstTagsType.add(tagsType);
       } else {
           viewportIndex = lstIds.indexOf(strId);
           lstViewport.get(viewportIndex).SetImageSize(width, height);
       }
       //Log("Type: " + strId + "  index:" + viewportIndex + " vp size: " + lstTags.size() + " id size: " + lstTags.size() +" ta size: " + lstTags.size() +" tae size: " + lstTagsType.size());
       
      
       //ShowPipes( request );      // Show pipes and flow lines if requested.

       UpdateDrawingWithData(devId); // Updates drawing with current data.

       // Setup after data update to prepare to generate image.
       lstViewport.get(viewportIndex).SetupHierarchy();

       // Main action: Generate Image.
       if (action.equals("GetImage")) {
           // Create an image of the viewport's graphics.
           BufferedImage image = null;
           image = (BufferedImage)lstViewport.get(viewportIndex).CreateImage(null);

           if (image != null) {
               GlgObject.Unlock();
               // Write the image
               try {
                   response.setContentType("image/png");
                   OutputStream out_stream = response.getOutputStream();
                   BufferedImage out = image.getSubimage(0, 0, image.getWidth(), image.getHeight() - 20);
                   ImageIO.write(out, "png", out_stream);
                   out_stream.close();
               } catch (IOException e) {
                   // Log( "Aborted writing of image file." );
               }
           }
       }
       // Secondary action: ProcessEvent.
       else if (action.equals("ProcessEvent")) {
           String selection_info = null;

           String event_type = GetStringParameter(request, "event_type", "");

           int selection_type;
           if (event_type.equals("MouseClick")) // Get selected object
           {
               // Find object with the MouseClick custom event.
               selection_type = GlgObject.CLICK_SELECTION;
           } else if (event_type.equals("Tooltip")) // Get object's tooltip
           {
               // Find object with the TooltipString property.
               selection_type = GlgObject.TOOLTIP_SELECTION;
           } else {
               selection_type = 0;
               selection_info = "Unsupported event_type";
           }

           if (selection_type != 0) {
               // Get x and y coordinates of the mouse click.
               int x = GetIntegerParameter(request, "x", -1);
               int y = GetIntegerParameter(request, "y", -1);

               // Selection rectangle around the mouse click.
               GlgCube click_box = new GlgCube();
               int selection_sensitivity = 3; // in pixels
               click_box.p1.x = x - selection_sensitivity;
               click_box.p1.y = y - selection_sensitivity;
               click_box.p2.x = x + selection_sensitivity;
               click_box.p2.y = y + selection_sensitivity;

               // Find selected object with MouseClick custom event attached.
               if (x > 0 && y > 0) {
                   // Select using MouseClick custom events.
                   GlgObject selection_message = null;
                   selection_message = GlgObject.CreateSelectionMessage(lstViewport.get(viewportIndex), click_box, lstViewport.get(viewportIndex), selection_type, 1);
                   if (selection_message != null)
                       switch (selection_type) {
                       default:
                       case GlgObject.CLICK_SELECTION: // Return object name.
                           selection_info =
                               selection_message.GetSResource("Object/Name");
                           break;

                       case GlgObject.TOOLTIP_SELECTION:
                           /* Return tooltip string, which is an event label
                           of the tooltip action message.
                            */
                           selection_info = selection_message.GetSResource("EventLabel");
                           break;
                       }
               } else
                   selection_info = "Invalid x/y coordinates.";
           }

           GlgObject.Unlock();

           WriteAsPlainText(response,
               selection_info == null ? "None" : selection_info);
       } else {
           Log("Unsupported action!");
           GlgObject.Unlock();
       }
   }

   /////////////////////////////////////////////////////////////////
   // Helper methods
   /////////////////////////////////////////////////////////////////

   void OpenConnection(){
	   try {
		   Properties prop = new Properties();
		   InputStream inputStream = this.getClass().getClassLoader().getResourceAsStream("/" + site + "_db.properties");
		   prop.load(inputStream);
		   String driver = prop.getProperty("driver");
	       String url = prop.getProperty("url");
	       String user = prop.getProperty("user");
	       String password = prop.getProperty("password");
	       Class.forName(driver);
			connection = DriverManager.getConnection(url, user, password);
		} catch (IOException e) {
			e.printStackTrace();
		} catch (ClassNotFoundException e) {
	           e.printStackTrace();
	    } catch (SQLException e) {
			e.printStackTrace();
		} 
   }
   
   int GetIntegerParameter(HttpServletRequest request, String name,
       int default_value) {
       String parameter_string = request.getParameter(name);
       if (parameter_string == null || parameter_string.equals(""))
           return default_value;

       try {
           return Integer.parseInt(parameter_string);
       } catch (NumberFormatException e) {
           Log("Invalid parameter value for: " + name +
               " = " + parameter_string);
           return default_value;
       }
   }

   String GetStringParameter(HttpServletRequest request, String name,
       String default_value) {
       String parameter_string = request.getParameter(name);
       if (parameter_string == null)
           return default_value;
       else
           return parameter_string;
   }

   /////////////////////////////////////////////////////////////////
   void Log(String msg) {
       getServletContext().log("GlgProcessServlet: " + msg);
   }

   // GlgErrorHandler interface method for error handling.
   public void Error(String message, int error_type, Exception e) {
       Log(message); // Log errors

       Log(GlgObject.GetStackTraceAsString()); // Print stack
   }

   /////////////////////////////////////////////////////////////////
   void WriteAsPlainText(HttpServletResponse response, String string) {
       try {
           response.setContentType("text/plain");
           PrintWriter out_stream =
               new PrintWriter(response.getOutputStream());
           out_stream.write(string);
           out_stream.close();
       } catch (IOException e) {
           // Log( "Aborted writing of text responce." );
       }
   }

   /////////////////////////////////////////////////////////////////
   void InitGLG() {
       // Set an error handler to log errors.
       GlgObject.SetErrorHandler(this);

       GlgObject.Init(); // Init GlgToolkit
   }

   /////////////////////////////////////////////////////////////////
   GlgObject LoadDrawing(String drawing_name) {
       GlgObject drawing;

       // Get drawing URL relative to the servlet's directory.
       URL drawing_url = null;
       try {
           drawing_url =
               getServletConfig().getServletContext().getResource(drawing_name);
       } catch (MalformedURLException e) {
           Log("Malformed URL: " + drawing_name);
           return null;
       }

       if (drawing_url == null) {
           Log("Can't find drawing: " + drawing_name);
           return null;
       }

       // Load drawing from the URL
       drawing = GlgObject.LoadWidget(drawing_url.toString(), GlgObject.URL);
       if (drawing == null) {
           Log("Can't load drawing: " + drawing_name);
           return null;
       }

       // Disable viewport border in the image: let html define it if needed.
       drawing.SetDResource("LineWidth", 0.);

       return drawing;
   }

   //////////////////////////////////////////////////////////////////////////
   // Updates drawing state with data.
   //////////////////////////////////////////////////////////////////////////
   void UpdateDrawingWithData(int devId) {
          UpdateProcessTags(devId);
   }

   //////////////////////////////////////////////////////////////////////////
   // Updates drawing using resources.
   //////////////////////////////////////////////////////////////////////////
   public void UpdateProcessTags(int devId) {
	   
       try {
           Statement statement = connection.createStatement();
           ResultSet rs = statement.executeQuery("SELECT e.nombre AS e_name, v.id AS id_var, v.nombre AS tag, r.valor AS value, r.fecha AS dt "
                    + " FROM equipo_variable ev JOIN equipo e ON e.id=ev.id_equipo  "
                    + " JOIN variable v ON ev.id_variable=v.id  "
                    + " JOIN registro r ON e.id=r.id_equipo AND v.id = r.id_variable AND r.fecha = e.ultima_fecha "
                    + " WHERE e.id=" + devId + " ORDER BY r.fecha ASC");
           String dt = "";
           String name = "";
           GlgObject chart_vp = null;
           GlgObject chart = null;
           GlgObject plot_array = null;
           if (lstViewport.get(viewportIndex).HasResourceObject("CHART_VP")) {
               chart_vp = lstViewport.get(viewportIndex).GetResourceObject("CHART_VP");
               if (chart_vp.HasResourceObject("CHART")) {
                   chart = chart_vp.GetResourceObject("CHART");
                   if (chart.HasResourceObject("Plots")) plot_array = chart.GetResourceObject("Plots");
               }
           }
           double min_value = 9999999999.9;
           double max_value = -9999999999.9;
	       for( int i=0; i<lstTags.get(viewportIndex).size(); i++ )
	       {
    		   if(lstTagsType.get(viewportIndex).get(i).equalsIgnoreCase("1")) lstViewport.get(viewportIndex).SetSTag(lstTags.get(viewportIndex).get(i), "", true);
    		   else if(lstTagsType.get(viewportIndex).get(i).equalsIgnoreCase("2"))
    			   {
    			   		lstViewport.get(viewportIndex).SetDTag(lstTags.get(viewportIndex).get(i), -1.0, true);
    			   		if(plot_array != null) {
    			   			GlgObject plot = plot_array.GetNamedObject(lstTags.get(viewportIndex).get(i));
    			   			if (plot != null) plot.ClearDataBuffer(null);
    			   		}
    			   }
	       }
           while (rs.next()) {
           	   if(lstTags.get(viewportIndex).contains(rs.getString("tag"))) lstViewport.get(viewportIndex).SetDTag(rs.getString("tag"), rs.getDouble("value"), true);
               if (dt == "") dt = rs.getString("dt");
               if (name == "") name = rs.getString("e_name");
               if (chart != null) {
                   if (plot_array != null) {
                       GlgObject plot = plot_array.GetNamedObject(rs.getString("tag"));
                       if (plot != null) {
                           Statement statementPlot = connection.createStatement();
                           ResultSet rsPlot = statementPlot.executeQuery("SELECT r.valor AS value, UNIX_TIMESTAMP(r.fecha) AS dt "
                                    + " FROM registro r "
                                    + " JOIN equipo e ON e.id=r.id_equipo "
                                    + " WHERE r.id_equipo=" + devId + " AND r.id_variable=" + rs.getInt("id_var") + " AND r.fecha>=DATE_SUB(e.ultima_fecha, INTERVAL 1 DAY) "
                                    + " ORDER BY r.fecha ASC");
                           if(rsPlot.next()){
	                           do {
	                           	double val=rsPlot.getFloat("value"); 
	                               plot.SetDResource("ValueEntryPoint", val);
	                               plot.SetDResource("TimeEntryPoint", rsPlot.getFloat("dt"));
	                               if (val > max_value) max_value = val;
	                               if (val < min_value) min_value = val;
	                           } while (rsPlot.next());
                           }
                           rsPlot.close();
                           statementPlot.close(); 
                       }
                   }
               }
           }
           rs.close();
           statement.close();
           if (chart != null) {
               if (chart.HasResourceObject("YAxis")) {
               	if(min_value<=max_value){
	                    GlgObject yAxis = chart.GetResourceObject("YAxis");
	                    if (yAxis.HasResourceObject("Low")) yAxis.SetDResource("Low", min_value - (max_value - min_value) * 0.1);
	                    if (yAxis.HasResourceObject("High")) yAxis.SetDResource("High", max_value + (max_value - min_value) * 0.1);
               	}
               }
           }
           if(lstTags.get(viewportIndex).contains("TAG_DT")) lstViewport.get(viewportIndex).SetSTag("TAG_DT", dt, true);
           if(lstTags.get(viewportIndex).contains("TAG_NAME")) lstViewport.get(viewportIndex).SetSTag("TAG_NAME", name, true);
       } 
       catch (SQLException e) {
           e.printStackTrace();
           try {
        	   if(!connection.isClosed()) connection.close();
			} catch (SQLException e1) {
				// TODO Auto-generated catch block
				e1.printStackTrace();
			}
           OpenConnection();
       }
   }

   /////////////////////////////////////////////////////////////////
   // Shows pipes and flow lines if requested.
   // Constraints in the drawing take care of updating associated
   // toggle buttons when the pipe or flow line display state is
   // changed. The last parameter of SetDResource() is set to true
   // to update the drawing only if the resource value gets changed.
   /////////////////////////////////////////////////////////////////
   void ShowPipes(HttpServletRequest request, GlgObject viewport) {
       // Show pipes if requested, default 0.
       int show_pipes = GetIntegerParameter(request, "show_pipes", 0);
       viewport.SetDResource("3DPipes/Visibility", (double)show_pipes, true);

       // Show flow if requested, default 1.
       int show_flow = GetIntegerParameter(request, "show_flow", 1);
       viewport.SetDResource("FlowGroup/Visibility", (double)show_flow, true);
   }

   /////////////////////////////////////////////////////////////////
   // Returns data for a requested dialog.
   /////////////////////////////////////////////////////////////////
   void ProcessDialogData(HttpServletRequest request,
       HttpServletResponse response) {
       /*
       String dialog_type =
       GetStringParameter( request, "dialog_type", "None" );

       if( dialog_type.equals( "Heater" ) ){
       WriteAsPlainText( response,
       "<b>Solvent Heater</b><br>" +
       "Level: " + Format( data.HeaterLevel * 100. ) + " %<br>" +
       "Pressure: " + Format( data.HeaterPressure * 5. ) + " ATM.<br>" +
       "Temperature: " + Format( 50. + data.HeaterTemperature * 100. )
       + " C\u00B0" );
       }
       else if( dialog_type.equals( "WaterSeparator" ) ){
       WriteAsPlainText( response,
       "<b>Water Heater</b><br>" +
       "Level: " + Format( data.WaterLevel * 100. ) + " %<br>" +
       "Temperature: " + Format( 50 + data.CoolingTemperature * 30. )
       + " C\u00B0" );
       }
       else if( dialog_type.equals( "SolventValve" ) ){
       WriteAsPlainText( response,
       "<b>Solvent Valve</b><br>" +
       "Open: " + Format( data.SolventValve * 100. ) + " %" );
       }
       else if( dialog_type.equals( "SteamValve" ) ){
       WriteAsPlainText( response,
       "<b>Steam Valve</b><br>" +
       "Open: " + Format( data.SteamValve * 100. ) + " %" );
       }
       else if( dialog_type.equals( "CoolingValve" ) ){
       WriteAsPlainText( response,
       "<b>Cooling Valve</b><br>" +
       "Open: " + Format( data.CoolingValve * 100. ) + " %" );
       }
       else if( dialog_type.equals( "WaterValve" ) ){
       WriteAsPlainText( response,
       "<b>Water Valve</b><br>" +
       "Open: " + Format( data.WaterValve * 100. ) + " %" );
       }
       else
       WriteAsPlainText( response, "None" );
        */
   }

   String Format(double value) {
       return GlgObject.Printf("%.2f", value);
   }
   
}
