<?php
class Mod_Event
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Event');

    }
    public function getMaintenanceTable($EID,$D1,$D2,$USER_ID,$PARAMETERS= "{}",$FILTROS= "{}",$LIMIT="",$PAGINAR= "{}",$ORDER= "{}"){
        $arr = array();
        $KEYS = array();
        if ((new Mod_Permission())->canConsultDevice($USER_ID,$EID)) {
            $arr  = $this->CI->Model_Event->select_events($EID,$D1,$D2,$FILTROS,$LIMIT,$ORDER,$KEYS);
        }
        $arrparameters = json_decode($PARAMETERS, true);
        $response = filter_parameters($arr, $arrparameters);
        $arr = page_data($response, json_decode($PAGINAR, true));
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$arr);   
    }
        
}
