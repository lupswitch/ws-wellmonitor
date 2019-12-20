<?php
class Mod_Auditoria
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Auditoria');
    }     
    public function log($userId,$deviceId=-1,$msg) {
        $ip=clientIp();
        $pos=getLatLon4Ip($ip);
        $message= $msg." "." From IP : $ip";
        if ( $pos['lat']!=0 && $pos['lon']!=0 )
          $message = $message." "."lat:".$pos['lat']." lon:".$pos['lon'];
        $userName = (new Mod_User())->user($userId,json_encode(array("username")));
        $arr = array('id_usuario'=>$userId,'fecha'=>SYSDATE(),'id_equipo'=>$deviceId,'nombre'=>$userName,'description'=>$message);
        $this->CI->Model_Auditoria->insert_auditoria($arr);
      }
    public function logLoginNoUser($user) {
        $this->log(-1,-1,"Try login user[$user] NOT in Db");
      }
      public function logLoginUserInactive($id,$user) {
        $this->log($id,-1,"Try login user[$user] INACTIVE in Db");
      }
      public function logLoginOk($id,$user) {
        $this->log($id,-1,"Login user[$user] Ok");
      }
      public function logLoginOkMobile($id) {
        $this->log($id,-1,'Login Ok from mobile');
      }
      public function logLoginNoPass($id,$user) {
        $this->log($id,-1,"Try login user[$user] BAD pass");
      }
      public function logLogout($id,$user) {
        $this->log($id,-1,"Logout user[$user] Ok");
      }
      public function logChangePassWrong($id,$user) {
        $this->log($id,-1,"Try change pass user[$user] pass error");
      }
      public function logChangePassOk($id,$user) {
        $this->log($id,-1,"Change pass user[$user] Ok");
      }
      public function logSendData($userId,$devId) {
        $this->log($userId,$devId,'Manual data saved');
      }
      public function logControlSendCommand($userId,$eId,$idVal,$vVal)
      {
        $eName = (new Mod_Device())->device($eId,json_encode(array("name")));
        $userName = (new Mod_User())->user($userId,json_encode(array("username")));
        $vName = (new Mod_Variable())->variable($idVal,json_encode(array("name")));
        $msg = "User[$userName] modified value for variable[$vName] in device[$eName] to value[$vVal]";
        $this->log($userId,$eId,$msg);
      }
      public function logControlSendCommandRamp($userId,$eId,$idVal,$vValTarget,$vValDeltaF,$vValDeltaT)
      {
        $eName = (new Mod_Device())->device($eId,json_encode(array("name")));
        $userName = (new Mod_User())->user($userId,json_encode(array("username")));
        $vName = (new Mod_Variable())->variable($idVal,json_encode(array("name")));
        $msg = "User[$userName] send ramp for variable[$vName] in device[$eName]. Target value[$vValTarget], Delta value[$vValDeltaF], Delta time[$vValDeltaT] mins";
        $this->log($userId,$eId,$msg);
      }
      public function logControlSendCommandRampStop($userId,$eId,$idVal,$vValTarget,$vValDeltaF,$vValDeltaT)
      {
        $eName = (new Mod_Device())->device($eId,json_encode(array("name")));
        $userName = (new Mod_User())->user($userId,json_encode(array("username")));
        $vName = (new Mod_Variable())->variable($idVal,json_encode(array("name")));
        $msg = "User[$userName] STOP ramp for variable[$vName] in device[$eName]. Target value[$vValTarget], Delta value[$vValDeltaF], Delta time[$vValDeltaT] mins";
        $this->log($userId,$eId,$msg);
      }
      public function LogReport($clientId)
      {
        $userName = (new Mod_client())->client($clientId,json_encode(array("name")));
        $msg = " The daily report was sent to the client [$userName] ";
        $this->log(-1,-1,$msg);
      }
      //Registro y borrado de clientes
      //registro y borrado de usuarios
}
