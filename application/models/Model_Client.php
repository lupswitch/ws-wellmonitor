<?php
class Model_Client extends CI_Model
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
    public function select_client($id)
    {
        $sql = "SELECT id,nit,nombre,administrador,
                contacto_telefono,direccion,telefono
                FROM cliente WHERE id=$id";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_clients($filtros,$limit,$order,$keys)
    {
        $sqlselect = "SELECT id,nit,nombre,direccion,telefono,ciudad,departamento,
                      contacto_nombre,contacto_telefono,contacto_correo,observaciones,
                      mapa_lat,mapa_lon,mapa_zoom,administrador,gmt 
                      FROM cliente";
        $sqlwhere  = "";
        return $this->db_local->query(integrar_api_sql($sqlselect,$sqlwhere,$keys,$filtros,$limit,$order))->result_array();
    }
    public function insert_client($nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments)
    {
        $sql = "INSERT INTO cliente(nit,nombre,direccion,telefono,ciudad,departamento,contacto_nombre,contacto_telefono,contacto_correo,observaciones,mapa_lat,mapa_lon,mapa_zoom,administrador,gmt)
                VALUES($nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments)";
        if (!$this->db_local->query($sql))
            return false;
            return $this->db_local->insert_id();
    }
    public function update_client($id,$nit,$name,$address,$telephone,$state,$city,$map_latitude,$map_longitude,$map_zoom,$administrador,$gmt,$contact_name,$contact_email,$contact_telephone,$comments)
    {
        $sql = "UPDATE cliente SET 
                nit=$nit,
                nombre=$name,
                direccion=$address,
                telefono=$telephone,
                ciudad=$city,
                departamento=$state,
                contacto_nombre=$contact_name,
                contacto_telefono=$contact_telephone,
                contacto_correo=$contact_email,
                observaciones=$comments,
                mapa_lat=$map_latitude,
                mapa_lon=$map_longitude,
                mapa_zoom=$map_zoom,
                administrador=$administrador,
                gmt=$gmt
                WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function delete_client($id)
    {
        $sql = "DELETE FROM cliente WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function patch_update_client($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('cliente', $data);
    }
    public function patch_select_client($where=array(),$string)
    {
        $this->db_local->select($string);
        $this->db_local->from('cliente');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        $data = $this->db_local->get();
        return $data->result_array();
    }
}
