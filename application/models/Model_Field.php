<?php
class Model_Field extends CI_Model
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
    public function select_field($id)
    {
        $sql = "SELECT id,nombre,descripcion,mapa_lat,mapa_lon,mapa_zoom,correo,gravedad_especifica
                FROM campo WHERE id=$id";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_fields($filtros,$limit,$order,$keys)
    {
        $sqlselect = "SELECT id,nombre,gravedad_especifica,mapa_lat,mapa_lon,mapa_zoom,descripcion
                      FROM campo";
        $sqlwhere  = "";
        return $this->db_local->query(integrar_api_sql($sqlselect,$sqlwhere,$keys,$filtros,$limit,$order))->result_array();
    }
    public function insert_field($name,$map_latitude,$map_longitude,$map_zoom ,$email,$osg,$comments)
    {
        $sql = "INSERT INTO campo(nombre,descripcion,mapa_lat,mapa_lon,mapa_zoom,correo,gravedad_especifica)
                VALUES($name,$comments,$map_latitude,$map_longitude,$map_zoom,$email,$osg)";
        if (!$this->db_local->query($sql))
            return false;
            return $this->db_local->insert_id();
    }
    public function update_field($id,$name,$map_latitude,$map_longitude,$map_zoom,$email,$osg,$comments)
    {
        $sql = "UPDATE campo SET 
                nombre=$name,
                descripcion=$comments,
                mapa_lat=$map_latitude,
                mapa_lon=$map_longitude,
                mapa_zoom=$map_zoom,
                correo=$email,
                gravedad_especifica=$osg
                WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function delete_user($id)
    {
        $sql = "DELETE FROM campo WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function patch_update_field($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('campo', $data);
    }
    public function patch_select_field($where=array(),$string)
    {
        $this->db_local->select($string);
        $this->db_local->from('campo');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        return $this->db_local->get();
    }
    public function sql_device_field($USER_ID,$CLIENT_ID)
    {
        $sql = "SELECT c.id as f_id, " . ($USER_ID < 0 ? " CONCAT('Field_',CAST(c.id as CHAR(50))) " : " c.nombre ") . " as f_name "
        . " ,c.mapa_lat AS f_lat, c.mapa_lon AS f_lon, c.mapa_zoom AS f_zoom, "
        . " (SELECT count(permiso) FROM usuario_campo WHERE (id_campo=c.id and id_usuario = $USER_ID) order by id_usuario) as f_class_permiso,"
        . " (SELECT permiso FROM usuario_campo WHERE (id_campo=c.id and id_usuario = $USER_ID) order by id_usuario LIMIT 1) as f_permiso,"
        .  "(SELECT count(permiso) FROM usuario_equipo WHERE id_equipo IN (SELECT id FROM equipo WHERE id_campo=c.id) AND id_usuario =$USER_ID order by id_usuario) AS f_class_ue_permiso,"
        .  "(SELECT permiso FROM usuario_equipo WHERE id_equipo IN (SELECT id FROM equipo WHERE id_campo=c.id) AND id_usuario =$USER_ID order by id_usuario LIMIT 1) AS f_ue_permiso"
        . " FROM equipo e, campo c WHERE ";
        if ($CLIENT_ID == 0)
            $sql = $sql . " (c.id=e.id_campo AND e.id_padre=-1) ";
        else
        $sql = $sql . " (c.id=e.id_campo AND ((e.id_cliente=$CLIENT_ID OR (c.id IN (SELECT id_campo FROM usuario_campo WHERE id_usuario = $USER_ID)) OR (c.id IN (SELECT id_campo FROM equipo WHERE id IN (SELECT id_equipo FROM usuario_equipo WHERE id_usuario = $USER_ID)))) AND e.id_padre=-1) ) ";
        $sql = $sql . " GROUP by e.id_campo ORDER BY f_name ";
        return $this->db_local->query($sql)->result_array();
    }
}
