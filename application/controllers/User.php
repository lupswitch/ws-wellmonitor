<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class User extends REST_Controller
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
            $this->response((new Mod_User())->user($id, $parameters), REST_Controller::HTTP_OK);
        }else{
            $filtros    = ($this->get('filtros') && $this->get('filtros')!="") ? urldecode($this->get('filtros')) : "{}";
            $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')): "{}";
            $limit      = ($this->get('limit') && $this->get('limit')!="") && is_number($this->get('limit')) ? $this->get('limit'): "";
            $paginar    = ($this->get('page') && $this->get('page')!="") ? urldecode($this->get('page')): "{}";
            $order      = ($this->get('order') && $this->get('order')!="") ? urldecode($this->get('order')) : "{}";
            $this->response((new Mod_User())->users($filtros, $parameters, $limit, $paginar, $order), REST_Controller::HTTP_OK);
        }
    }
    public function index_post()
    {
        if (
            !$this->post('id_client') || $this->post('id_client') =="" ||
            !$this->post('id_field') || $this->post('id_field') =="" ||
            !$this->post('first_name') || $this->post('first_name') ==""  ||
            !$this->post('last_name') || $this->post('last_name') =="" ||
            !$this->post('user_name') || $this->post('user_name') ==""  ||
            !$this->post('email') || $this->post('email') =="" ||
            !$this->post('token')  || $this->post('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if(!ValidateToken($this->post('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $identification = ($this->post('identification') && $this->post('identification')!="") ? string_secure($this->post('identification')) : "";
        $username   = string_secure($this->post('user_name'));
        $first_name = string_secure($this->post('first_name'));
        $last_name  = string_secure($this->post('last_name'));
        $email = string_secure($this->post('email'));
        $telephone = ($this->post('telephone') && $this->post('telephone')!="") ? string_secure($this->post('telephone')) : "";
        $mobile = ($this->post('mobile') && $this->post('mobile')!="") ? string_secure($this->post('mobile')) : "";
        $active = ($this->post('active') && $this->post('active')!="") ? (int) $this->post('active') : 0;
        $notes  = ($this->post('notes') && $this->post('notes')!="") ? string_secure($this->post('notes')) : "";
        $administrador = ($this->post('administrador') && $this->post('administrador')!="") ? (int) $this->post('administrador') : 0;
        $operator  = ($this->post('operator') && $this->post('operator')!="") ? (int) $this->post('operator') : 0;
        $id_client = $this->post('id_client');
        $id_field  = $this->post('id_field');
        $installer = ($this->post('installer') && $this->post('installer') != "") ? (int) $this->post('installer') : 0;
        $this->response((new Mod_User())->set_user($identification, $username, $first_name, $last_name, $email, $telephone, $mobile, $active, $notes, $administrador, $operator, $id_client, $id_field), REST_Controller::HTTP_OK);
    }
    public function index_put($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (
            !$this->put('id_client') || $this->put('id_client') == "" ||
            !$this->put('id_field') || $this->put('id_field') == "" ||
            !$this->put('first_name') || $this->put('first_name') == ""  ||
            !$this->put('last_name')  || $this->put('last_name') == "" ||
            !$this->put('user_name')  || $this->put('user_name') == ""  ||
            !$this->put('email')  || $this->put('email') == "" ||
            !$this->put('token')  || $this->put('token') == ""
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if (!ValidateToken($this->put('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $identification = ($this->put('identification') && $this->put('identification') != "") ? string_secure($this->put('identification')) : "";
        $username   = string_secure($this->put('user_name'));
        $first_name = string_secure($this->put('first_name'));
        $last_name  = string_secure($this->put('last_name'));
        $email = string_secure($this->put('email'));
        $telephone = ($this->put('telephone') && $this->put('telephone') != "") ? string_secure($this->put('telephone')) : "";
        $mobile = ($this->put('mobile') && $this->put('mobile') != "") ? string_secure($this->put('mobile')) : "";
        $active = ($this->put('active') && $this->put('active') != "") ? (int) $this->put('active') : 0;
        $notes  = ($this->put('notes') && $this->put('notes') != "") ? string_secure($this->put('notes')) : "";
        $administrador = ($this->put('administrador') && $this->put('administrador') != "") ? (int) $this->put('administrador') : 0;
        $operator  = ($this->put('operator') && $this->put('operator') != "") ? (int) $this->put('operator') : 0;
        $id_client = $this->put('id_client');
        $id_field  = $this->put('id_field');
        $installer = ($this->put('installer') && $this->put('installer') != "") ? (int) $this->put('installer') : 0;
        $this->response((new Mod_User())->put_user($id,$identification, $username, $first_name, $last_name, $email, $telephone, $mobile, $active, $notes, $administrador, $operator, $id_client, $id_field), REST_Controller::HTTP_OK);
    }
    public function index_delete($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (!ValidateToken($this->delete('token')))
            die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $this->response((new Mod_User())->delete_user($id), REST_Controller::HTTP_OK);
    }
}
