<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Vatiable extends REST_Controller
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
            $this->response((new Mod_Variable())->variable($id, $parameters), REST_Controller::HTTP_OK);
        }else{
            $filtros    = ($this->get('filtros') && $this->get('filtros')!="") ? urldecode($this->get('filtros')) : "{}";
            $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')): "{}";
            $limit      = ($this->get('limit') && $this->get('limit')!="") && is_number($this->get('limit')) ? $this->get('limit'): "";
            $paginar    = ($this->get('page') && $this->get('page')!="") ? urldecode($this->get('page')): "{}";
            $order      = ($this->get('order') && $this->get('order')!="") ? urldecode($this->get('order')) : "{}";
            $this->response((new Mod_Variable())->variables($filtros, $parameters, $limit, $paginar, $order), REST_Controller::HTTP_OK);
        }
    }
    public function index_post()
    {
        if (
            !$this->post('name') || $this->post('name') =="" ||
            !$this->post('token')  || $this->post('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if(!ValidateToken($this->post('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $name = string_secure($this->post('name')); 
        $graphicable = ($this->post('graphicable') && $this->post('graphicable')!="") ?(int)$this->post('graphicable'):0;
        $manual = ($this->post('manual') && $this->post('manual')!="") ?(int)$this->post('manual'):0;
        $manual_type =($this->post('manual_type') && $this->post('manual_type')!="") ? (int)$this->post('manual_type'):0;
        $mobile = ($this->post('mobile') && $this->post('mobile')!="") ?(int)$this->post('mobile'):0;
        $description = ($this->post('description') && $this->post('description')!="") ? string_secure($this->post('description')) : NULL;
        $this->response((new Mod_Variable())->set_variable($name,$graphicable,$manual,$manual_type,$mobile,$description), REST_Controller::HTTP_OK);
    }
    public function index_put($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (
            !$this->put('name') || $this->put('name') =="" ||
            !$this->put('token')  || $this->put('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if (!ValidateToken($this->put('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $name = string_secure($this->put('name')); 
        $graphicable = ($this->put('graphicable') && $this->put('graphicable')!="") ?(int)$this->put('graphicable'):0;
        $manual = ($this->put('manual') && $this->put('manual')!="") ?(int)$this->put('manual'):0;
        $manual_type =($this->put('manual_type') && $this->put('manual_type')!="") ? (int)$this->put('manual_type'):0;
        $mobile = ($this->put('mobile') && $this->put('mobile')!="") ?(int)$this->put('mobile'):0;
        $description = ($this->put('description') && $this->post('description')!="") ? string_secure($this->post('description')) : NULL;
        $this->response((new Mod_Variable())->put_variable($id,$name,$graphicable,$manual,$manual_type,$mobile,$description), REST_Controller::HTTP_OK);
    }
    public function index_delete($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (!ValidateToken($this->delete('token')))
            die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $this->response((new Mod_Variable())->delete_variable($id), REST_Controller::HTTP_OK);
    }
}
