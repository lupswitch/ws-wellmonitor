<?php
class Model_Global extends CI_Model
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
    public function patch_select_global($table,$where=array(),$string,$join=array(),$group=array(),$order="",$limit="",$or="")
    {
        $this->db_local->select($string);
        $this->db_local->from($table);
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        if($or!=""){
            $this->db_local->or_where($or);
        }
        if(count($join)!=0){
            $this->db_local->join($join[0],$join[1],$join[2]);
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
    public function sql_lasdata($eId,$vId)
    {
        $sql="SELECT id_equipo FROM equipo_variable WHERE (id_equipo=$eId OR id_equipo IN (SELECT id FROM equipo WHERE id_padre=$eId))"
            ." AND id_variable=$vId ";
        return $this->db_local->query($sql)->result_array();
    }
    public function sql_getdataAvg($eId,$vId,$d1,$d2)
    {
        $sql="SELECT AVG(valor) AS avg FROM registro WHERE id_equipo=".$eId." AND id_variable=".$vId." AND fecha BETWEEN '$d1' AND '$d2'";
	    return $this->db_local->query($sql)->result_array();
    }
    public function sql_getdata($source=0,$sqlV,$low,$eId,$d1,$d2,$idVars,$order,$delta,$count=0,$limit=0,$init=0)
    {
        $sql="SELECT x.* "
        ." FROM ( SELECT @rownum := @rownum +1 AS rownum, fecha ".($source==1? "":", max(case when id_variable=".K_RUN_STATUS_VAR_ID." then valor end) run_status ").$sqlV
        ." FROM ".($low==1?"registro_60":"registro")." JOIN ( SELECT @rownum :=0 ) r WHERE id_equipo=$eId "
        ." AND fecha BETWEEN '$d1' AND '$d2' AND (id_variable IN ($idVars) ".($source==1? "":"OR id_variable=".K_RUN_STATUS_VAR_ID."").") GROUP BY fecha $order )x "
        ." WHERE (x.rownum-1) % $delta = 0 OR (x.rownum-1) = 0 OR x.rownum = $count ".($source==1? "":"OR (x.run_status>=0 AND x.run_status<1 ) ");
        if ( $limit>0 )
          $sql = $sql." LIMIT $init,$limit";
        return $this->db_local->query($sql)->result_array();
    }
    public function sql_getDataStatsSmall($vIds,$eId,$d1,$d2)
    {
       $sql="SELECT MAX(valor) AS high, MIN(valor) AS low, AVG(valor) AS avg FROM registro WHERE id_equipo=$eId AND id_variable IN ($vIds) AND fecha BETWEEN '$d1' AND '$d2'";
       return $this->db_local->query($sql)->result_array();
    }
    public function sql_getLastDataFoundEMAS($id_clase,$id_campo,$id_locacion,$fecha)
    {
        $sql="SELECT e.id, e.nombre, r.fecha AS date "
        .",MAX(CASE WHEN r.id_variable=120 then r.valor_texto end) pot_activa"
        .",MAX(CASE WHEN r.id_variable=121 then r.valor_texto end) pot_reactiva"
        .",MAX(CASE WHEN r.id_variable=119 then r.valor_texto end) pot_aparente"
        .",MAX(CASE WHEN r.id_variable=123 then r.valor_texto end) thdv_l1"
        .",MAX(CASE WHEN r.id_variable=124 then r.valor_texto end) thdv_l2"
        .",MAX(CASE WHEN r.id_variable=125 then r.valor_texto end) thdv_l3"
        .",MAX(CASE WHEN r.id_variable=126 then r.valor_texto end) thdi_l1"
        .",MAX(CASE WHEN r.id_variable=127 then r.valor_texto end) thdi_l2"
        .",MAX(CASE WHEN r.id_variable=128 then r.valor_texto end) thdi_l3"
        .",MAX(CASE WHEN r.id_variable=122 then r.valor_texto end) freq"
        .",MAX(CASE WHEN r.id_variable=117 then r.valor_texto end) fp"
        ." FROM registro r JOIN equipo e ON r.id_equipo=e.id WHERE r.id_equipo IN (SELECT id FROM equipo WHERE id_clase=".$id_clase." AND id_campo=".$id_campo." AND id_locacion=".$id_locacion." ) "
        ." AND r.fecha='".$fecha."' GROUP BY e.id, e.nombre, r.fecha ORDER BY e.nombre";
        return $this->db_local->query($sql)->result_array();
    }
    public function getVarRefInfoLastData($eId,$vId) {
        $sql="SELECT MAX(fecha) AS fecha FROM registro WHERE id_equipo=".$eId." AND id_variable=".$vId." ";
        $result = $this->db_local->query($sql)->result_array();
        if (count($result)>0 )
        {
            $fecha = $result[0]['fecha'];
            $sql="SELECT fecha as vDate, valor_texto as vValue  "
            ." FROM registro "
            ." WHERE id_equipo=".$eId." AND id_variable=".$vId." " 
            ." AND fecha='".$fecha."'";
            $result= $this->db_local->query($sql)->result_array();
            if($result) return $result;
            else return Array("vDate"=>"","vValue"=>"");
        }
        else return Array("vDate"=>"","vValue"=>"");
      }
      public function getLastDataFound($eId, $vId) {
        $sql="SELECT fecha AS date, valor AS value FROM registro r "
        ." WHERE id_equipo=$eId AND id_variable=$vId AND fecha IN (SELECT MAX(fecha) FROM registro WHERE id_equipo=$eId AND id_variable=$vId)";
        $result=$this->db_local->query($sql)->result_array();
        if ( count($result)>0 )   return $result[0];
        else return Array('date'=>'','value'=>'');
      }
    
}

