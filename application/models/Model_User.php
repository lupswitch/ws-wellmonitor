<?php
class Model_User extends CI_Model
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
    public function login_user($username)
    {
        $sql = "SELECT id,clave,activo FROM usuario WHERE usuario='$username'";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_user($id)
    {
        $sql = "SELECT 
                u.id,u.id_cliente,u.id_campo,u.identificacion,
                u.usuario,u.nombres,u.apellidos,u.telefono,
                u.celular,u.correo,u.observaciones,u.activo,u.administrador,
                u.operador,u.sonido_silenciado,c.administrador AS cliente_administrador,u.instalador 
                FROM usuario u JOIN cliente c ON u.id_cliente=c.id 
                WHERE u.id=$id  ";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_users($filtros,$limit,$order,$keys)
    {
        $sqlselect = "SELECT 
                    u.id,u.id_cliente,c.nombre,u.id_campo,u.identificacion,
                    u.usuario,u.clave,u.nombres,u.apellidos,u.telefono,
                    u.celular,u.correo,u.observaciones,u.activo,u.administrador,
                    u.operador,u.sonido_silenciado,c.administrador AS cliente_administrador,u.instalador 
                    FROM usuario u JOIN cliente c ON u.id_cliente=c.id ";
        $sqlwhere  = "";
        return $this->db_local->query(integrar_api_sql($sqlselect,$sqlwhere,$keys,$filtros,$limit,$order))->result_array();
    }
    public function insert_user($identification,$username,$first_name,$last_name,$email,$telephone,$mobile,$active,$notes,$administrador,$operator,$id_client,$id_field)
    {
        $sql = "INSERT INTO usuario(identificacion,usuario,nombres,apellidos,correo,telefono,celular,activo,observaciones,administrador,operador,id_cliente,id_campo)
                VALUES($identification,$username,$first_name,$last_name,$email,$telephone,$mobile,$active,$notes,$administrador,$operator,$id_client,$id_field)";
        if (!$this->db_local->query($sql))
            return false;
            return $this->db_local->insert_id();
    }
    public function update_user($id,$identification, $username, $first_name, $last_name, $email, $telephone, $mobile, $active, $notes, $administrador, $operator, $id_client, $id_field)
    {
        $sql = "UPDATE usuario SET 
                identificacion=$identification,
                usuario=$username,
                nombres=$first_name,
                apellidos=$last_name,
                correo=$email,
                telefono=$telephone,
                celular=$mobile,
                activo=$active,
                observaciones=$notes,
                administrador=$administrador,
                operador=$operator,
                id_cliente=$id_client,
                id_campo=$id_field
                WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function delete_user($id)
    {
        $sql = "DELETE FROM usuario WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function patch_update_user($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('usuario', $data);
    }
    public function patch_select_user($where=array(),$string)
    {
        $this->db_local->select($string);
        $this->db_local->from('usuario');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        $data = $this->db_local->get();
        return $data->result_array();
    }
}
