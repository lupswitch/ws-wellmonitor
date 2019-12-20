<?php
class Model_Variable extends CI_Model
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
    public function select_variable($id)
    {
        $sql = "SELECT id,nombre,descripcion,graficable,manual,manual_tipo,mobile FROM variable WHERE id=$id";
        return $this->db_local->query($sql)->result_array();
    }
    public function select_variables($filtros,$limit,$order,$keys)
    {
        $sqlselect = "SELECT id,nombre,descripcion,graficable,manual,manual_tipo,mobile FROM variable ";
        $sqlwhere  = "";
        return $this->db_local->query($sqlselect)->result_array();
    }
    public function insert_variable($name,$graphicable,$manual,$manual_type,$mobile,$description)
    {
        $sql = "INSERT INTO variable(nombre,descripcion,graficable,manual,manual_tipo,mobile)
                VALUES($name,$description,$graphicable,$manual,$manual_type,$mobile)";
        if (!$this->db_local->query($sql))
            return false;
            return $this->db_local->insert_id();
    }
    public function update_variable($id,$name,$graphicable,$manual,$manual_type,$mobile,$description)
    {
        $sql = "UPDATE variable SET 
                nombre=$name,
                descripcion=$description,
                graficable=$graphicable,
                manual=$manual,
                manual_tipo=$manual_type,
                mobile=$mobile
                WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function delete_user($id)
    {
        $sql = "DELETE FROM variable WHERE id=$id";
        return $this->db_local->query($sql);
    }
    public function patch_update_variable($where,$data)
    {
        $this->db_local->where($where);
        return $this->db_local->update('variable', $data);
    }
    public function patch_select_variable($where=array(),$string)
    {
        $this->db_local->select($string);
        $this->db_local->from('variable');
        if(count($where)!=0){
            $this->db_local->where($where);
        }
        return $this->db_local->get();
    }
    public function get_info_var($eId,$plotFlag)
    {
        $sql = "SELECT ev.id_variable AS vId, v.nombre AS vName, v.descripcion AS vTooltip "
        .",ev.unidad  AS vUnit, ev.minimo AS vLow_Low,ev.minimo_int AS vLow,ev.maximo AS vHigh_High,ev.maximo_int AS vHigh, e.id_varencendido AS varRunStatus, e.id_varfrecuencia AS varFreq, e.id_varfrecuencia_ref AS varFreqRef, ev.detalle AS vDetail, ev.detalle_tipo AS vDetailType, ev.widget AS vWidget, v.manual AS vManual, v.manual_tipo AS vManualType,e.fabricante AS vFabricante,"
        ."(SELECT valor FROM equipo_variable_detalle WHERE id_equipo=".$eId." AND id_variable=ev.id_variable ORDER BY valor LIMIT 1) AS vDetailValue"
        ." FROM equipo_variable ev, variable v, equipo e "
        ." WHERE ev.id_variable=v.id AND ev.id_equipo=e.id AND "
        ." (ev.id_equipo=$eId OR ev.id_equipo IN (SELECT id FROM equipo WHERE id_padre=$eId )) "
        .($plotFlag? " AND v.graficable=1 ":"")
        ." ORDER BY v.nombre ";
        return $this->db_local->query($sql)->result_array();
    }
    function get_var_detail($IdDevice,$IdVar,$VarDetailType,$VarValue)
    {
      $res="ND";
      if($VarDetailType==0)
      {
        $sql="SELECT valor,detalle FROM equipo_variable_detalle WHERE id_equipo=".$IdDevice." AND id_variable=".$IdVar." AND POW(2,valor) & ".$VarValue." > 0 ORDER BY valor";
        $query = $this->db_local->query($sql);
        $count = $query->num_rows();
        if ($query->num_rows() > 0)
        {
           $i = 1;
           foreach ($query->result() as $row)
           {
              $coma = ($i<$count)?",":"";
              $res="bit(".$row->valor.") -> ".$row->detalle.$coma;
              $i++; 
           }
        }  
      }
      else
      {
        $sql="SELECT valor,detalle FROM equipo_variable_detalle WHERE id_equipo=".$IdDevice." AND id_variable=".$IdVar." AND valor=".$VarValue;
        $query = $this->db_local->query($sql);
        if ($query->num_rows() > 0)
        {
           $res="";
           foreach ($query->result() as $row)
           {
              $res=$res.$row->detalle;
           }
        }
      }
      return $res;
    }
    public function get_var_ref_info($eId) {
        $sql="SELECT ev.id_variable as vId, v.nombre as vName "
        ." ,ev.unidad as  vUnit, ev.escritura as vWrite, ev.detalle AS vDetail, ev.detalle_tipo AS vDetailType "
        ." FROM equipo_variable ev, variable v "
        ." WHERE v.id=ev.id_variable AND (ev.widget=1 OR ev.escritura=1) "
        ." AND ev.id_equipo=$eId ORDER BY v.nombre ";
        $result= $this->db_local->query($sql)->result_array();
        return $result;
      }
      public function getVarDetails($eId,$vId) {
        $sql="SELECT evd.* "
        ." FROM equipo_variable_detalle evd "
        ." WHERE evd.id_equipo=$eId AND evd.id_variable=$vId ORDER BY evd.valor";
        $result= $this->db_local->query($sql)->result_array();
       if ( count($result)>0 ) return $result[0];
        else return Array();
      }
      public function get_var_limits($eId,$vId) {
        $sql="SELECT ev.minimo,ev.minimo_int,ev.maximo,ev.maximo_int, ev.id_rango "
        ." FROM equipo_variable ev "
        ." WHERE (ev.id_equipo=$eId OR ev.id_equipo IN (SELECT id FROM equipo WHERE id_padre=$eId) ) AND ev.id_variable=$vId";
        $result= $this->db_local->query($sql)->result_array();
        return $result;
      }
}
