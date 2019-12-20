<?php
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/REST_Controller.php';
class Login extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
   public function index_post($id=0)
   {
     $params = json_decode(file_get_contents('php://input'), TRUE);
     if($id!=0){
        if (!is_number($id))
            die(json_encode(array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 2, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[2])));
        if (!isset($params['token']) || empty($params['token']))
            die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        print_r((new Token())->ValidateToken($params['token']));
        // if (!(new Token())->ValidateToken($params['token']))
        //     die(json_encode(array('STATUS_CODE' => 4, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[4])));
        // $this->response((new Mod_User())->logout($id), REST_Controller::HTTP_OK);
     }else{
        if (
            !isset($params['username']) || empty($params['username']) ||
            !isset($params['password']) || empty($params['password']) 
        )die(json_encode(array('STATUS_CODE' => 0, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[0])));
        $username  = $params['username'];
        $password  = $params['password'];
        $this->response((new Mod_User())->login($username,$password), REST_Controller::HTTP_OK);
     }
   }
}
