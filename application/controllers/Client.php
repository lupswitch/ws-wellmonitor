<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Client extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index_get($id=0){
        if (!$this->get('token') || $this->get('token') == "")
            die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        // if (!(new Token())->ValidateToken($this->get('token')))
        //     die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));    
        if(!empty($id)){
            if (!is_number($id))
                die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
            $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')) : "{}";
            $this->response((new Mod_Client())->client($id, $parameters), REST_Controller::HTTP_OK);
        }else{
            $filtros    = ($this->get('filtros') && $this->get('filtros')!="") ? urldecode($this->get('filtros')) : "{}";
            $parameters = ($this->get('parameters') && $this->get('parameters')!="") ? urldecode($this->get('parameters')): "{}";
            $limit      = ($this->get('limit') && $this->get('limit')!="") && is_number($this->get('limit')) ? $this->get('limit'): "";
            $paginar    = ($this->get('page') && $this->get('page')!="") ? urldecode($this->get('page')): "{}";
            $order      = ($this->get('order') && $this->get('order')!="") ? urldecode($this->get('order')) : "{}";
            $this->response((new Mod_Client())->clients($filtros, $parameters, $limit, $paginar, $order), REST_Controller::HTTP_OK);
        }
    }
    public function index_post()
    {
        if (
            !$this->post('nit') || $this->post('nit') =="" ||
            !$this->post('name') || $this->post('name') =="" ||
            !$this->post('administrador') || $this->post('administrador') =="" ||
            !$this->post('token')  || $this->post('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if(!ValidateToken($this->post('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $nit = (int) $this->post('nit');
        $name = string_secure($this->post('name')); 
        $administrador = (int)$this->post('administrador');
        $contact_telephone = ($this->post('contact_telephone') && $this->post('contact_telephone')!="") ? string_secure($this->post('contact_telephone')) : ""; 
        $address = ($this->post('address') && $this->post('address')!="") ? string_secure($this->post('address')) : "";
        $telephone = ($this->post('telephone') && $this->post('telephone')!="") ? string_secure($this->post('telephone')) : "";
        $state = ($this->post('state') && $this->post('state')!="") ? string_secure($this->post('state')) : "";
        $city = ($this->post('city') && $this->post('city')!="") ? string_secure($this->post('city')) : "";
        $map_latitude = ($this->post('map_latitude') && $this->post('map_latitude')!="") ? floatval($this->post('map_latitude')) : "";
        $map_longitude = ($this->post('map_longitude') && $this->post('map_longitude')!="") ? floatval($this->post('map_longitude')) : "";
        $map_zoom = ($this->post('map_zoom') && $this->post('map_zoom')!="") ? (int) $this->post('map_zoom') : "";
        $gmt = ($this->post('gmt') && $this->post('gmt')!="") ? string_secure($this->post('gmt')) : "";
        $contact_name = ($this->post('contact_name') && $this->post('contact_name')!="") ? string_secure($this->post('contact_name')) : "";
        $contact_email = ($this->post('contact_email') && $this->post('contact_email')!="") ? string_secure($this->post('contact_email')) : "";
        $comments = ($this->post('comments') && $this->post('comments')!="") ? string_secure($this->post('comments')) : "";
        $this->response((new Mod_Client())->set_client($nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments), REST_Controller::HTTP_OK);
    }
    public function index_put($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (
            !$this->put('nit') || $this->put('nit') =="" ||
            !$this->put('name') || $this->put('name') =="" ||
            !$this->put('administrador') || $this->put('administrador') =="" ||
            !$this->put('token')  || $this->put('token') =="" 
        )
        die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        if (!ValidateToken($this->put('token')))
        die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $nit = (int) $this->put('nit');
        $name = string_secure($this->put('name')); 
        $administrador = (int)$this->put('administrador');
        $contact_telephone = ($this->put('contact_telephone') && $this->put('contact_telephone')!="") ? string_secure($this->put('contact_telephone')) : ""; 
        $address = ($this->put('address') && $this->put('address')!="") ? string_secure($this->put('address')) : "";
        $telephone = ($this->put('telephone') && $this->put('telephone')!="") ? string_secure($this->put('telephone')) : "";
        $state = ($this->put('state') && $this->put('state')!="") ? string_secure($this->put('state')) : "";
        $city = ($this->put('city') && $this->put('city')!="") ? string_secure($this->put('city')) : "";
        $map_latitude = ($this->put('map_latitude') && $this->put('map_latitude')!="") ? floatval($this->put('map_latitude')) : "";
        $map_longitude = ($this->put('map_longitude') && $this->put('map_longitude')!="") ? floatval($this->put('map_longitude')) : "";
        $map_zoom = ($this->put('map_zoom') && $this->put('map_zoom')!="") ? (int) $this->put('map_zoom') : "";
        $gmt = ($this->put('gmt') && $this->put('gmt')!="") ? string_secure($this->put('gmt')) : "";
        $contact_name = ($this->put('contact_name') && $this->put('contact_name')!="") ? string_secure($this->put('contact_name')) : "";
        $contact_email = ($this->put('contact_email') && $this->put('contact_email')!="") ? string_secure($this->put('contact_email')) : "";
        $comments = ($this->put('comments') && $this->put('comments')!="") ? string_secure($this->put('comments')) : "";
        $this->response((new Mod_Client())->put_client($id,$nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments), REST_Controller::HTTP_OK);
    }
    public function index_delete($id)
    {
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (!ValidateToken($this->delete('token')))
            die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        $this->response((new Mod_Client())->delete_client($id), REST_Controller::HTTP_OK);
    }
}
