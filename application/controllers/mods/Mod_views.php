<?php
class Mod_Views
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_View');
        $this->CI->load->model('Model_Variable');
        $this->CI->load->model('Model_Device');
    }
    /** START CONST FOR SETTINGS VIEWS */
    const K_DATA_TYPE = 100; // incluye intervalo al comienzo
    const K_PLOT_TYPE = 101;//2;
    const K_CANVAS_TYPE = 102;//15;
    const K_CONTROL_TYPE = 103;//16;
    const K_FIELD_STATE_TYPE = 104;//17;
    const K_DATA_STATS_TYPE = 105;
    const K_FIELD_STATE_DEVICE_TYPE = 106;
    const ID_VIEW = 1;
    const ETYPE = 1;
    const TYPE = -1;
    /** END CONST FOR SETTINGS VIEWS */
    public function getFieldStatusDevice($userId) 
    {
        $result = $this->CI->Model_View->patch_select_view(array('id_vista'=>self::ID_VIEW,'id_usuario'=>$userId,'id_tipo_equipo'=>self::ETYPE,'tipo'=>self::TYPE),"variables");
        return ( count($result)>0 )?$result[0]['variables']:array();
    }
    public function getFieldStateConfiguration($userId,$fId,$eIds) {
        $varInfo = Array();
        $varInfo[] = Array("vId" => "-1", "vName" => "*[Type]");
        $varInfo[] = Array("vId" => "-2", "vName" => "*[State]");
        $varInfo[] = Array("vId" => "-3", "vName" => "*[Comm. Type]");
        $varInfo[] = array("vId" => "-4", "vName" => "*[Brand]");
        $eIdsCount = count($eIds);
        // info variables de los equipo incluidas las NO graficables
        for ($i=0; $i<$eIdsCount; $i++) {
          $varInfoDevice = (new Mod_Variable())->getVarInfo($eIds[$i],true);
          $varInfoDeviceCount = count($varInfoDevice);
          // adiciono las variables del equipo a la cuenta que llevo en varInfo
          // si las nuevas variables NO estan en varInfo -> adiciono
          for ($j=0; $j<$varInfoDeviceCount; $j++) {
            $found = false;
            $varInfoCount = count($varInfo);
            for ($k=0; $k<$varInfoCount; $k++) {
              if ( $varInfo[$k]['vId']==$varInfoDevice[$j]['vId'] ) {
                $found = true;
                break;
              }
            }
            // si NO se encuentra -> adicionar
            if ( $found==false ) {
              $varInfo[] = $varInfoDevice[$j];
            }
          }
        }
        $varInfoCount = count($varInfo);
        // marcar 'field' a false inicialmente
        for ($j=0; $j<$varInfoCount; $j++) {
          $varInfo[$j]['field'] = false;
        }
        $activeVarCount = 0;
        // vista guardada
        $viewId = self::ID_VIEW;
        $res = $this->CI->Model_Device->patch_select_device(array('id_campo'=>$fId),"id_clase,count(id_clase) as total","",array("id_clase"),"'total','DESC'",1);
        if ( count($res)>0 ) $viewId = $res[0]["id_clase"];
        $eType = -1; //-$fId; // usada como control
        $type = self::K_FIELD_STATE_TYPE;
        // configuracion de la vista para control del usuario y tipo equipo
        $varStr = $this->getDbTableVistaVarStr($userId,$eType,$viewId,$type);
        $varStrValues = Array();
        $varRes = Array();
        if ( strlen($varStr)>0 ) {
          // los IDS definidos en la vista
          $varStrValues=explode(";",$varStr);
          // marcar cuales variables van a mostrarse en sliders
          $varStrValuesCount=count($varStrValues);
          for($i=0; $i<$varStrValuesCount; $i++) {
            $id = $varStrValues[$i];
            // buscar varId en la info de variables y marcar si esta activa
            for ($j=0; $j<$varInfoCount; $j++) {
              if ( $id==$varInfo[$j]['vId'] ) {
                $varInfo[$j]['field'] = true;
                $found = true;
                $activeVarCount++;
                $varRes[] = $varInfo[$j];
                break;
              }
            }
          }
        } else {
          // activar las x defecto
          for ($j=0; $j<$varInfoCount; $j++) {
            if ( $varInfo[$j]['default']==true ) {
              $varInfo[$j]['field'] = true;
              $activeVarCount++;
              $varRes[] = $varInfo[$j];
            }
          }
        }
        for ($j=0; $j<$varInfoCount; $j++) {
            if ( $varInfo[$j]['field']==false ) {
              $varRes[] = $varInfo[$j];
            }
        }
        return $varRes;
      } 
      protected function getDbTableVistaVarStr($userId,$eType,$viewId,$type,$template=0) {
        $result=$this->CI->Model_View->patch_select_view(array('id_vista'=>$viewId,'id_usuario'=>$userId,'id_tipo_equipo'=>$eType,'tipo'=>$type,'template'=>$template),"variables");
        return ( count($result)>0 )?$result[0]['variables']:"";
      }
      public function getTableConfiguration($userId,$eType,$eId,$source=0,$type=self::K_DATA_TYPE) {
        if($source==0) $tmpVarInfo = (new Mod_Variable())->getVarInfo($eId,true);
        else $tmpVarInfo = (new Mod_Variable())->getVarInfo($eId,false);
        $varInfo =array();
        for($i=0;$i<count($tmpVarInfo);$i++) {
          if((int)$tmpVarInfo[$i]['vManual']==(int)$source) $varInfo[]=$tmpVarInfo[$i];
        }
        $varInfoCount = count($varInfo);
        // configuracion de la vista para table del usuario y tipo equipo
        $varStr = $this->getDbTableVistaVarStr($userId,$eType,self::ID_VIEW,$type);
        $varStrValues = Array();
        $varRes = Array();
        if ( strlen($varStr)>0 ) {
          // los IDS definidos en la vista
        if(strpos($varStr,"*") !== false)
        {
          $varStr=substr($varStr,0,strpos($varStr,"*"));
        }
          $varStrValues=explode(";",$varStr);
        }
        $varStrValuesCount = count($varStrValues);
        // Marcar No activas TODAS inicialmente
        for ($i=0; $i<$varInfoCount; $i++) {
          $varInfo[$i]['table'] = false;
        }
        $var2ControlCount = 0;
        // marcar cuales variables van a mostrarse en sliders
        for($i=0; $i<$varStrValuesCount; $i++) {
          $id = $varStrValues[$i];
          // buscar varId en la info de variables y marcar si esta activa
          for ($j=0; $j<$varInfoCount; $j++) {
            if ( $id==$varInfo[$j]['vId'] ) {
              $varInfo[$j]['table'] = true;
              $var2ControlCount++;
           $varRes[] = $varInfo[$j];
              break;
            }
          }
        }
        // verificar que haya al menos 1 marcada -> o marcar las x defecto
        if ( $var2ControlCount==0 ) {
          for ($j=0; $j<$varInfoCount; $j++) {
            if ( $varInfo[$j]['default'] )
        {
              $varInfo[$j]['table'] = true;
          $varRes[] = $varInfo[$j];
        }
          }
        }
        for ($i=0; $i<$varInfoCount; $i++) {
          if($varInfo[$i]['table'] == false) $varRes[] = $varInfo[$i];
        }
        return $varRes;
      }
      public function getControlConfiguration($userId,$eType,$eId) {
        // info variables del equipo incluidas las NO graficables
        $varInfoInit = (new Mod_Variable())->getVarInfo($eId,true);
        $varInfoInitCount = count($varInfoInit);
        // buscar los indices de las variables de control especiales
        $runStatusIndex = -1;
        $runFrecIndex = -1;
        $runFrecRefIndex = -1;
        $varInfo = Array();
      $varRes = Array();
        // marcar no activas TODAS inicialmente
        for ($j=0; $j<$varInfoInitCount; $j++) {
          $varInfoInit[$j]['control'] = false;
          // quitar RUn status de las x defecto
          if ( $varInfoInit[$j]['vId']==$varInfoInit[$j]['varRunStatus'] ) {
            $runStatusIndex = $j;
            continue;
          }
          if ( $varInfoInit[$j]['vId']==$varInfoInit[$j]['varFreq'] ) {
            $runFrecIndex = $j;
            continue;
          }
          if ( $varInfoInit[$j]['vId']==$varInfoInit[$j]['varFreqRef'] ) {
            $runFrecRefIndex = $j;
            continue;
          }
          // esta es para incluir
          $varInfo[] = $varInfoInit[$j];
        }
        
        // definir ids runStatus, runFrec x defecto
        $config['runStatus']['vId'] =  $runStatusIndex!=-1?$varInfoInit[$runStatusIndex]['vId']:K_RUN_STATUS_VAR_ID;
        $config['runStatus']['vName'] = $runStatusIndex!=-1? $varInfoInit[$runStatusIndex]['vName']:"R. STATUS";
        $config['runFrec']['vId'] = $runFrecIndex!=-1?$varInfoInit[$runStatusIndex]['vId']:K_RUN_FRECUENCY_VAR_ID;
        $config['runFrec']['vName'] = $runFrecIndex!=-1? $varInfoInit[$runFrecIndex]['vName']:"R. FREC";
        $config['refFrec']['vId'] = $runFrecRefIndex!=-1?$varInfoInit[$runStatusIndex]['vId']:K_RUN_FRECUENCY_REF_VAR_ID;
        $config['refFrec']['vName'] = ($runFrecRefIndex!=-1? $varInfoInit[$runFrecRefIndex]['vName']:"RUN FREQ. REF.");
        
        $viewId = self::ID_VIEW;
        $type = self::K_CONTROL_TYPE;
        // configuracion de la vista para control del usuario y tipo equipo
        $varStr = $this->getDbTableVistaVarStr($userId,$eType,$viewId,$type);
        $varStrValues = Array();
        if ( strlen($varStr)>0 ) {
          // los IDS definidos en la vista
          $varStrValues=explode(";",$varStr);
        }
        $var2ControlCount = 0;
        $varInfoCount = count($varInfo);
        // marcar cuales variables van a mostrarse en sliders
        $varStrValuesCount=count($varStrValues);
        for($i=0; $i<$varStrValuesCount; $i++) {
          $id = $varStrValues[$i];
          // buscar varId en la info de variables y marcar si esta activa
          for ($j=0; $j<$varInfoCount; $j++) {
            if ( $id==$varInfo[$j]['vId'] ) {
              $varInfo[$j]['control'] = true;
          $varRes[] = $varInfo[$j];
              $var2ControlCount++;
              break;
            }
          }
        }
        // verificar que haya al menos 1 marcada -> o marcar las x defecto
        if ( $var2ControlCount==0 ) {
          for ($j=0; $j<$varInfoCount; $j++) {
            if ( $varInfo[$j]['default'] )
        {
              $varInfo[$j]['control'] = true;
          $varRes[] = $varInfo[$j];
        }
          }
        }
      for ($j=0; $j<$varInfoCount; $j++) {
        if ( $varInfo[$j]['control'] == false ) $varRes[] = $varInfo[$j];
      }
        // adicionar las variables a la configuracion
        $config['varInfo'] = $varRes;
        
        return $config;
      }
}
