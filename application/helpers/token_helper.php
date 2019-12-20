<?php 
class Token
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Token');
        $this->CI->load->model('Model_User');
    }
    public function CreateToken($id)
    {
        $data = $this->CI->Model_User->select_user($id);
        $key = base64_encode($data[0]['usuario'] . "-" . get_next_codigo28($data[0]['id']) . "-" . time() . "+" . generateRandomString());
        $tokenString = array(
            "id" => $data[0]['id'],
            "user" => $data[0]['usuario'],
            "timestamp" => now(),
            "key" => $key
        );
        $token = AUTHORIZATION::generateToken($tokenString);
        $response = $this->CI->Model_Token->insert_token($data[0]['id'],$token,$key);
        return (!$response)?$response:$token;
    }
    public function ValidateToken($token)
    {
        $tokenJson = AUTHORIZATION::validateToken($token);
        $data = $this->CI->Model_Token->select_token($tokenJson['id'],$token);
        if (!$data)
            return false;
        $TokenKey = $tokenJson['key'];
        if($data[0]['token']==="")
            return false;
        if($TokenKey === $data[0]['llave'] && $token === $data[0]['token'])
           return AUTHORIZATION::validateTimestamp($token);
           return false;
    }
    public function RefreshToken($id)
    {
        if(!$this->CI->Model_Token->delete_token($id))
            return false;
        return $this->CreateToken($id);
    }
}
?>