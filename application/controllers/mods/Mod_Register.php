<?php
class Mod_Register
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Global');

    }
    public function getLastData($eId, $eDate, $vId)
    {
        if (K_USE_RAND_VALUES) {
            return Array('vId'=>$vId,'date'=>date('Y-m-d H:i:s'),'value'=>($vId==K_RUN_STATUS_VAR_ID? ((date('s')&1)? 1:0):rand(10,90)));
          } else {
            // verifiar que la variable este asociada al equipo
            // Si no buscaria en TODA la Db para no encontrar despues de 2 dias jeje
            $result = $this->CI->Model_Global->sql_lasdata($eId,$vId);
            if ( count($result)>0 ) {
              $result= $this->CI->Model_Global->patch_select_global('registro',array('id_equipo'=>$eId,'id_variable'=>$vId,'fecha'=>"'".$eDate."'"),"fecha AS date,valor AS value");
              if ( count($result)>0 )
                return $result[0];
            }
            return Array('date'=>' ','value'=>' ');
          }
    }
    public function getDataAvg($eId,$d1,$d2,$vId) 
    {
        $result = $this->CI->Model_Global->sql_getdataAvg($eId,$vId,$d1,$d2);
        if (count($result)>0 )
        {
            return Array('date'=>' ','value'=>number_format($result[0]['avg'],2));	
        }  
        else return Array('date'=>' ','value'=>' ');	
    }
    public function getData($eId,$d1,$d2,$vIds,$delta,$init=0,$limit=0,$asc=true, $count=0,$source=0,$low=0) {
        $sqlV="";
        $idVars="";
        $countVars=count($vIds);
        for ($i=0; $i<$countVars; $i++) {
          $vId = $vIds[$i];
          if ( $vId>0 || $vId <=-50 )
          {
            $sqlV=$sqlV.",max(case when id_variable=$vId then valor_texto end) `v".$vId."`";
            if($idVars=="") $idVars=$vId;
            else $idVars=$idVars.",".$vId;
          }
        }
        if($idVars=="") $idVars="-9999";
        $order=" ORDER BY fecha ".($asc? "ASC":"DESC");
        return $this->CI->Model_Global->sql_getdata($source,$sqlV,$low,$eId,$d1,$d2,$idVars,$order,$delta,$count,$limit,$init);
      }
  public function getDataStatsSmall($eId,$d1,$d2,$vIds) 
  {
    $res = Array(); 
    $result = $this->CI->Model_Global->sql_getDataStatsSmall($vIds,$eId,$d1,$d2);
    for($i=0; $i<count($result); $i++)
    {
      if (count($result)>0 )
      {
        $res[$i]['min']=$result[0]['low'];
        $res[$i]['max']=$result[0]['high'];
        $res[$i]['avg']=$result[0]['avg'];
      }    
      else
      {
        $res[$i]['min']='';
        $res[$i]['max']='';
        $res[$i]['avg']='';
      } 
    }        
    return $res;
  }
  public function getLastDataVars($eId,$vIds) 
  {
    $res = Array(); 
    for($i=0; $i<count($vIds); $i++)
    {
      $result= $this->CI->Model_Global->patch_select_global('registro',array('id_equipo'=>$eId,'id_variable'=>$vIds[$i]),"MAX(fecha) AS fecha");
	  if (count($result)>0 )
	  {
      $fecha = $result[0]['fecha'];
      $result= $this->CI->Model_Global->patch_select_global('registro',array('id_equipo'=>$eId,'id_variable'=>$vIds[$i],'fecha'=>"'".$fecha."'"),"fecha,valor");
		  if (count($result)>0 )
		  {
			$res[$i]['date']=$result[0]['fecha'];
			$res[$i]['value']=$result[0]['valor'];
		  }    
		  else
		  {
			$res[$i]['date']='';
			$res[$i]['value']='';
		  }
	  }		  
	  else
	  {
		$res[$i]['date']='';
		$res[$i]['value']='';
	  } 
    }        
    return $res;
  }
  public function getLastDataFoundEMAS($eId) {
    $res= $this->CI->Model_Global->patch_select_global('equipo',array('id'=>$eId),"ultima_fecha, id_clase, id_campo, id_locacion");
      if (count($res)>0 )
      {
        $fecha = $res[0]['ultima_fecha'];
        $id_clase = $res[0]['id_clase'];
        $id_campo = $res[0]['id_campo'];
        $id_locacion = $res[0]['id_locacion'];
        return $this->CI->Model_Global->sql_getLastDataFoundEMAS($id_clase,$id_campo,$id_locacion,$fecha);
      }
      else return Array();
  }
      
}
