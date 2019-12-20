<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Cluster extends REST_Controller
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
            $this->response((new Mod_Cluster())->cluster($id, $parameters), REST_Controller::HTTP_OK);
        }else{
            $filtros    = ($this->get('filtros') && $this->get('filtros')!="") ? urldecode($this->get('filtros')) : "{}";
            $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')): "{}";
            $limit      = ($this->get('limit') && $this->get('limit')!="") && is_number($this->get('limit')) ? $this->get('limit'): "";
            $paginar    = ($this->get('page') && $this->get('page')!="") ? urldecode($this->get('page')): "{}";
            $order      = ($this->get('order') && $this->get('order')!="") ? urldecode($this->get('order')) : "{}";
            $this->response((new Mod_Cluster())->clusters($filtros, $parameters, $limit, $paginar, $order), REST_Controller::HTTP_OK);
        }
    }
    public function index_post()
    {
        if (
            !$this->post('field_id') || $this->post('field_id') =="" ||
            !$this->post('name') || $this->post('name') =="" ||
            !$this->post('token')  || $this->post('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if(!ValidateToken($this->post('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $name = string_secure($this->post('name')); 
        $field_id = (int)$this->post('field_id');
        $description = ($this->post('description') && $this->post('description')!="") ? string_secure($this->post('description')) : "";
        $this->response((new Mod_Cluster())->set_cluster($name,$field_id,$description), REST_Controller::HTTP_OK);
    }
    public function index_put($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (
            !$this->put('field_id') || $this->put('field_id') =="" ||
            !$this->put('name') || $this->put('name') =="" ||
            !$this->put('token')  || $this->put('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if (!ValidateToken($this->put('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $name = string_secure($this->put('name')); 
        $field_id = (int)$this->put('field_id');
        $description = ($this->put('description') && $this->post('description')!="") ? string_secure($this->post('description')) : "";
        $this->response((new Mod_Cluster())->put_cluster($id,$name,$field_id,$description), REST_Controller::HTTP_OK);
    }
    public function index_delete($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (!ValidateToken($this->delete('token')))
            die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $this->response((new Mod_Cluster())->delete_cluster($id), REST_Controller::HTTP_OK);
    }
}
