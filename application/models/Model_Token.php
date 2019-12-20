<?php
class Model_Token extends CI_Model
{
    protected  $db_slave;
    protected  $db_master;
    protected  $db_local;
    public function __construct()
    {
        parent::__construct();
        // Este metodo conecta a nuestra segunda conexión
        $this->db_slave = $this->load->database('slave', TRUE);
        $this->db_master = $this->load->database('master', TRUE);
        $this->db_local = $this->load->database('local', TRUE);
    }
    public function insert_token($id,$token,$key){
        $sql = "INSERT INTO token(id_usuario,token,llave)VALUES($id,'$token','$key')";
        return $this->db_local->query($sql);
    }
    public function select_token($id,$token)
    {
        $sql = "SELECT token,llave FROM token WHERE id_usuario=$id AND token='$token'";
        return $this->db_local->query($sql);
    }
    public function delete_token($id)
    {
        $sql = "DELETE FROM token WHERE id_usuario=$id";
        return $this->db_local->query($sql);
    }
}
