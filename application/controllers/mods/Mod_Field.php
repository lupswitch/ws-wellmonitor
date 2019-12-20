<?php
class Mod_Field
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Field');
        $this->CI->load->model('Model_Global');
    }
    public function field($ID,$PARAMETERS = "{}")
    {
        $data = $this->CI->Model_Field->select_field($ID);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        $arr = array(
            'id'=> ($data[0]['id']!="") ? (int) $data[0]['id'] : "",
            'name'=> ($data[0]['name']!="") ? (int) $data[0]['name'] : "",
            'osg'=> ($data[0]['gravedad_especifica']!="") ?  floatval($data[0]['gravedad_especifica']) : 0,
            'description'=> ($data[0]['descripcion']!="") ? (int) $data[0]['descripcion'] : "",
            'map_latitude'=>($data[0]['mapa_lat']!="") ? floatval($data[0]['mapa_lat']) : 0,
            'map_longitude'=> ($data[0]['mapa_lon']!="") ? floatval($data[0]['mapa_lon']) : 0,
            'map_zoom'=> ($data[0]['mapa_zoom']!="") ? (int) $data[0]['mapa_zoom'] : 0,
            'email'=> ($data[0]['correo']!="") ? (string) $data[0]['correo'] : ""
        );
        $arrparameters = json_decode($PARAMETERS, true);
        $response      = filter_parameters($arr, $arrparameters);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$response);
    }
    public function fields($FILTROS= "{}",$PARAMETERS="{}",$LIMIT="",$PAGINAR= "{}",$ORDER= "{}")
    {
        $KEYS = array();
        $KEYS['id'] = array('key' => 'id', 'tabla' => true);
        $KEYS['name'] = array('key' => 'nombre', 'tabla' => true);
        $KEYS['osg'] = array('key' => 'gravedad_especifica', 'tabla' => true);
        $KEYS['description'] = array('key' => 'descripcion', 'tabla' => true);
        $KEYS['map_latitude'] = array('key' => 'mapa_lat', 'tabla' => true);
        $KEYS['map_longitude'] = array('key' => 'mapa_lon', 'tabla' => true);
        $KEYS['map_zoom'] = array('key' => 'mapa_zoom', 'tabla' => true);
        $KEYS['email'] = array('key' => 'correo', 'tabla' => true);
        $data = $this->CI->Model_Field->select_fields($FILTROS,$LIMIT,$ORDER,$KEYS);
        $arr = array();
        for ($i=0; $i <count($data) ; $i++) { 
           $arr[] = array(
            'id'=> ($data[$i]['id']!="") ? (int) $data[$i]['id'] : "",
            'name'=> ($data[$i]['name']!="") ? (int) $data[$i]['name'] : "",
            'osg'=> ($data[$i]['gravedad_especifica']!="") ?  floatval($data[$i]['gravedad_especifica']) : 0,
            'description'=> ($data[$i]['descripcion']!="") ? (int) $data[$i]['descripcion'] : "",
            'map_latitude'=>($data[$i]['mapa_lat']!="") ? floatval($data[$i]['mapa_lat']) : 0,
            'map_longitude'=> ($data[$i]['mapa_lon']!="") ? floatval($data[$i]['mapa_lon']) :0,
            'map_zoom'=> ($data[$i]['mapa_zoom']!="") ? (int) $data[$i]['mapa_zoom'] :0
           );
        }
        $arrparameters = json_decode($PARAMETERS, true);
        $response = filter_parameters($arr, $arrparameters);
        $arr = page_data($response, json_decode($PAGINAR, true));
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' => $arr);
    }
    public function set_field($name,$map_latitude,$map_longitude,$map_zoom ,$email,$osg,$comments)
    {
      $id = $this->CI->Model_Field->insert_field($name,$map_latitude,$map_longitude,$map_zoom ,$email,$osg,$comments);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->field($id,"{}");
    }
    public function put_client($id,$name,$map_latitude,$map_longitude,$map_zoom,$email,$osg,$comments)
    {
      $id = $this->CI->Model_Field->update_field($id,$name,$map_latitude,$map_longitude,$map_zoom,$email,$osg,$comments);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->field($id,"{}");
    }
    public function delete_field($id)
    {
        $data = $this->CI->Model_Field->delete_field($id);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->fields("{}","{}","","{}","{}");
    } 
    public function getFieldInfo($userId,$clientId)
   {
    $fieldInfo = array();
    $fieldInfoAll = $this->CI->Model_Field->sql_device_field($userId,$clientId);
    $fieldId4User = -1;
    $fieldId= $this->CI->Model_Global->patch_select_global('usuario',array('id'=>$userId),"id_campo AS fieldId");
    if (count($fieldId) > 0)
      $fieldId4User = $fieldId[0]['fieldId'];
    $fieldInfoAllCount = count($fieldInfoAll);
    for ($i = 0; $i < $fieldInfoAllCount; $i++) {
      $fieldId = $fieldInfoAll[$i]['f_id'];
      if ($fieldInfoAll[$i]['f_class_permiso'] > 0)
        $permission4User = $fieldInfoAll[$i]['f_permiso'];
      else
	  {
        if ($fieldInfoAll[$i]['f_class_ue_permiso'] > 0)
          $permission4User = $fieldInfoAll[$i]['f_ue_permiso'];
        else  
          $permission4User = 0;
	  }
      if ($fieldId4User == -1 || $fieldId4User == $fieldId || $permission4User > 0) {
        $fieldInfoTemp = $fieldInfoAll[$i];
        if ($userId > 10000)
          $fieldInfoTemp['f_name'] = "Field:$i";
        $fieldInfo[] = $fieldInfoTemp;
      }
    }
    return $fieldInfo;
  }
        
}
