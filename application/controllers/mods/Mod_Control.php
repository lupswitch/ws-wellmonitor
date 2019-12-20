<?php
class Mod_Control
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Device');
        $this->CI->load->model('Model_Variable');
        $this->CI->load->model('Model_Global');
    }
    public function controlData($EID, $USER_ID = 1, $PARAMETERS = "{}")
    {
        $result = array();
        $success = -1;
        $EID = (new Mod_Permission())->canConsultDevice($USER_ID, $EID) ? $EID : 0;
        if ($EID > 0) {
            $success = 1;
            $device = $this->CI->Model_Device->select_device($EID);
            $eType = $device[0]['id_clase'];
            if ($eType == 3 || $eType == 4 || $eType == 7) {
                $result['varTable'] = @file_get_contents($this->CI->config->item('base_url') . '/hmi/hmi_alkhorayef.html');
                $result['eClass'] = $eType;
            } elseif ($eType == 5) {
                $result['eClass'] = $eType;
                $res = (new Mod_Register())->getLastDataFoundEMAS($EID);
            } else {
                $result['varRefInfo'] = $this->CI->Model_Variable->get_var_ref_info($EID);

                $info = $this->CI->Model_Device->select_device($EID);
                $result['eClass'] = $info[0]['id_clase'];
                $result['idVarScadaStart'] = (isset($info [0]['id_varscada_start']))?$info [0]['id_varscada_start']:"";
                $result['idVarScadaStop'] = (isset($info [0]['id_varscada_stop']))?$info [0]['id_varscada_stop']:"";
                $result['idVarFreqRef'] = (isset($info [0]['id_varfrecuencia_ref']))?$info [0]['id_varfrecuencia_ref']:"";
                $result['rampState'] = (isset($info [0]['ramp_state']))?$info [0]['ramp_state']:"";
                $result['rampTarget'] = (isset($info [0]['ramp_target']))?$info [0]['ramp_target']:"";
                $result['rampDeltaF'] = (isset($info [0]['ramp_delta_value']))?$info [0]['ramp_delta_value']:"";
                $result['rampDeltaT'] = (isset($info [0]['ramp_delta_time']))?$info [0]['ramp_delta_time']:"";

                $result['valueVarScadaStart'] = 1;
                $var_value = $this->CI->Model_Variable->getVarDetails($EID, $result['idVarScadaStart']);
                if (count($var_value) > 0) $result['valueVarScadaStart'] = $var_value['valor'];
                $result['valueVarScadaStop'] = 1;
                $var_value = $this->CI->Model_Variable->getVarDetails($EID, $result['idVarScadaStop']);
                if (count($var_value) > 0) $result['valueVarScadaStop'] = $var_value['valor'];
                $result['cE'] = false;
                if (K_USE_RAND_VALUES)
                    $result['cE'] = true;
                $result['cE'] = (new Mod_Permission())->canEditDevice($USER_ID, $EID);
                $id = $info[0]['id_varencendido'];
                $result['runStatus'] = (new Mod_Register())->getLastData($EID, $info[0]['ultima_fecha'], $id);
                $result['runStatus']['vId'] = $id;
                $id = $info[0]['id_varfrecuencia'];
                $result['runFrec']['vId'] = $id;
                $result['runFrec']['value'] = (new Mod_Register())->getLastData($EID, $info[0]['ultima_fecha'], $id);
                $varInfoCount = count($result['varRefInfo']);
                for ($i = 0; $i < $varInfoCount; $i++) {
                    $varData = $this->CI->Model_Global->getVarRefInfoLastData($EID, $result['varRefInfo'][$i]['vId']);
                      if($result['varRefInfo'][$i]['vDetail']==1 && $result['varRefInfo'][$i]['vWrite']==1)
                      {
                        $query=$this->CI->Model_Global->patch_select_global("equipo_variable_detalle",array("id_equipo"=>$EID,"id_variable"=>$result['varRefInfo'][$i]['vId']),"valor,detalle",array(),array(),"'valor','DESC'");
                       }             
                    $result['varInfo'] = array();
                    if ($result['eClass'] != 1) {
                        // poner en result SOLO las configuradas
                        $config = (new Mod_Views())->getControlConfiguration($USER_ID, $eType, $EID);
                        $result['varInfo']['last_date']= $info[0]['ultima_fecha'];
                        $varInfoCount = count($config['varInfo']);
                        for ($i = 0; $i < $varInfoCount; $i++) {
                            if (!$config['varInfo'][$i]['control']) continue;
                            $id = $config['varInfo'][$i]['vId'];
                            $tmpValue = (new Mod_Register())->getLastData($EID, $info[0]['ultima_fecha'], $id);
                            $result['varInfo'][] = array('vId' => $id, $tmpValue, (new Mod_Variable())->get_var_limits($EID, $id), false);
                        }
                    } else {
                        $result['varInfo']['img']= 'esp.png';
                        $result['varInfo']['valP']= $this->CI->Model_Global->getLastdataFound($EID, -51);
                        $result['varInfo']['valBSW'] = $this->CI->Model_Global->getLastdataFound($EID, -101);
                        $result['varInfo']['valOSG'] = $this->CI->Model_Global->getLastdataFound($EID, -108);
                        $result['varInfo']['valH ']= $this->CI->Model_Global->getLastdataFound($EID, -102);
                        $valFluidLevel = $this->CI->Model_Global->getLastdataFound($EID, -115);
                        $valFluidLevel2 = $this->CI->Model_Global->getLastdataFound($EID, -107);
                        if (trim($valFluidLevel2['value']) != '' && $valFluidLevel2['date'] > $valFluidLevel['date']) {
                            $valFluidLevel = $valFluidLevel2;
                        }

                        $result['varInfo']['valPDP'] = (new Mod_Register())->getLastData($EID, $info[0]['ultima_fecha'], 19);
                        $result['varInfo']['valPIP']= (new Mod_Register())->getLastData($EID, $info[0]['ultima_fecha'], 11);
                        $result['varInfo']['valPIP2'] = (new Mod_Register())->getLastData($EID, $info[0]['ultima_fecha'], -114);
                        if (trim($result['varInfo']['valPIP2']['value']) != '' && $result['varInfo']['valPIP2']['date'] > $result['varInfo']['valPIP']['date']) {
                            $result['varInfo']['valPIP'] =$result['varInfo']['valPIP2'];
                        }
                         $result['varInfo']['valPIT'] = (new Mod_Register())->getLastData($EID,$info[0]['ultima_fecha'], 3);
                         $result['varInfo']['valMT'] = (new Mod_Register())->getLastData($EID,$info[0]['ultima_fecha'], 5);
                         $result['varInfo']['valVIB'] = (new Mod_Register())->getLastData($EID,$info[0]['ultima_fecha'], $info[0]['id_varvibracion']);
                         $result['varInfo']['valMAmps'] = (new Mod_Register())->getLastData($EID,$info[0]['ultima_fecha'], $info[0]['id_varcorriente']);
                         $result['varInfo']['valMVolts'] = (new Mod_Register())->getLastData($EID,$info[0]['ultima_fecha'], $info[0]['id_varvoltaje']);
                         $result['varInfo']['valMFreq'] = (new Mod_Register())->getLastData($EID,$info[0]['ultima_fecha'], (K_SITE_KEY == "sims") ? 17 : 12);
                         $result['varInfo']['valReservoirPressure'] = $this->CI->Model_Global->getLastdataFound($EID, -106);
                         $result['varInfo']['valPWF'] = $this->CI->Model_Global->getLastdataFound($EID, -100);
                         $result['varInfo']['valTDV_H'] = $this->CI->Model_Global->getLastdataFound($EID, -102);
                         $result['varInfo']['valTDV_2'] = $this->CI->Model_Global->getLastdataFound($EID, -103);
                         $result['varInfo']['valTDV_3'] = $this->CI->Model_Global->getLastdataFound($EID, -104);
                    }
                }
            }
        }
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA'=>$result);
    }
}
