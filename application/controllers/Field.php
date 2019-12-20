<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Field extends REST_Controller
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
            $this->response((new Mod_Field())->field($id, $parameters), REST_Controller::HTTP_OK);
        }else{
            $filtros    = ($this->get('filtros') && $this->get('filtros')!="") ? urldecode($this->get('filtros')) : "{}";
            $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')): "{}";
            $limit      = ($this->get('limit') && $this->get('limit')!="") && is_number($this->get('limit')) ? $this->get('limit'): "";
            $paginar    = ($this->get('page') && $this->get('page')!="") ? urldecode($this->get('page')): "{}";
            $order      = ($this->get('order') && $this->get('order')!="") ? urldecode($this->get('order')) : "{}";
            $this->response((new Mod_Field())->fields($filtros, $parameters, $limit, $paginar, $order), REST_Controller::HTTP_OK);
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
        $map_latitude = ($this->post('map_latitude') && $this->post('map_latitude')!="") ? floatval($this->post('map_latitude')) : 0;
        $map_longitude = ($this->post('map_longitude') && $this->post('map_longitude')!="") ? floatval($this->post('map_longitude')) : 0;
        $map_zoom = ($this->post('map_zoom') && $this->post('map_zoom')!="") ? (int) $this->post('map_zoom') : "";
        $email = ($this->post('email') && $this->post('email')!="") ? string_secure($this->post('email')) : "";
        $osg = ($this->post('osg') && $this->post('osg')!="") ? floatval($this->post('osg')) : 0; 
        $comments = ($this->post('description') && $this->post('description')!="") ? string_secure($this->post('description')) : "";
        $this->response((new Mod_Field())->set_field($name,$map_latitude,$map_longitude,$map_zoom ,$email,$osg,$comments), REST_Controller::HTTP_OK);
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
        $map_latitude = ($this->put('map_latitude') && $this->put('map_latitude')!="") ? floatval($this->put('map_latitude')) : 0;
        $map_longitude = ($this->put('map_longitude') && $this->put('map_longitude')!="") ? floatval($this->put('map_longitude')) : 0;
        $map_zoom = ($this->put('map_zoom') && $this->put('map_zoom')!="") ? (int) $this->put('map_zoom') : 0;
        $email = ($this->put('email') && $this->put('email')!="") ? string_secure($this->put('email')) : "";
        $osg = ($this->put('osg') && $this->put('osg')!="") ? string_secure($this->put('osg')) : 0; 
        $comments = ($this->put('description') && $this->put('description')!="") ? string_secure($this->put('description')) : "";
        $this->response((new Mod_Field())->put_field($id,$name,$map_latitude,$map_longitude,$map_zoom,$email,$osg,$comments), REST_Controller::HTTP_OK);
    }
    public function index_delete($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (!ValidateToken($this->delete('token')))
            die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $this->response((new Mod_Field())->delete_field($id), REST_Controller::HTTP_OK);
    }
}
