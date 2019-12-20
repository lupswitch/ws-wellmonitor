<?php
class Mod_Tree
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Cluster');
        $this->CI->load->model('Model_Device');
        $this->CI->load->model('Model_Field');
        $this->CI->load->model('Model_Global');
        $this->CI->load->model('Model_Variable');
    }
    public function fieldClient($CLIENTID,$USER_ID,$FILTROS = "{}", $PARAMETERS = "{}", $LIMIT = "", $PAGINAR = "{}", $ORDER = "{}") {
        $data = array();
        $cluster = array();
          if ((new Mod_Permission())->canConsultClient($USER_ID,$CLIENTID)) {
            $info =(new Mod_Field())->getFieldInfo($USER_ID,$CLIENTID);
            $count=count($info);
            for($i=0; $i<$count; $i++) {
              $fId = $info[$i]['f_id'];
              $info[$i]['f_cE']=(new Mod_Permission())->canEditField($USER_ID,$fId);
              $datos =$this->CI->Model_Device->getDeviceInfoTree($fId);
              for($k=0; $k<count($datos); $k++){
                     $eId = $datos[$k]['e_id'];
                     $eInfo =  $this->CI->Model_Device->select_device($eId);
                     $datos[$k]['e_state']=$eInfo[0]['estado'];
                     $datos[$k]['idVarScadaStart']=$eInfo[0]['id_varscada_start'];
                     $datos[$k]['idVarScadaStop']=$eInfo[0]['id_varscada_stop'];
                     $datos[$k]['idVarFreqRef']=$eInfo[0]['id_varfrecuencia_ref'];
                     $datos[$k]['valueVarScadaStart']=1;
                    $var_value = $this->CI->Model_Variable->getVarDetails($eId,$eInfo[0]['id_varscada_start']);
                    if (count($var_value)>0) $datos[$k]['valueVarScadaStart']=$var_value['valor'];
                    $datos[$k]['valueVarScadaStop']=1;
                    $var_value = $this->CI->Model_Variable->getVarDetails($eId,$eInfo[0]['id_varscada_stop']);
                    if (count($var_value)>0) $datos[$k]['valueVarScadaStop']=$var_value['valor'];
                  
                    $cluster[$fId][$datos[$k]['e_cluster_name']]=array("e_id_cluster"=>$datos[$k]['e_id_cluster'],"e_cluster_name"=>$datos[$k]['e_cluster_name']);
                    if(isset($cluster[$fId][$datos[$k]['e_cluster_name']])){
                      $cluster[$fId][$datos[$k]['e_cluster_name']]["devices"][]=$datos[$k];
                    }
                    $info[$i]['e_info']=array_values($cluster[$fId]);
            }
            $data =$info;
          }
        }
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1], 'DATA' =>array_msort($data, array('f_name'=>SORT_ASC)));
    }
}
