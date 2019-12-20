<?php
class Mod_Datahistorical
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Device');
        $this->CI->load->model('Model_Variable');

    }
    public function tableData($EID,$D1,$D2,$DELTA,$SOURCE,$USER_ID,$PARAMETERS = "{}") {
          if ((new Mod_Permission())->canConsultDevice($USER_ID,$EID)) {
            $device = $this->CI->Model_Device->select_device($EID);
            $eType = $device[0]['id_tipo'];
            $eName = $device[0]['nombre'];
            $varInfo = (new Mod_Views())->getTableConfiguration($USER_ID,$eType,$EID,$SOURCE);
            $tmp = $varInfo;
            $countVarInfo = count($varInfo);
            $varIds = Array();
            $varNames = Array();
            $varLimitLowLow = Array();
            $varLimitLow = Array();
            $varLimitHigh = Array();
            $varLimitHighHigh = Array();
            $varDetail = Array();
            $varDetailType = Array();
            // contar las variables para la tabla
            for ($i=0; $i<$countVarInfo; $i++) {
              if ( $varInfo[$i]['table'] )
              {
                if($varInfo[$i]['vDetail']==1 && $varInfo[$i]['vDetailValue']!=""){
                  $varInfo[$i]['vDetailDescription'] = $this->CI->Model_Variable->get_var_detail($EID,$varInfo[$i]['vId'],$varInfo[$i]['vDetailType'],$varInfo[$i]['vDetailValue']);
                }
                $varIds[] = $varInfo[$i]['vId'];
                $varNames[] = $varInfo[$i]['vName'];
                $varLimitLowLow[] = $varInfo[$i]['vLow_Low'];
                $varLimitLow[] = $varInfo[$i]['vLow'];
                $varLimitHigh[] = $varInfo[$i]['vHigh'];
                $varLimitHighHigh[] = $varInfo[$i]['vHigh_High'];
                $varDetail[] = $varInfo[$i]['vDetail'];
                $varDetailType[] = $varInfo[$i]['vDetailType'];
              }
            }
            if($SOURCE==1)
            {
                $varIds[] = -52;
                $varNames[] = "POSITION";
                $varLimitLowLow[] = 0;
                $varLimitLow[] = 0;
                $varLimitHigh[] = 0;
                $varLimitHighHigh[] = 0;
                $varDetail[] = 0;
                $varDetailType[] = 0;
            }
            $low=0;
            $dtStart = new DateTime($D1); 
            $dtEnd   = new DateTime($D2); 
            if($dtEnd<=new DateTime()){
                $dtDiff  = $dtStart->diff($dtEnd); 
            }
            else {
                $dtDiff  = $dtStart->diff(new DateTime()); 
            }
            if($dtDiff->days > 7) $low=1;
            $data = array("varInfo"=>$varInfo,"vars"=>(new Mod_Variable())->objVar(),"data"=>(new Mod_Register())->getData($EID,$D1,$D2,$varIds,$DELTA,0,0,true,0,$SOURCE,$low)) ;
            return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$data);
          }
       
      }
        
}
