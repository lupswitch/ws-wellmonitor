<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Datastats extends REST_Controller
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
        $date_ini  = ($this->get('date_ini') && $this->get('date_ini')!="")?(int)urldecode($this->get('date_ini')):7;  
        $date_end  = ($this->get('date_end') && $this->get('date_end')!="")?(int)urldecode($this->get('date_end')):30; 
        $user_id = 1;  
        $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')) : "{}";
        $this->response((new Mod_Datastats())->tableDataStats($id,$date_ini,$date_end,$user_id,$parameters), REST_Controller::HTTP_OK);
    }
    
}
