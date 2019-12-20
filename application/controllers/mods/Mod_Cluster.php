<?php
class Mod_Cluster
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Cluster');
    }
    public function cluster($ID,$PARAMETERS = "{}")
    {
        $data = $this->CI->Model_Cluster->select_cluster($ID);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        $arr = array(
            'id'=> ($data[0]['id']!="") ? (int) $data[0]['id'] : "",
            'id_field'=> ($data[0]['id_campo']!="") ? (int) $data[0]['id_campo'] : "",
            'name'=> ($data[0]['nombre']!="") ? (string) $data[0]['nombre'] : "",
            'description'=> ($data[0]['descripcion']!="") ? (string) $data[0]['descripcion'] : ""
        );
        $arrparameters = json_decode($PARAMETERS, true);
        $response      = filter_parameters($arr, $arrparameters);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$response);
    }
    public function clusters($FILTROS= "{}",$PARAMETERS="{}",$LIMIT="",$PAGINAR= "{}",$ORDER= "{}")
    {
        $KEYS = array();
        $KEYS['id'] = array('key' => 'id', 'tabla' => true);
        $KEYS['id_field'] = array('key' => 'id_campo', 'tabla' => true);
        $KEYS['name'] = array('key' => 'nombre', 'tabla' => true);
        $data = $this->CI->Model_Cluster->select_clusters($FILTROS,$LIMIT,$ORDER,$KEYS);
        $arr = array();
        for ($i=0; $i <count($data) ; $i++) { 
           $arr[] = array(
            'id'=> ($data[$i]['id']!="") ? (int) $data[$i]['id'] : "",
            'id_field'=> ($data[$i]['id_campo']!="") ? (int) $data[$i]['id_campo'] : "",
            'name'=> ($data[$i]['nombre']!="") ? (string) $data[$i]['nombre'] : "",
            'description'=> ($data[$i]['descripcion']!="") ? (string) $data[$i]['descripcion'] : ""
           );
        }
        $arrparameters = json_decode($PARAMETERS, true);
        $response = filter_parameters($arr, $arrparameters);
        $arr = page_data($response, json_decode($PAGINAR, true));
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' => $arr);
    }
    public function set_cluster($id_field,$name,$description)
    {
      $id = $this->CI->Model_Cluster->insert_cluster($id_field,$name,$description);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->cluster($id,"{}");
    }
    public function put_client($id,$id_field,$name,$description)
    {
      $id = $this->CI->Model_Cluster->update_cluster($id,$id_field,$name,$description);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->cluster($id,"{}");
    }
    public function delete_cluster($id)
    {
        $data = $this->CI->Model_Cluster->delete_cluster($id);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->clusters("{}","{}","","{}","{}");
    }     
}
