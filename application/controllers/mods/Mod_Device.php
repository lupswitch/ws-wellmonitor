<?php
class Mod_Device
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Device');
    }
    public function device($ID,$PARAMETERS = "{}")
    {
        $data = $this->CI->Model_Device->select_device($ID);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        $arr = array(
            'id'=> ($data[0]['id']!="") ? (int) $data[0]['id'] : "",
            'id_client'=> ($data[0]['id_cliente']!="") ? (int) $data[0]['id_cliente'] : "",
            'id_field'=> ($data[0]['id_campo']!="") ? (int) $data[0]['id_campo'] : "",
            'id_cluster'=> ($data[0]['id_locacion']!="") ? (int) $data[0]['id_locacion'] : "",
            'id_class'=> ($data[0]['id_clase']!="") ? (int) $data[0]['id_clase'] : "",
            'id_type'=> ($data[0]['id_tipo']!="") ? (int) $data[0]['id_tipo'] : "",
            'name'=> ($data[0]['nombre']!="") ? (string) $data[0]['nombre'] : "",
            'description'=> ($data[0]['description']!="") ? (string) $data[0]['description'] : "",
            'reference'=> ($data[0]['referencia']!="") ? (string) $data[0]['referencia'] : "",
            'pod'=> ($data[0]['pod']!="") ? (string) $data[0]['pod'] : "",
            'manufacturer'=> ($data[0]['fabricante']!="") ? (string) $data[0]['fabricante'] : "",
            'modbus_id'=> ($data[0]['modbus_id']!="") ? (int) $data[0]['modbus_id'] : "",
            'modbus_tipo'=> ($data[0]['modbus_tipo']!="") ? (int) $data[0]['modbus_tipo'] : "",
            'tcp_ip'=> ($data[0]['tcp_ip']!="") ? (string) $data[0]['tcp_ip'] : "",
            'tcp_puerto'=> ($data[0]['tcp_puerto']!="") ? (int) $data[0]['tcp_puerto'] : "",
            'latitude'=> ($data[0]['latitud']!="") ?floatval( $data[0]['latitud']) : 0,
            'longitude'=> ($data[0]['longitud']!="") ? floatval( $data[0]['longitud']) : 0,
            'id_varencendido'=> ($data[0]['id_varencendido']!="") ? (int) $data[0]['id_varencendido'] : -1,
            'id_varfalla'=> ($data[0]['id_varfalla']!="") ? (int) $data[0]['id_varfalla'] : -1,
            'id_varscada_start'=> ($data[0]['id_varscada_start']!="") ? (int) $data[0]['id_varscada_start'] : -1,
            'id_varscada_stop'=> ($data[0]['id_varscada_stop']!="") ? (int) $data[0]['id_varscada_stop'] : -1,
            'id_varcorriente'=> ($data[0]['id_varcorriente']!="") ? (int) $data[0]['id_varcorriente'] : -1,
            'id_varvoltaje'=> ($data[0]['id_varvoltaje']!="") ? (int) $data[0]['id_varvoltaje'] :-1,
            'id_varmotortemp'=> ($data[0]['id_varmotortemp']!="") ? (int) $data[0]['id_varmotortemp'] : -1,
            'id_varpip'=> ($data[0]['id_varpip']!="") ? (int) $data[0]['id_varpip'] : "",
            'id_varvibracion'=> ($data[0]['id_varvibracion']!="") ? (int) $data[0]['id_varvibracion'] :-1,
            'id_varfrecuencia'=> ($data[0]['id_varfrecuencia']!="") ? (int) $data[0]['id_varfrecuencia'] : "",
            'id_varfrecuencia_ref'=> ($data[0]['id_varfrecuencia_ref']!="") ? (int) $data[0]['id_varfrecuencia_ref'] : -1,
            'active'=> ($data[0]['activo']!="") ? (int) $data[0]['activo'] : 1,
            'replica'=> ($data[0]['replica']!="") ? (int) $data[0]['replica'] : 0,
            'id_father'=> ($data[0]['id_padre']!="") ? (int) $data[0]['id_padre'] : -1,
            'energy_skid'=> ($data[0]['energia_skid']!="") ? (int) $data[0]['energia_skid'] : -1,
            'alert_sound'=> ($data[0]['alerta_sonido']!="") ? (int) $data[0]['alerta_sonido'] : -1,
            'conection_alerts'=> ($data[0]['alerta_conexion']!="") ? (int) $data[0]['alerta_conexion'] : -1
        );
        
        $arrparameters = json_decode($PARAMETERS, true);
        $response      = filter_parameters($arr, $arrparameters);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$response);
    }
    public function devices($FILTROS= "{}",$PARAMETERS="{}",$LIMIT="",$PAGINAR= "{}",$ORDER= "{}")
    {
        $KEYS = array();
        $KEYS['id_client'] = array('key' => 'id_cliente', 'tabla' => true);
        $KEYS['id_field'] = array('key' => 'id_campo', 'tabla' => true);
        $data = $this->CI->Model_Device->select_devices($FILTROS,$LIMIT,$ORDER,$KEYS);
        $arr = array();
        for ($i=0; $i <count($data) ; $i++) { 
           $arr[] = array(
            'id'=> ($data[$i]['id']!="") ? (int) $data[$i]['id'] : "",
            'name'=> ($data[$i]['nombre']!="") ? (string) $data[$i]['nombre'] : "",
            'name_client'=> ($data[$i]['cliente']!="") ? (string) $data[$i]['cliente'] : "",
            'name_field'=> ($data[$i]['campo']!="") ? (string) $data[$i]['campo'] : "",
            'name_cluster'=> ($data[$i]['locacion']!="") ? (string) $data[$i]['locacion'] : "",
            'name_type'=> ($data[$i]['tipo']!="") ? (string) $data[$i]['tipo'] : "",
            'tcp_ip'=> ($data[$i]['tcp_ip']!="") ? (string) $data[$i]['tcp_ip'] : "",
            'modbus_id'=> ($data[$i]['modbus_id']!="") ? (int) $data[$i]['modbus_id'] : "",
            'tcp_puerto'=> ($data[$i]['tcp_puerto']!="") ? (int) $data[$i]['tcp_puerto'] : "",
            'pod'=> ($data[$i]['pod']!="") ? (string) $data[$i]['pod'] : "",
            'active_name'=> ($data[$i]['estado_nombre']!="") ? (string) $data[$i]['estado_nombre'] :""
           );
        }
        $arrparameters = json_decode($PARAMETERS, true);
        $response = filter_parameters($arr, $arrparameters);
        $arr = page_data($response, json_decode($PAGINAR, true));
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' => $arr);
    }
    public function set_device($name,$id_client,$id_field,$id_cluster,$id_class,$pod,$id_type,$description,$manufacturer,$referencia,$modbus_tipo,$modbus_id,$tcp_ip,$tcp_puerto,$latitude,$longitude,$id_varencendido,$id_varfalla,$id_varscada_start,$id_varscada_stop,$id_varfrecuencia,$id_varfrecuencia_ref,$id_varcorriente,$id_varvoltaje,$id_varmotortemp,$id_varpip,$id_varvibracion,$conection_alerts,$alert_sound,$energy_skid,$replica,$active,
    $id_father){
      $id = $this->CI->Model_Device->insert_device($name,$id_client,$id_field,$id_cluster,$id_class,$pod,$id_type,$description,$manufacturer,$referencia,$modbus_tipo,$modbus_id,$tcp_ip,$tcp_puerto,$latitude,$longitude,$id_varencendido,$id_varfalla,$id_varscada_start,$id_varscada_stop,$id_varfrecuencia,$id_varfrecuencia_ref,$id_varcorriente,$id_varvoltaje,$id_varmotortemp,$id_varpip,$id_varvibracion,$conection_alerts,$alert_sound,$energy_skid,$replica,$active,
      $id_father);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->device($id,"{}");
    }
    public function put_client($id,$id_field,$name,$description)
    {
      $id = $this->CI->Model_Device->update_device($id,$id_field,$name,$description);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->device($id,"{}");
    }
    public function delete_device($id)
    {
        $data = $this->CI->Model_Device->delete_device($id);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->clusters("{}","{}","","{}","{}");
    }     
}
