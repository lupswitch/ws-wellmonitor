<?php
class Mod_Permission
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Userfield');
        $this->CI->load->model('Model_Device');
        $this->CI->load->model('Model_User');
        $this->CI->load->model('Model_Client');
        
    }
    //verificar si se puede consultar el cliente
    public function canConsultClient($userId,$clientId) {
      $result = $this->CI->Model_User->patch_select_user(array('id'=>$userId),"id_cliente");
        if ( count($result)<=0 ) return false;
        $userIdClient = $result[0]['id_cliente'];
        // si el cliente es cero -> puede consultar TODOS
        if ( $userIdClient==0 ) return true;
        // compara el cliente que puede ver el usuario y el que se consulta
        return ($clientId==$userIdClient);
      }
      public function canConsultField($userId,$fId) {
        return ($fId>0);
        $result = $this->CI->Model_User->patch_select_user(array('id'=>$userId),"id_campo");
        // id de usuario NO encontrado
        if ( count($result)<=0 ) return false;
        $userIdCampo = $result[0]['id_campo'];
        if ( $userIdCampo==-1 )
          return true; // puede consultar TODOS los campos
        // comparar el campo asignado
        if ($userIdCampo==$fId)
          return true;
        // buscar la relacion usuario-campo
        $result = $this->CI->Model_Userfield->patch_select_userfield(array('id_campo'=>$fId,'id_usuario'=>$userId),"permiso",array());
        return ( count($result)>0 );
      }
      
      public function canEditField($userId,$fId) {
        // primero buscar si tiene permiso de escritura en el campo
        $result = $this->CI->Model_Userfield->patch_select_userfield(array('id_campo'=>$fId,'id_usuario'=>$userId),"permiso",array());
        return ( count($result)>0 );
      }
      
      public function canConsultDevice($userId,$eId) {
        return ($eId>0);  //Retorna verdadero , debido a que por ahora el control se hace a nivel de campo
        // busco el campo del equipo
        $result = $this->CI->Model_Device->patch_select_device(array('id'=>$eId),"id_campo","");
        // idEquipo NO encontrado
        if ( count($result)<=0 ) return false;
        // buscar si puede consultar el campo
        $idCampo=$result[0]['id_campo'];
        return $this->canConsultField($userId,$idCampo);
      }
      public function canEditDevice($userId,$eId) {
        // primero buscar si tiene permiso de escritura en el campo
        $result = $this->CI->Model_Userfield->patch_select_userfield(array('usuario_campo.permiso >'=>'1','usuario_campo.id_usuario'=>$userId,'equipo.id'=>(int)$eId),"permiso",array("equipo","equipo.id_campo=usuario_campo.id_campo","left"));
        if(count($result)>0) return true;
        // buscar la relacion usuario_equipo
        $result = $this->CI->Model_Userfield->patch_select_userfield(array('id_campo'=>(int)$eId,'id_usuario'=>$userId,'permiso >'=>'1'),"permiso",array());
        return ( count($result)>0 );
      }
        
}
