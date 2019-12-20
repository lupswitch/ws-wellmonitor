<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Event extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index_get($id=0){
        if (!$this->get('token') || $this->get('token') == "")
            die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        // if (!ValidateToken($this->get('token')))
        //     die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        $filtros    = ($this->get('filtros') && $this->get('filtros')!="") ? urldecode($this->get('filtros')) : "{}";
        $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')): "{}";
        $limit      = ($this->get('limit') && $this->get('limit')!="") && is_number($this->get('limit')) ? $this->get('limit'): "";
        $paginar    = ($this->get('page') && $this->get('page')!="") ? urldecode($this->get('page')): "{}";
        $order      = ($this->get('order') && $this->get('order')!="") ? urldecode($this->get('order')) : "{}";
        $date_ini  = ($this->get('date_ini') && $this->get('date_ini')!="")?(string)urldecode($this->get('date_ini')):date("Y-m-d H:i:s",strtotime(date("d-m-Y")."- 60 days"));  
        $date_end  = ($this->get('date_end') && $this->get('date_end')!="")?(string)urldecode($this->get('date_end')):date("Y-m-d H:i:s"); 
        $user_id = 1;  
        $this->response((new Mod_Event())->getMaintenanceTable($id,$date_ini,$date_end,$user_id,$filtros,$parameters,$limit,$paginar,$order), REST_Controller::HTTP_OK);
    }
    
}
