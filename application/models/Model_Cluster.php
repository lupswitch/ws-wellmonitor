<?php
class Model_Cluster extends CI_Model
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
    public function select_cluster($id)
    {
        $sql = "SELECT id,id_campo,nombre,descripcion FROM locacion WHERE id=$id";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_clusters($filtros,$limit,$order,$keys)
    {
        $sqlselect = "SELECT l.id,c.nombre AS nombre_campo,l.nombre,l.descripcion,l.id_campo FROM locacion l,campo c";
        $sqlwhere  = " WHERE l.id_campo=c.id";
        return $this->db_local->query(integrar_api_sql($sqlselect,$sqlwhere,$keys,$filtros,$limit,$order))->result_array();
    }
    public function insert_cluster($id_field,$name,$description)
    {
        $sql = "INSERT INTO locacion(id_campo,nombre,descripcion)
                VALUES($id_field,$name,$description)";
        if (!$this->db_local->query($sql))
            return false;
            return $this->db_local->insert_id();
    }
    public function update_cluster($id,$id_field,$name,$description)
    {
        $sql = "UPDATE locacion SET 
                id_campo=$id_field,
                nombre=$name,
                descripcion=$description
                WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function delete_user($id)
    {
        $sql = "DELETE FROM locacion WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function patch_update_cluster($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('locacion', $data);
    }
    public function patch_select_cluster($where=array(),$string)
    {
        $this->db_local->select($string);
        $this->db_local->from('locacion');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        return $this->db_local->get();
    }
}
