<?php
class Mod_Device_type
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Device_type');
    }
    public function device_type($ID,$PARAMETERS = "{}")
    {
        $data = $this->CI->Model_Device_type->select_device_type($ID);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        $arr = array(
            'id'=> ($data[0]['id']!="") ? (int) $data[0]['id'] : "",
            'name'=> ($data[0]['name']!="") ? (string) $data[0]['name'] : "",
            'description'=> ($data[0]['descripcion']!="") ? (string) $data[0]['descripcion'] : ""
        );
        $arrparameters = json_decode($PARAMETERS, true);
        $response      = filter_parameters($arr, $arrparameters);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$response);
    }
    public function device_types($FILTROS= "{}",$PARAMETERS="{}",$LIMIT="",$PAGINAR= "{}",$ORDER= "{}")
    {
        $KEYS = array();
        $KEYS['id'] = array('key' => 'id', 'tabla' => true);
        $KEYS['name'] = array('key' => 'nombre', 'tabla' => true);
        $KEYS['description'] = array('key' => 'descripcion', 'tabla' => true);
        $data = $this->CI->Model_Device_type->select_device_types($FILTROS,$LIMIT,$ORDER,$KEYS);
        $arr = array();
        for ($i=0; $i <count($data) ; $i++) { 
           $arr[] = array(
            'id'=> ($data[$i]['id']!="") ? (int) $data[$i]['id'] : "",
            'name'=> ($data[$i]['name']!="") ? (string) $data[$i]['name'] : "",
            'description'=> ($data[$i]['descripcion']!="") ? (string) $data[$i]['descripcion'] : ""
           );
        }
        $arrparameters = json_decode($PARAMETERS, true);
        $response = filter_parameters($arr, $arrparameters);
        $arr = page_data($response, json_decode($PAGINAR, true));
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' => $arr);
    }
    public function set_device_types($name,$description)
    {
      $id = $this->CI->Model_Device_type->insert_device_types($name,$description);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->device_type($id,"{}");
    }
    public function put_device_types($id,$name,$description)
    {
      $id = $this->CI->Model_Device_type->update_device_types($id,$name,$description);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->field($id,"{}");
    }
    public function delete_device_types($id)
    {
        $data = $this->CI->Model_Device_type->delete_device_types($id);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->device_types("{}","{}","","{}","{}");
    }     
}
