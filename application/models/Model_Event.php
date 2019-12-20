<?php
class Model_Event extends CI_Model
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
    public function select_event($id)
    {
        $sql="SELECT "
      ." m.id AS id, m.dt AS dt, m.imei AS imei, "
      ." lat, lon, "
      ." brand_vsd,brand_bsh, bhs_integrated, "
      ." modbus_vsd,modbus_bhs,modbus_chain, "
      ." run_frec,out_volts,out_amps,motor_temp, "
      ." pit,pip,pdp,power_test,problems,conditions,"
      ." skid_photo_count,vsd_photo_count,bhs_photo_count, "
      ." cabinet_photo_count,power_photo_count,antenna_photo_count, "
      ." CONCAT(u.nombres,' ',u.apellidos) AS installer, "
      ." (CASE "
      ."   WHEN m.install_type=1 THEN 'Installation' "
      ."   WHEN m.install_type=2 THEN 'Maintenance' "
      ."   WHEN m.install_type=3 THEN 'Desactivation' "
      ."   WHEN m.install_type=4 THEN 'Terminal activation' "
      ." END) AS install_type, "
      ." (CASE "
      ."   WHEN m.comm_type=1 THEN 'ST6100' "
      ."   WHEN m.comm_type=2 THEN 'GPRS' "
      ."   WHEN m.comm_type=3 THEN 'IDP680' "
      ."   WHEN m.comm_type=4 THEN 'TCP' "
      ." END) AS device "
      ." FROM maintenance m "
      ." INNER JOIN usuario u ON u.id=m.user_id "
      ." WHERE m.id=$id ";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_events($id,$d1,$d2,$filtros,$limit,$order,$keys)
    {
        $sqlselect = "SELECT "
        ." m.id AS id, m.dt AS dt, m.imei AS imei, "
        ." CONCAT(u.nombres,' ',u.apellidos) AS installer, "
        ." (CASE "
        ."   WHEN m.install_type=1 THEN 'Installation' "
        ."   WHEN m.install_type=2 THEN 'Maintenance' "
        ."   WHEN m.install_type=3 THEN 'Uninstall' "
        ."   WHEN m.install_type=4 THEN 'Terminal activation' "
        ."   WHEN m.install_type=-1 THEN 'Active' "
        ."   WHEN m.install_type=-2 THEN 'Inactive' "
        ."   WHEN m.install_type=-3 THEN 'Stand by' "
        ." END) AS install_type, "
        ." (CASE "
        ."   WHEN m.comm_type=1 THEN 'ST6100' "
        ."   WHEN m.comm_type=2 THEN 'GPRS' "
        ."   WHEN m.comm_type=3 THEN 'IDP680' "
        ."   WHEN m.comm_type=4 THEN 'TCP' "
        ." END) AS device,"
        ." m.install_type AS event"
        ." FROM maintenance m "
        ." INNER JOIN usuario u ON u.id=m.user_id ";
        $sqlwhere  = " WHERE m.dt>='$d1' AND m.dt<='$d2' AND m.install_type <> 0 AND m.dev_id=$id "
        ." ORDER BY m.dt DESC ";
        return $this->db_local->query(integrar_api_sql($sqlselect,$sqlwhere,$keys,$filtros,$limit,$order))->result_array();
    }
    public function insert_event($id_field,$name,$description)
    {
        $sql = "";
        if (!$this->db_local->query($sql))
            return false;
            return $this->db_local->insert_id();
    }
    public function update_event($id,$id_field,$name,$description)
    {
        $sql = "";
        return $this->db_local->query($sql);
    }
    public function delete_event($id)
    {
        $sql = "DELETE FROM maintenance WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function patch_update_event($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('maintenance', $data);
    }
    public function patch_select_event($where=array(),$string)
    {
        $this->db_local->select($string);
        $this->db_local->from('maintenance');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        return $this->db_local->get();
    }
}
