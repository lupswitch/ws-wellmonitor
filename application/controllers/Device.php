<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Device extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index_get($id=0){
        if (!$this->get('token') || $this->get('token') != "")
            die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if (!ValidateToken($this->get('token')))
            die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));    
        if(!empty($id)){
            if (!is_number($id))
                die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
            $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')) : "{}";
            $this->response((new Mod_Device())->device($id, $parameters), REST_Controller::HTTP_OK);
        }else{
            $filtros    = ($this->get('filtros') && $this->get('filtros')!="") ? urldecode($this->get('filtros')) : "{}";
            $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')): "{}";
            $limit      = ($this->get('limit') && $this->get('limit')!="") && is_number($this->get('limit')) ? $this->get('limit'): "";
            $paginar    = ($this->get('page') && $this->get('page')!="") ? urldecode($this->get('page')): "{}";
            $order      = ($this->get('order') && $this->get('order')!="") ? urldecode($this->get('order')) : "{}";
            $this->response((new Mod_Device())->devices($filtros, $parameters, $limit, $paginar, $order), REST_Controller::HTTP_OK);
        }
    }
    public function index_post()
    {
        if (
            !$this->post('id_client') || $this->post('id_client') =="" ||
            !$this->post('id_field') || $this->post('id_field') =="" ||
            !$this->post('id_cluster') || $this->post('id_cluster') =="" ||
            !$this->post('id_class') || $this->post('id_class') =="" ||
            !$this->post('name') || $this->post('name') =="" ||
            !$this->post('pod') || $this->post('pod') =="" ||
            !$this->post('id_type') || $this->post('id_type') =="" ||
            !$this->post('modbus_tipo') || $this->post('modbus_tipo') =="" ||
            !$this->post('modbus_id') || $this->post('modbus_id') =="" ||
            !$this->post('tcp_ip') || $this->post('tcp_ip') =="" ||
            !$this->post('tcp_puerto') || $this->post('tcp_puerto') =="" ||
            !$this->post('token')  || $this->post('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if(!ValidateToken($this->post('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $name = string_secure($this->post('name')); 
        $id_client = (int)$this->post('id_client'); 
        $id_field = (int)$this->post('id_field'); 
        $id_cluster = (int)$this->post('id_cluster');
        $id_class = (int) $this->post('id_class'); 
        $pod = $this->post('pod');
        $id_type = (int)$this->post('id_type');  
        $description = ($this->post('description') && $this->post('description')!="") ? string_secure($this->post('description')) : NULL;
        $manufacturer = ($this->post('manufacturer') && $this->post('manufacturer')!="") ? string_secure($this->post('manufacturer')) : NULL;
        $referencia = ($this->post('referencia') && $this->post('referencia')!="") ? string_secure($this->post('referencia')) : NULL;
        $modbus_tipo = (int)$this->post('modbus_tipo');
        $modbus_id = (int)$this->post('modbus_id');
        $tcp_ip = string_secure($this->post('tcp_ip'));
        $tcp_puerto = (int)$this->post('tcp_puerto');
        $latitude = ($this->post('latitude') && $this->post('latitude')!="") ? floatval ($this->post('latitude')) : 0;
        $longitude = ($this->post('longitude') && $this->post('longitude')!="") ? floatval ($this->post('longitude')) : 0;
        $id_varencendido = ($this->post('id_varencendido') && $this->post('id_varencendido')!="") ? $this->post('id_varencendido') : -1;
        $id_varfalla = ($this->post('id_varfalla') && $this->post('id_varfalla')!="") ? $this->post('id_varfalla') : -1;
        $id_varscada_start = ($this->post('id_varscada_start') && $this->post('id_varscada_start')!="") ? $this->post('id_varscada_start') : -1;
        $id_varscada_stop = ($this->post('id_varscada_stop') && $this->post('id_varscada_stop')!="") ? $this->post('id_varscada_stop'): -1;
        $id_varfrecuencia = ($this->post('id_varfrecuencia') && $this->post('id_varfrecuencia')!="") ? $this->post('id_varfrecuencia') : -1;
        $id_varfrecuencia_ref = ($this->post('id_varfrecuencia_ref') && $this->post('id_varfrecuencia_ref')!="") ? $this->post('id_varfrecuencia_ref'): -1;
        $id_varcorriente = ($this->post('id_varcorriente') && $this->post('id_varcorriente')!="") ?$this->post('id_varcorriente') :-1;
        $id_varvoltaje = ($this->post('id_varvoltaje') && $this->post('id_varvoltaje')!="") ? $this->post('id_varvoltaje') : -1;
        $id_varmotortemp = ($this->post('id_varmotortemp') && $this->post('id_varmotortemp')!="") ? $this->post('id_varmotortemp') : -1;
        $id_varpip = ($this->post('id_varpip') && $this->post('id_varpip')!="") ? $this->post('id_varpip') : "";
        $id_varvibracion = ($this->post('id_varvibracion') && $this->post('id_varvibracion')!="") ? $this->post('id_varvibracion') : -1;
        $conection_alerts = ($this->post('conection_alerts') && $this->post('conection_alerts')!="") ? $this->post('conection_alerts') : 0;
        $alert_sound = ($this->post('alert_sound') && $this->post('alert_sound')!="") ? $this->post('alert_sound') : 1;
        $energy_skid = ($this->post('energy_skid') && $this->post('energy_skid')!="") ? $this->post('energy_skid') : 0;
        $replica = ($this->post('replica') && $this->post('replica')!="") ? $this->post('replica') : 0;
        $active = ($this->post('activo') && $this->post('activo')!="") ? $this->post('activo') : 1;
        $id_father = ($this->post('id_father') && $this->post('id_father')!="") ? $this->post('id_father') : -1;
        $this->response((new Mod_Device())->set_device($name,$id_client,$id_field,$id_cluster,$id_class,$pod,$id_type,$description,$manufacturer,$referencia,$modbus_tipo,$modbus_id,$tcp_ip,$tcp_puerto,$latitude,$longitude,$id_varencendido,$id_varfalla,$id_varscada_start,$id_varscada_stop,$id_varfrecuencia,$id_varfrecuencia_ref,$id_varcorriente,$id_varvoltaje,$id_varmotortemp,$id_varpip,$id_varvibracion,$conection_alerts,$alert_sound,$energy_skid,$replica,$active,
        $id_father), REST_Controller::HTTP_OK);
    }
    public function index_put($id)
    {
        if (
            !$this->put('id_client') || $this->put('id_client') =="" ||
            !$this->put('id_field') || $this->put('id_field') =="" ||
            !$this->put('id_cluster') || $this->put('id_cluster') =="" ||
            !$this->put('id_class') || $this->put('id_class') =="" ||
            !$this->put('name') || $this->put('name') =="" ||
            !$this->put('pod') || $this->put('pod') =="" ||
            !$this->put('id_type') || $this->put('id_type') =="" ||
            !$this->put('modbus_tipo') || $this->put('modbus_tipo') =="" ||
            !$this->put('modbus_id') || $this->put('modbus_id') =="" ||
            !$this->put('tcp_ip') || $this->put('tcp_ip') =="" ||
            !$this->put('tcp_puerto') || $this->put('tcp_puerto') =="" ||
            !$this->put('token')  || $this->put('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if(!ValidateToken($this->put('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $name = string_secure($this->put('name')); 
        $id_client = (int)$this->put('id_client'); 
        $id_field = (int)$this->put('id_field'); 
        $id_cluster = (int)$this->put('id_cluster');
        $id_class = (int) $this->put('id_class'); 
        $pod = $this->put('pod');
        $id_type = (int)$this->put('id_type');  
        $description = ($this->put('description') && $this->put('description')!="") ? string_secure($this->put('description')) : NULL;
        $manufacturer = ($this->put('manufacturer') && $this->put('manufacturer')!="") ? string_secure($this->put('manufacturer')) : NULL;
        $referencia = ($this->put('referencia') && $this->put('referencia')!="") ? string_secure($this->put('referencia')) : NULL;
        $modbus_tipo = (int)$this->put('modbus_tipo');
        $modbus_id = (int)$this->put('modbus_id');
        $tcp_ip = string_secure($this->put('tcp_ip'));
        $tcp_puerto = (int)$this->put('tcp_puerto');
        $latitude = ($this->put('latitude') && $this->put('latitude')!="") ? floatval ($this->put('latitude')) : 0;
        $longitude = ($this->put('longitude') && $this->put('longitude')!="") ? floatval ($this->put('longitude')) : 0;
        $id_varencendido = ($this->put('id_varencendido') && $this->put('id_varencendido')!="") ? $this->put('id_varencendido') : -1;
        $id_varfalla = ($this->put('id_varfalla') && $this->put('id_varfalla')!="") ? $this->put('id_varfalla') : -1;
        $id_varscada_start = ($this->put('id_varscada_start') && $this->put('id_varscada_start')!="") ? $this->put('id_varscada_start') : -1;
        $id_varscada_stop = ($this->put('id_varscada_stop') && $this->put('id_varscada_stop')!="") ? $this->put('id_varscada_stop'): -1;
        $id_varfrecuencia = ($this->put('id_varfrecuencia') && $this->put('id_varfrecuencia')!="") ? $this->put('id_varfrecuencia') : -1;
        $id_varfrecuencia_ref = ($this->put('id_varfrecuencia_ref') && $this->put('id_varfrecuencia_ref')!="") ? $this->put('id_varfrecuencia_ref'): -1;
        $id_varcorriente = ($this->put('id_varcorriente') && $this->put('id_varcorriente')!="") ?$this->put('id_varcorriente') :-1;
        $id_varvoltaje = ($this->put('id_varvoltaje') && $this->put('id_varvoltaje')!="") ? $this->put('id_varvoltaje') : -1;
        $id_varmotortemp = ($this->put('id_varmotortemp') && $this->put('id_varmotortemp')!="") ? $this->put('id_varmotortemp') : -1;
        $id_varpip = ($this->put('id_varpip') && $this->put('id_varpip')!="") ? $this->put('id_varpip') : "";
        $id_varvibracion = ($this->put('id_varvibracion') && $this->put('id_varvibracion')!="") ? $this->put('id_varvibracion') : -1;
        $conection_alerts = ($this->put('conection_alerts') && $this->put('conection_alerts')!="") ? $this->put('conection_alerts') : 0;
        $alert_sound = ($this->put('alert_sound') && $this->put('alert_sound')!="") ? $this->put('alert_sound') : 1;
        $energy_skid = ($this->put('energy_skid') && $this->put('energy_skid')!="") ? $this->put('energy_skid') : 0;
        $replica = ($this->put('replica') && $this->put('replica')!="") ? $this->put('replica') : 0;
        $active = ($this->put('active') && $this->put('active')!="") ? $this->put('active') : 1;
        $id_father = ($this->put('id_father') && $this->put('id_father')!="") ? $this->put('id_father') : -1;
        $this->response((new Mod_Device())->update_device($id,$name,$id_client,$id_field,$id_cluster,$id_class,$pod,$id_type,$description,$manufacturer,$referencia,$modbus_tipo,$modbus_id,$tcp_ip,$tcp_puerto,$latitude,$longitude,$id_varencendido,$id_varfalla,$id_varscada_start,$id_varscada_stop,$id_varfrecuencia,$id_varfrecuencia_ref,$id_varcorriente,$id_varvoltaje,$id_varmotortemp,$id_varpip,$id_varvibracion,$conection_alerts,$alert_sound,$energy_skid,$replica,$active,
        $id_father), REST_Controller::HTTP_OK);
    }
    public function index_delete($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (!ValidateToken($this->delete('token')))
            die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $this->response((new Mod_Device())->delete_device($id), REST_Controller::HTTP_OK);
    }
}
