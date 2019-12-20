<?php
class Model_View extends CI_Model
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
    public function patch_select_view($where=array(),$string)
    {
        $this->db_local->select($string);
        $this->db_local->from('vista');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        return $this->db_local->get()->result_array();
    }
}
