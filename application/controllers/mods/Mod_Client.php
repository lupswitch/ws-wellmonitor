<?php
class Mod_Client
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Client');
    }
    public function client($ID,$PARAMETERS = "{}")
    {
        $data = $this->CI->Model_Client->select_client($ID);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        $arr = array(
            'id'=> ($data[0]['id']!="") ? (int) $data[0]['id'] : "",
            'nit'=> ($data[0]['nit']!="") ? (int) $data[0]['nit'] : "",
            'name'=> ($data[0]['nombre']!="") ? (string) $data[0]['nombre'] : "",
            'administrador'=> ($data[0]['administrador']!="") ? (int) $data[0]['administrador'] : "",
            'telephone'=>($data[0]['telefono']!="") ? (string) $data[0]['telefono'] : "",
            'address'=> ($data[0]['direccion']!="") ? (string) $data[0]['direccion'] : ""
        );
        $arrparameters = json_decode($PARAMETERS, true);
        $response      = filter_parameters($arr, $arrparameters);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$response);
    }
    public function clients($FILTROS= "{}",$PARAMETERS="{}",$LIMIT="",$PAGINAR= "{}",$ORDER= "{}")
    {
        $KEYS = array();
        $KEYS['id'] = array('key' => 'id', 'tabla' => true);
        $KEYS['nit'] = array('key' => 'nit', 'tabla' => true);
        $KEYS['name'] = array('key' => 'nombre', 'tabla' => true);
        $KEYS['administrador'] = array('key' => 'administrador', 'tabla' => true);
        $KEYS['contact_telephone'] = array('key' => 'contacto_telefono', 'tabla' => true);
        $KEYS['address'] = array('key' => 'direccion', 'tabla' => true);
        $KEYS['telephone'] = array('key' => 'telefono', 'tabla' => true);
        $KEYS['state'] = array('key' => 'departamento', 'tabla' => true);
        $KEYS['city'] = array('key' => 'ciudad', 'tabla' => true);
        $KEYS['map_latitude'] = array('key' => 'mapa_lat', 'tabla' => true);
        $KEYS['map_longitude'] = array('key' => 'mapa_lon', 'tabla' => true);
        $KEYS['map_zoom'] = array('key' => 'mapa_zoom', 'tabla' => true);
        $KEYS['gmt'] = array('key' => 'gmt', 'tabla' => true);
        $KEYS['contact_name'] = array('key' => 'contacto_nombre', 'tabla' => true);
        $KEYS['contact_email'] = array('key' => 'contacto_correo', 'tabla' => true);
        $KEYS['comments'] = array('key' => 'observaciones', 'tabla' => true);
        $data = $this->CI->Model_Client->select_clients($FILTROS,$LIMIT,$ORDER,$KEYS);
        $arr = array();
        for ($i=0; $i <count($data) ; $i++) { 
           $arr[] = array(
               'id'=> ($data[$i]['id']!="") ? (int) $data[$i]['id'] : "",
               'nit'=> ($data[$i]['nit']!="") ? (int) $data[$i]['nit'] : "",
               'name'=> ($data[$i]['nombre']!="") ? (string) $data[$i]['nombre'] : "",
               'administrador'=> ($data[$i]['administrador']!="") ? (int) $data[$i]['administrador'] : "",
               'contact_telephone'=> ($data[$i]['contacto_telefono']!="") ? (string) $data[$i]['contacto_telefono'] : "",
               'address'=> ($data[$i]['direccion']!="") ? (string) $data[$i]['direccion'] : "",
               'telephone'=> ($data[$i]['telefono']!="") ? (string) $data[$i]['telefono'] : "",
               'state'=> ($data[$i]['departamento']!="") ? (string) $data[$i]['departamento'] : "",
               'city'=> ($data[$i]['ciudad']!="") ? (string) $data[$i]['ciudad'] : "",
               'map_latitude'=> ($data[$i]['mapa_lat']!="") ?  $data[$i]['mapa_lat'] : "",
               'map_longitude'=> ($data[$i]['mapa_lon']!="") ?  $data[$i]['mapa_lon'] : "",
               'map_zoom'=> ($data[$i]['mapa_zoom']!="") ? (int) $data[$i]['mapa_zoom'] : "",
               'gmt'=> ($data[$i]['gmt']!="") ? (string) $data[$i]['gmt'] : "",
               'contact_name'=> ($data[$i]['contacto_nombre']!="") ? (string) $data[$i]['contacto_nombre'] : "",
               'contact_email'=> ($data[$i]['contacto_correo']!="") ? (string) $data[$i]['contacto_correo'] : "",
               'comments'=> ($data[$i]['observaciones']!="") ? (string) $data[$i]['observaciones'] : ""
           );
        }
        $arrparameters = json_decode($PARAMETERS, true);
        $response = filter_parameters($arr, $arrparameters);
        $arr = page_data($response, json_decode($PAGINAR, true));
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' => $arr);
    }
    public function set_client($nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments)
    {
      $id = $this->CI->Model_Client->insert_client($nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->client($id,"{}");
    }
    public function put_client($id,$nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments)
    {
      $id = $this->CI->Model_Client->update_client($id,$nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->client($id,"{}");
    }
    public function delete_client($id)
    {
        $data = $this->CI->Model_Client->delete_client($id);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->clients("{}","{}","","{}","{}");
    }     
}
