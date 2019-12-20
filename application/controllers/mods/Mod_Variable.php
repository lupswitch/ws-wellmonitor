<?php
class Mod_Variable
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Variable');

    }
    public function variable($ID,$PARAMETERS = "{}")
    {
        $data = $this->CI->Model_Variable->select_variable($ID);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        $arr = array(
            'id'=> ($data[0]['id']!="") ? (int) $data[0]['id'] : "",
            'name'=> ($data[0]['nombre']!="") ? (string) $data[0]['nombre'] : "",
            'description'=> ($data[0]['descripcion']!="") ? (string) $data[0]['descripcion'] : "",
            'graphicable'=> ($data[0]['graficable']!="") ? (int) $data[0]['graficable'] : 0,
            'manual'=> ($data[0]['manual']!="") ? (int) $data[0]['manual'] : 0,
            'manual_type'=> ($data[0]['manual_tipo']!="") ? (int) $data[0]['manual_tipo'] : 0,
            'mobile'=> ($data[0]['mobile']!="") ? (int) $data[0]['mobile'] : 0
        );
        $arrparameters = json_decode($PARAMETERS, true);
        $response      = filter_parameters($arr, $arrparameters);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$response);
    }
    public function variables($FILTROS= "{}",$PARAMETERS="{}",$LIMIT="",$PAGINAR= "{}",$ORDER= "{}")
    {
        $KEYS = array();
        $KEYS['id'] = array('key' => 'id', 'tabla' => true);
        $KEYS['graphicable'] = array('key' => 'graficable', 'tabla' => true);
        $KEYS['manual'] = array('key' => 'manual', 'tabla' => true);
        $KEYS['mobile'] = array('key' => 'mobile', 'tabla' => true);
        $data = $this->CI->Model_Variable->select_variables($FILTROS,$LIMIT,$ORDER,$KEYS);
        $arr = array();
        for ($i=0; $i <count($data) ; $i++) { 
           $arr[] = array(
            'id'=> ($data[$i]['id']!="") ? (int) $data[$i]['id'] : "",
            'name'=> ($data[$i]['nombre']!="") ? (string) $data[$i]['nombre'] : "",
            'description'=> ($data[$i]['descripcion']!="") ? (string) $data[$i]['descripcion'] : "",
            'graphicable'=> ($data[$i]['graficable']!="") ? (int) $data[$i]['graficable'] : 0,
            'manual'=> ($data[$i]['manual']!="") ? (int) $data[$i]['manual'] : 0,
            'manual_type'=> ($data[$i]['manual_tipo']!="") ? (int) $data[$i]['manual_tipo'] : 0,
            'mobile'=> ($data[$i]['mobile']!="") ? (int) $data[$i]['mobile'] :0
           );
        }
        $arrparameters = json_decode($PARAMETERS, true);
        $response = filter_parameters($arr, $arrparameters);
        $arr = page_data($response, json_decode($PAGINAR, true));
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' => $arr);
    }
    public function objVar()
    {
      $data = $this->CI->Model_Variable->select_variables("{}","","",array());
      $arr = array();
      for ($i=0; $i <count($data) ; $i++) { 
        $arr[$data[$i]['id']]=array(
          'id'=> ($data[$i]['id']!="") ? (int) $data[$i]['id'] : "",
          'name'=> ($data[$i]['nombre']!="") ? (string) $data[$i]['nombre'] : "",
          'description'=> ($data[$i]['descripcion']!="") ? (string) $data[$i]['descripcion'] : "",
          'graphicable'=> ($data[$i]['graficable']!="") ? (int) $data[$i]['graficable'] : 0,
          'manual'=> ($data[$i]['manual']!="") ? (int) $data[$i]['manual'] : 0,
          'manual_type'=> ($data[$i]['manual_tipo']!="") ? (int) $data[$i]['manual_tipo'] : 0,
          'mobile'=> ($data[$i]['mobile']!="") ? (int) $data[$i]['mobile'] :0
         );
      }
      return $arr;
    }
    public function set_variable($name,$graphicable,$manual,$manual_type,$mobile,$description)
    {
      $id = $this->CI->Model_Variable->insert_variable($name,$graphicable,$manual,$manual_type,$mobile,$description);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->variable($id,"{}");
    }
    public function put_variable($id,$name,$graphicable,$manual,$manual_type,$mobile,$description)
    {
      $id = $this->CI->Model_Variable->update_variable($id,$name,$graphicable,$manual,$manual_type,$mobile,$description);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->variable($id,"{}");
    }
    public function delete_variable($id)
    {
        $data = $this->CI->Model_Variable->delete_variable($id);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->variables("{}","{}","","{}","{}");
    } 
    public function getVarInfo($eId,$plotFlag=false) {
        $varInfo = $this->CI->Model_Variable->get_info_var($eId,$plotFlag);
        $countVarInfo = count($varInfo);
        $defaultCount = 0;
        // marcar las variables x defecto en caso que no haya vista definida
        $defaultVarIds = Array();
        //(K_RUN_STATUS_VAR_ID,K_PIP_VAR_ID,K_MOTOR_TEMP_VAR_ID,K_CABINET_TEMP_VAR_ID);
        // Array(varId,color,left,min,max)
        $defaultVarIds[] = Array(K_RUN_FRECUENCY_VAR_ID,'#99CC00',true,0, 130);
        $defaultVarIds[] = Array(K_PIP_VAR_ID,'#FF0000',true,0,4000);
        $defaultVarIds[] = Array(K_MOTOR_TEMP_VAR_ID,'#0000FF',false,150, 400);
        $defaultVarIds[] = Array(K_MOTOR_CURR_VAR_ID,'#FFCC00',false,0, 100);
        $defaultVarIds[] = Array(K_VIBX_VAR_ID,'#00FFFF',false,0,10);
        $countDefaultVarIds = count($defaultVarIds);
        $arrIds = array();
        for ($i=0; $i<$countVarInfo; $i++) {
          $varInfo[$i]['default'] = false;
          $varInfo[$i]['color'] = g_colorFromTable($i);
          $varInfo[$i]['left']  = ($i%2==0);
          $varInfo[$i]['min']   = "";
          $varInfo[$i]['max']   = "";
          $varInfo[$i]['range'] = false;
          $vId = $varInfo[$i]['vId'];
          for ($j=0; $j<$countDefaultVarIds; $j++) {
            if ( $vId==$defaultVarIds[$j][0] ) {
              $arrIds[]['vId' . $vId] = "defaultVarIds -".$defaultVarIds[$j][0]." vid -". $vId;
              $varInfo[$i]['default'] = true;
              $varInfo[$i]['color'] = $defaultVarIds[$j][1];
              $varInfo[$i]['left']  = $defaultVarIds[$j][2];
              $varInfo[$i]['min']   = $defaultVarIds[$j][3];
              $varInfo[$i]['max']   = $defaultVarIds[$j][4];
              $varInfo[$i]['range'] = true;
              $defaultCount++;
              break;
            }
          }
        }
        // Si aun NO hay x defecto -> adicionar las 2 primeras
        if ( $defaultCount==0 ) {
          if ( $countVarInfo>0 ) {
            $varInfo[0]['default'] = true;
          }
          if ( $countVarInfo>1 ) {
            $varInfo[1]['default'] = true;
          }
        }
        return $varInfo;
      } 
      public function get_var_limits($eId,$varId) {
        $res = $this->CI->Model_Variable->get_var_limits($eId,$varId);
        if(count($res)>0)
        {
          $varLimits['min'] = $res[0]['minimo'];
          $varLimits['low'] = $res[0]['minimo_int']>0? $res[0]['minimo_int'] : $res[0]['minimo']+($res[0]['maximo']-$res[0]['minimo'])/3 ;
          $varLimits['high'] = $res[0]['maximo_int']>0? $res[0]['maximo_int'] : $res[0]['maximo']-($res[0]['maximo']-$res[0]['minimo'])/3;
          $varLimits['max'] = $res[0]['maximo'];
          switch ($res[0]['id_rango']) {
            case 1:
               $varLimits['color_low']="#6baa01";
               $varLimits['color_normal']="#ffbf00";
               $varLimits['color_high']="#e44a00";           
                break;
            case 2:
               $varLimits['color_low']="#e44a00";
               $varLimits['color_normal']="#ffbf00";
               $varLimits['color_high']="#6baa01";
                break;
            default:
              $varLimits['color_low']="#e44a00";
              $varLimits['color_normal']="#6baa01";
              $varLimits['color_high']="#e44a00";         
          }      
        }
        else
        {
          $varLimits['min'] = 0;
          $varLimits['low'] = 30 ;
          $varLimits['high'] = 70;
          $varLimits['max'] = 100;    
          $varLimits['color_low']="#e44a00";
          $varLimits['color_normal']="#6baa01";
          $varLimits['color_high']="#e44a00";     
        }
        return $varLimits;
      }   
}
