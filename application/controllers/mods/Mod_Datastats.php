<?php
class Mod_Datastats
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Device');
    }
    public function tableDataStats($EID,$D1,$D2,$USER_ID,$PARAMETERS = "{}") {
          if ((new Mod_Permission())->canConsultDevice($USER_ID,$EID)) {
            $device = $this->CI->Model_Device->select_device($EID);
            $eType = $device[0]['id_tipo'];
            $varInfo = (new Mod_Views())->getTableConfiguration($USER_ID,$eType,$EID,0,105);
            $countVarInfo = count($varInfo);
            $varIds = Array();
            for ($i=0; $i<$countVarInfo; $i++) {
              if ( $varInfo[$i]['table'] )
              {
                $varIds[] = $varInfo[$i]['vId'];
                
              }
            }
            $dt1 = date('Y-m-d 00:00:00', strtotime(' -'.$D1.' day'));
            $dt2 = date('Y-m-d 23:59:59'); 
            $dataP = (new Mod_Register())->getDataStatsSmall($EID,$dt1,$dt2,implode(",",$varIds));
            $dt1   = date('Y-m-d 00:00:00', strtotime(' -'.$D2.' day'));
            $dataS = (new Mod_Register())->getDataStatsSmall($EID,$dt1,$dt2,implode(",",$varIds));
            $dataLast = (new Mod_Register())->getLastDataVars($EID,$varIds);
            $data = array("datap"=>$dataP,"datas"=>$dataS,"datalast"=>$dataLast,"varInfo"=>$varInfo);
            return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$data);
          }
       
      }
        
}
