<?php
class Mod_User
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->helper('token');
        $this->CI->load->model('Model_User');
    }
    public function login($username,$password)
    {
        $data = $this->CI->Model_User->login_user($username);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        if(count($data)==0)
            return array('STATUS_CODE' => 3, 'STATUS_DESCRIsPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 0, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[0]);
        if ($data[0]['activo']==0)
            return array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 5, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[5]);
        if($data[0]['clave']!= md5($password))
            return array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 1, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[1]);
        $token =(new Token())->CreateToken($data[0]['id']);
        $info = $this->user($data[0]['id'],"{}");
        $info['DATA']['token'] = $token;
        return $info;
    }
    public function logout($id)
    {
        if (!$this->CI->Model_Token->delete_token($id))
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1], 'DATA' =>array());
    }
    public function user($ID,$PARAMETERS = "{}")
    {
        $data = $this->CI->Model_User->select_user($ID);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
            $arr = array(
            'id' => ($data[0]['id']!="") ? (int) $data[0]['id'] : "",
            'identification' => ($data[0]['identificacion']!="") ? (int) $data[0]['identificacion'] : "",
            'username' => ($data[0]['usuario'] != "") ? (string) $data[0]['usuario'] : "",
            'first_name' => ($data[0]['nombres']!="") ? (string) $data[0]['nombres'] : "",
            'last_name' => ($data[0]['apellidos']!="") ? (string) $data[0]['apellidos'] : "",
            'email' => ($data[0]['correo']!="") ? (string) $data[0]['correo'] : "",
            'telephone' => ($data[0]['telefono']!="") ? (string) $$data[0]['telefono'] : "",
            'mobile' => ($data[0]['celular'] != "") ? (string) $$data[0]['celular'] : "",
            'active' => ($data[0]['activo']!="") ? (int) $data[0]['activo'] : 0,
            'notes' => ($data[0]['observaciones']!="") ? (string) $data[0]['observaciones'] : "",
            'administrador' => ($data[0]['administrador']!="") ? (int) $data[0]['administrador'] : 0,
            'operator' => ($data[0]['operador']!="") ? (int) $data[0]['operador'] : 0,
            'silence_sound' => ($data[0]['sonido_silenciado']!="") ? (int) $data[0]['sonido_silenciado'] : 0,
            'id_client' => ($data[0]['id_cliente']!="") ? (int) $data[0]['id_cliente'] : 0,
            'id_field' => ($data[0]['id_campo']!="") ? (int) $data[0]['id_campo'] :"",
            'installer' => ($data[0]['instalador'] != "") ? (int) $data[0]['instalador'] : 0,
            'administrator_client' => ($data[0]['cliente_administrador'] != "") ? (int) $data[0]['cliente_administrador'] : ""
        );
        $arrparameters = json_decode($PARAMETERS, true);
        $response      = filter_parameters($arr, $arrparameters);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$response);
    }
    public function users($FILTROS= "{}",$PARAMETERS="{}",$LIMIT="",$PAGINAR= "{}",$ORDER= "{}")
    {
        $KEYS = array();
        $KEYS['id'] = array('key' => 'id', 'tabla' => true);
        $KEYS['identification'] = array('key' => 'identificacion', 'tabla' => true);
        $KEYS['username'] = array('key' => 'usuario', 'tabla' => true);
        $KEYS['first_name'] = array('key' => 'nombres', 'tabla' => true);
        $KEYS['last_name'] = array('key' => 'apellidos', 'tabla' => true);
        $KEYS['email'] = array('key' => 'correo', 'tabla' => true);
        $KEYS['telephone'] = array('key' => 'telefono', 'tabla' => true);
        $KEYS['mobile'] = array('key' => 'celular', 'tabla' => true);
        $KEYS['active'] = array('key' => 'activo', 'tabla' => true);
        $KEYS['notes'] = array('key' => 'observaciones', 'tabla' => true);
        $KEYS['administrador'] = array('key' => 'administrador', 'tabla' => true);
        $KEYS['operator'] = array('key' => 'operador', 'tabla' => true);
        $KEYS['silence_sound'] = array('key' => 'sonido_silenciado', 'tabla' => true);
        $KEYS['id_client'] = array('key' => 'id_cliente', 'tabla' => true);
        $KEYS['id_field'] = array('key' => 'id_campo', 'tabla' => true);
        $KEYS['installer'] = array('key' => 'instalador', 'tabla' => true);
        $KEYS['administrator_client'] = array('key' => 'cliente_administrador', 'tabla' => true);
        $data = $this->CI->Model_User->select_users($FILTROS,$LIMIT,$ORDER,$KEYS);
        $arr = array();
        for ($i=0; $i <count($data) ; $i++) { 
           $arr[] = array(
            'id' => ($data[$i]['id']!="") ? (int) $data[$i]['id'] : "",
            'identification' => ($data[$i]['identificacion']!="") ? (int) $data[$i]['identificacion'] : "",
            'username' => ($data[$i]['usuario'] != "") ? (string) $data[$i]['usuario'] : "",
            'first_name' => ($data[$i]['nombres']!="") ? (string) $data[$i]['nombres'] : "",
            'last_name' => ($data[$i]['apellidos']!="") ? (string) $data[$i]['apellidos'] : "",
            'email' => ($data[$i]['correo']!="") ? (string) $data[$i]['correo'] : "",
            'telephone' => ($data[$i]['telefono']!="") ? (string) $$data[$i]['telefono'] : "",
            'mobile' => ($data[$i]['celular'] != "") ? (string) $$data[$i]['celular'] : "",
            'active' => ($data[$i]['activo']!="") ? (int) $data[$i]['activo'] : 0,
            'notes' => ($data[$i]['observaciones']!="") ? (string) $data[$i]['observaciones'] : "",
            'administrador' => ($data[$i]['administrador']!="") ? (int) $data[$i]['administrador'] : 0,
            'operator' => ($data[$i]['operador']!="") ? (int) $data[$i]['operador'] : 0,
            'silence_sound' => ($data[$i]['sonido_silenciado']!="") ? (int) $data[$i]['sonido_silenciado'] : 0,
            'id_client' => ($data[$i]['id_cliente']!="") ? (int) $data[$i]['id_cliente'] : 0,
            'id_field' => ($data[$i]['id_campo']!="") ? (int) $data[$i]['id_campo'] :"",
            'installer' => ($data[$i]['instalador'] != "") ? (int) $data[$i]['instalador'] : 0,
            'administrator_client' => ($data[$i]['cliente_administrador'] != "") ? (int) $data[$i]['cliente_administrador'] : ""
           );
        }
        $arrparameters = json_decode($PARAMETERS, true);
        $response = filter_parameters($arr, $arrparameters);
        $arr = page_data($response, json_decode($PAGINAR, true));
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' => $arr);
    }
    public function set_user($identification,$username,$first_name,$last_name,$email,$telephone,$mobile,$active,$notes,$administrador,$operator,$id_client,$id_field)
    {
      $id = $this->CI->Model_User->insert_user($identification, $username, $first_name, $last_name, $email, $telephone, $mobile, $active, $notes, $administrador, $operator, $id_client, $id_field);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->user($id,"{}");
    }
    public function put_user($id,$identification,$username,$first_name,$last_name,$email,$telephone,$mobile,$active,$notes,$administrador,$operator,$id_client,$id_field)
    {
      $id = $this->CI->Model_User->update_user($id,$identification, $username, $first_name, $last_name, $email, $telephone, $mobile, $active, $notes, $administrador, $operator, $id_client, $id_field);
      if(!$id)
        return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->user($id,"{}");
    }
    public function delete_user($id)
    {
        $data = $this->CI->Model_User->delete_user($id);
        if (!$data)
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        return $this->users("{}","{}","","{}","{}");
    }
    public function change_password($id,$password,$password_new){
        $data = $this->CI->Model_User->patch_select_user(array('id'=> $id),'clave');
        $passO = $data[0]['clave'];
        if(md5($password)==$passO){
            if(!$this->CI->Model_User->patch_update_user(array('id' => $id),array('clave'=>$password_new)))
                return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
            return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1]);
        }
        return array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 3, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[3]);
    }
    public function forgot_password($email)
    {
        $data = $this->CI->Model_User->patch_select_user(array('correo' => $email), 'correo,id');
        if(count($data)==0)
            return array('STATUS_CODE' => 3, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[3], 'RESPONSE_CODE' => 4, 'RESPONSE_DESCRIPTION' => RESPONSE::$RESPONSE[4]);
       $passN = generar_pass(6);
       if(!$this->CI->Model_User->patch_update_user(array('id' => $data[0]['id']),array('clave'=>$passN)))
            return array('STATUS_CODE' => 2, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[2]);
        (new Mod_Email())->email_forgot_password($email,$passN);
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1]);
    }       
}
