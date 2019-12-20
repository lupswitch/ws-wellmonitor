
<?php
class Model_Device_type extends CI_Model
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
    public function select_device_type($id)
    {
        $sql = "SELECT id,nombre,descripcion
                FROM r_equipo_tipo WHERE id=$id";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_device_types($filtros,$limit,$order,$keys)
    {
        $sqlselect = "SELECT id,nombre,descripcion
                      FROM r_equipo_tipo";
        $sqlwhere  = "";
        return $this->db_local->query(integrar_api_sql($sqlselect,$sqlwhere,$keys,$filtros,$limit,$order))->result_array();
    }
    public function insert_device_type($name,$description)
    {
        $sql = "INSERT INTO r_equipo_tipo(nombre,descripcion)
                VALUES($name,$description)";
        if (!$this->db_local->query($sql))
            return false;
            return $this->db_local->insert_id();
    }
    public function update_device_type($id,$name,$description)
    {
        $sql = "UPDATE r_equipo_tipo SET 
                nombre=$name,
                descripcion=$description
                WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function delete_device_type($id)
    {
        $sql = "DELETE FROM r_equipo_tipo WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function patch_update_device_type($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('r_equipo_tipo', $data);
    }
    public function patch_select_device_type($where=array(),$string)
    {
        $this->db_local->select($string);
        $this->db_local->from('r_equipo_tipo');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        return $this->db_local->get();
    }
}
