<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Tree extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index_get($client_id=0){
        if (!$this->get('token') || $this->get('token') == "")
            die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        // if (!(new Token())->ValidateToken($this->get('token')))
        //     die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));    
        $filtros    = ($this->get('filtros') && $this->get('filtros')!="") ? urldecode($this->get('filtros')) : "{}";
        $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')): "{}";
        $limit      = ($this->get('limit') && $this->get('limit')!="") && is_number($this->get('limit')) ? $this->get('limit'): "";
        $paginar    = ($this->get('page') && $this->get('page')!="") ? urldecode($this->get('page')): "{}";
        $order      = ($this->get('order') && $this->get('order')!="") ? urldecode($this->get('order')) : "{}";
        $user_id    = 1;
        $this->response((new Mod_Tree())->fieldClient($client_id,$user_id,$filtros, $parameters, $limit, $paginar, $order),REST_Controller::HTTP_OK); 
    }
}
