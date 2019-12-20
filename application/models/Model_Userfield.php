<?php
class Model_Userfield extends CI_Model
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
    public function patch_update_userfield($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('usuario_campo', $data);
    }
    public function patch_select_userfield($where=array(),$string,$join=array())
    {
        $this->db_local->select($string);
        $this->db_local->from('usuario_campo');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        if(count($join)!=0){
            $this->db_local->join($join[0],$join[1],$join[2]);
        }
        $data = $this->db_local->get();
        return $data->result_array();
    }
}
