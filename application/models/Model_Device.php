<?php
class Model_Device extends CI_Model
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
    public function select_device($id)
    {
        $sql = "SELECT e.*,CASE e.activo WHEN 1 THEN 'Active' WHEN -1 THEN 'No alerts (Halted)' WHEN 0 THEN 'Disabled' ELSE 'ND' END AS estado_nombre, t.nombre AS tipo, c.nombre AS cliente, f.nombre AS campo, l.nombre AS locacion
                FROM cliente c,equipo e
                LEFT JOIN campo f ON f.id=e.id_campo 
                LEFT JOIN locacion l ON l.id=e.id_locacion 
                LEFT JOIN r_equipo_tipo t ON t.id=e.id_tipo 
                WHERE e.id=$id AND c.id=e.id_cliente";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_devices($filtros,$limit,$order,$keys)
    {
        $sqlselect = " SELECT e.*, CASE e.activo WHEN 1 THEN 'Active' WHEN -1 THEN 'No alerts (Halted)' WHEN 0 THEN 'Disabled' ELSE 'ND' END AS estado_nombre,  t.nombre AS tipo, c.nombre AS cliente, f.nombre AS campo, l.nombre AS locacion 
                    FROM equipo e 
                    LEFT JOIN cliente c ON c.id=e.id_cliente 
                    LEFT JOIN campo f ON f.id=e.id_campo 
                    LEFT JOIN locacion l ON l.id=e.id_locacion 
                    LEFT JOIN r_equipo_tipo t ON t.id=e.id_tipo";
        $sqlwhere  = "";
        return $this->db_local->query(integrar_api_sql($sqlselect,$sqlwhere,$keys,$filtros,$limit,$order))->result_array();
    }
    public function insert_device($name,$id_client,$id_field,$id_cluster,$id_class,$pod,$id_type,$description,$manufacturer,$referencia,$modbus_tipo,$modbus_id,$tcp_ip,$tcp_puerto,$latitude,$longitude,$id_varencendido,$id_varfalla,$id_varscada_start,$id_varscada_stop,$id_varfrecuencia,$id_varfrecuencia_ref,$id_varcorriente,$id_varvoltaje,$id_varmotortemp,$id_varpip,$id_varvibracion,$conection_alerts,$alert_sound,$energy_skid,$replica,$active,
    $id_father)
    {
        $sql = "INSERT equipo(id_cliente,id_campo,id_locacion,id_tipo,id_clase,pod,nombre,descripcion,fabricante,referencia,modbus_tipo,modbus_id,tcp_ip,tcp_puerto,latitud,longitud,id_varencendido,
                id_varfalla,id_varscada_start,id_varscada_stop,id_varfrecuencia,id_varfrecuencia_ref,id_varcorriente,id_varvoltaje,id_varmotortemp,id_varpip,id_varvibracion,alerta_conexion,
                alerta_sonido,energia_skid,replica,activo,id_padre)
                VALUES($id_client,$id_field,$id_cluster,$id_type,$id_class,$pod,$name,$description,$manufacturer,$referencia,$modbus_tipo,$modbus_id,$tcp_ip,$tcp_puerto,$latitude,$longitude,$id_varencendido,$id_varfalla,$id_varscada_start,$id_varscada_stop,$id_varfrecuencia,$id_varfrecuencia_ref,$id_varcorriente,$id_varvoltaje,$id_varmotortemp,$id_varpip,$id_varvibracion,$conection_alerts,$alert_sound,$energy_skid,$replica,$active,$id_father)";
        if (!$this->db_local->query($sql))
            return false;
            return $this->db_local->insert_id();
    }
    public function update_device($id,$name,$id_client,$id_field,$id_cluster,$id_class,$pod,$id_type,$description,$manufacturer,$referencia,$modbus_tipo,$modbus_id,$tcp_ip,$tcp_puerto,$latitude,$longitude,$id_varencendido,$id_varfalla,$id_varscada_start,$id_varscada_stop,$id_varfrecuencia,$id_varfrecuencia_ref,$id_varcorriente,$id_varvoltaje,$id_varmotortemp,$id_varpip,$id_varvibracion,$conection_alerts,$alert_sound,$energy_skid,$replica,$active,
    $id_father)
    {
        $sql = "UPDATE equipo SET 
                id_cliente=$id_client,
                id_campo=$id_field,
                id_locacion=$id_cluster,
                id_tipo=$id_type,
                id_clase=$id_class,
                pod=$pod,
                nombre=$name,
                descripcion=$description,
                fabricante=$manufacturer,
                referencia=$referencia,
                modbus_tipo=$modbus_tipo,
                modbus_id=$modbus_id,
                tcp_ip=$tcp_ip,
                tcp_puerto=$tcp_puerto,
                latitud=$latitude,
                longitud=$longitude,
                id_varencendido=$id_varencendido,
                id_varfalla=$id_varfalla,
                id_varscada_start=$id_varscada_start,
                id_varscada_stop=$id_varscada_stop,
                id_varfrecuencia=$id_varfrecuencia,
                id_varfrecuencia_ref=$id_varfrecuencia_ref,
                id_varcorriente=$id_varcorriente,
                id_varvoltaje=$id_varvoltaje,
                id_varmotortemp=$id_varmotortemp,
                id_varpip=$id_varpip,
                id_varvibracion=$id_varvibracion,
                alerta_conexion=$conection_alerts,
                alerta_sonido=$alert_sound,
                energia_skid=$energy_skid,
                replica=$replica,
                activo=$active,
                id_padre=$id_father  WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function delete_user($id)
    {
        $sql = "DELETE FROM equipo WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function patch_update_device($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('equipo', $data);
    }
    public function patch_select_device($where=array(),$string,$join="",$group=array(),$order="",$limit="")
    {
        $this->db_local->select($string);
        $this->db_local->from('equipo');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        if($join!=""){
            $this->db_local->join($join);
        }
        if(count($group)!=0){
            $this->db_local->group_by($group);
        }
        if($order!=""){
            $this->db_local->order_by($order);
        }
        if($limit!=""){
            $this->db_local->limit($limit);
        }
        return $this->db_local->get()->result_array();
    }
    /** */
        public function select_Info_device($fieldId)
        {
            $USER_CLIENT_ID = 0;
            $USER_ID = 1;
            $sql = "SELECT e.id AS e_id, e.id_clase AS e_id_class, e.nombre AS e_name, e.estado_fecha AS e_date, e.ultima_fecha AS e_last_date "
        . " ,e.latitud AS e_lat, e.longitud AS e_lon, e.modbus_tipo AS e_ct,e.fabricante AS e_fabricante "
        . " ,e.id_padre,e.id_varencendido AS e_id_encendido,e.id_varcorriente AS e_id_amps,e.id_varvibracion AS e_id_vib, e.id_varvoltaje AS e_id_volts,e.id_varpip AS e_id_pip, e.id_varmotortemp AS e_id_motortemp, e.id_varfrecuencia AS e_id_varfreq, e.detalle AS e_detail, e.activo AS e_active "
        . " ,e.ramp_state AS e_ramp_state,e.ramp_target AS e_ramp_target,e.ramp_delta_value AS e_ramp_deltaf,e.ramp_delta_time AS e_ramp_deltat "
        . " ,ret.nombre AS e_type_name "
        . " ,CASE e.activo WHEN 1 THEN 3 WHEN -1 THEN 2 ELSE 1 END AS e_order "
        . " FROM equipo e, r_equipo_tipo ret WHERE "
        . " e.id_tipo=ret.id AND "
        . " e.id_campo=$fieldId  AND (".$USER_CLIENT_ID."=0 OR e.id_campo IN(SELECT id_campo FROM usuario WHERE id=".$USER_ID.") OR e.id_campo IN(SELECT id_campo FROM usuario_campo WHERE id_usuario=".$USER_ID.") OR e.id IN(SELECT id_equipo FROM usuario_equipo WHERE id_usuario=".$USER_ID.")) AND e.id_padre=-1 "
        . " ORDER BY  e_order DESC, e_id_class ASC,  e_name ASC ";
        return $this->db_local->query($sql)->result_array();
        }
        public function getDeviceInfoTree($fieldId)
        {
            
            $USER_CLIENT_ID = 0;
            $USER_ID = 1;
            $sql = "SELECT e.id AS e_id, e.id_clase AS e_id_class, " .($USER_ID<0 ?" CONCAT('Device_',CAST(e.id as CHAR(50))) " : " e.nombre "). " AS e_name, e.estado_fecha AS e_date, e.ultima_fecha AS e_last_date "
            . " ,e.latitud AS e_lat, e.longitud AS e_lon, e.modbus_tipo AS e_conn_type,e.fabricante AS e_fabricante "
            . " ,e.id_padre,e.id_varencendido AS e_id_encendido,e.id_varpip AS e_id_pip, e.id_varmotortemp AS e_id_motortemp, e.id_varfrecuencia AS e_id_varfreq, e.detalle AS e_detail, e.activo AS e_active "
            . " ,e.ramp_state AS e_ramp_state,e.ramp_target AS e_ramp_target,e.ramp_delta_value AS e_ramp_deltaf,e.ramp_delta_time AS e_ramp_deltat "
            . " ,ret.nombre AS e_type_name "
            . " ,e.id_locacion AS e_id_cluster "
            . " ,CASE WHEN e.activo=1 OR e.id_locacion>=0 THEN 3 WHEN e.activo=-1 THEN 2 ELSE 1 END AS e_order_general "
            . " ,CASE WHEN e.id_locacion>=0 THEN " . ($USER_ID < 0 ? " CONCAT('Cluster_',CAST(loc.id as CHAR(50))) " : " loc.nombre ") . " ELSE " . ($USER_ID < 0 ? " CONCAT('Device_',CAST(e.id as CHAR(50))) " : " e.nombre ") . " END AS e_cluster_name "
            . " ,CASE e.activo WHEN 1 THEN 3 WHEN -1 THEN 2 ELSE 1 END AS e_order "
            . " FROM equipo e "
            . " JOIN r_equipo_tipo ret ON e.id_tipo=ret.id  "
            . " LEFT JOIN locacion loc ON e.id_locacion=loc.id "
            . " WHERE e.id_campo=$fieldId AND (".$USER_CLIENT_ID."=0 OR e.id_campo IN(SELECT id_campo FROM usuario WHERE id=".$USER_ID.") OR e.id_campo IN(SELECT id_campo FROM usuario_campo WHERE id_usuario=".$USER_ID.") OR e.id IN(SELECT id_equipo FROM usuario_equipo WHERE id_usuario=".$USER_ID.")) AND e.id_padre=-1"
            . " ORDER BY e_order_general DESC, e_cluster_name ASC, e_order DESC, e_id_class ASC,  e_name ASC ";

            return $this->db_local->query($sql)->result_array();
        }
    public function select_info_devices($eIds)
    {
        $sql = "SELECT e.id AS e_id, e.id_clase AS e_id_class, e.nombre  AS e_name, e.estado_fecha AS e_date, e.ultima_fecha AS e_last_date "
        . " ,e.latitud AS e_lat, e.longitud AS e_lon, e.modbus_tipo AS e_ct,e.fabricante AS e_fabricante "
        . " ,e.id_padre,e.id_varencendido AS e_id_encendido,e.id_varpip AS e_id_pip, e.id_varmotortemp AS e_id_motortemp, e.id_varfrecuencia AS e_id_varfreq, e.detalle AS e_detail, e.activo AS e_active "
        . " ,e.ramp_state AS e_ramp_state,e.ramp_target AS e_ramp_target,e.ramp_delta_value AS e_ramp_deltaf,e.ramp_delta_time AS e_ramp_deltat "
        . " ,ret.nombre AS e_type_name "
        . " , CASE e.activo WHEN 1 THEN 3 WHEN -1 THEN 2 ELSE 1 END AS e_order "
        . " FROM equipo e, r_equipo_tipo ret WHERE "
        . " e.id_tipo=ret.id AND "
        . " e.id_padre=-1 AND e.id IN ($eIds) ";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_state_device($eId) {
        $result = Array();
        $sql = "SELECT e.id AS e_id, e.estado AS e_state"
        .", e.estado_fecha AS e_date, e.estado_descripcion AS e_description, e.activo AS e_active, e.alerta_sonido AS e_alert_sound, e.estado_alerta_sonido AS e_state_alert_sound "
        ." FROM equipo e WHERE e.id=$eId";
        $result = $this->db_local->query($sql)->result_array();
        if ( count($result)>0 ) {
          $active=$result[0]['e_active'];
          $state=$result[0]['e_state'];
          $stateStr='Code:'.$state;
          if ( $active==1 ) {
            switch($state) {
            case 2: $stateStr='Alert: '.$result[0]['e_description'];              break;
            case 1: $stateStr='On';                 break;
            case 0: $stateStr='Off';                break;
            case -1:$stateStr='IP error';           break;
            case -2:$stateStr='Serial error';       break;
            }
          } else if ( $active==0 ) {
            $stateStr="Disabled";
          } else {
            $stateStr="Halted";
          }
          $result[0]['e_stateStr']=$stateStr;
        }
        return $result;
      }
}
