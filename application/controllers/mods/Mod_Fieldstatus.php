<?php
class Mod_Fieldstatus
{
    protected  $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Device');

    }
    public function field_status($ID,$SD,$SDR,$USER_ID=1,$PARAMETERS = "{}")
    {
        $info = Array();
        $data = Array();
        if((new Mod_Permission())->canConsultField($USER_ID,$ID) || $SD==2){
            
            if($SD==2){
                $res = (new Mod_Views())->getFieldStatusDevice($USER_ID);
                if($res!="")$info = Array();
                else $info = $this->CI->Model_Device->select_info_devices($res);  
            }else {
                $info = $this->CI->Model_Device->select_Info_device($ID);
            }
            $infoCount = count($info);
            $eIds = Array();
            // ids de los equipos
            for ($i=0; $i<$infoCount; $i++) {
                $eIds[] = $info[$i]['e_id'];
            }
            // estado de los equipos
            for ($i=0; $i<$infoCount; $i++) {
                $info[$i]['state'] = $this->CI->Model_Device->select_state_device($eIds[$i]);
            }
            $varInfo = (new Mod_Views())->getFieldStateConfiguration($USER_ID,$ID,$eIds);
            $varInfoCount = count($varInfo);
            $showType=false;
            $showState=false;
            $showCommType=false;
            $showFabricante=false;
            for ($i=0; $i<$infoCount; $i++) {
                $var2Table = Array();
                $vi =  (new Mod_Variable())->getVarInfo($info[$i]['e_id'],false);
                for ($j=0; $j<$varInfoCount; $j++) {
                if ( $varInfo[$j]['field']==true ) {
                    if($varInfo[$j]['vId']!=-1 && $varInfo[$j]['vId']!=-2 && $varInfo[$j]['vId']!=-3 && $varInfo[$j]['vId'] != -4 )
                    {
                        $lLow=0;
                        $lLowLow=0;
                        $lHigh=0;
                        $lHighHigh=0;
                        $k=0;
                        $found=0;
                        while ($k<count($vi) && $found==0) 
                        {
                          if($vi[$k]['vId']==$varInfo[$j]['vId'])
                          {
                            $lLow=$vi[$k]['vLow'];
                            $lLowLow=$vi[$k]['vLow_Low'];
                            $lHigh=$vi[$k]['vHigh'];
                            $lHighHigh=$vi[$k]['vHigh_High'];
                            $found=1;
                          } 
                          $k++;
                        }     
                        if($SDR==0)
                        {
                            $varValue= (new Mod_Register())->getLastData($info[$i]['e_id'],$info[$i]['e_last_date'],$varInfo[$j]['vId']);
                        }
                        else
                        {
                            $d1=date('Y-m-d 00:00:00', strtotime(' -'.$SDR.' day'));
                            $d2=date('Y-m-d 23:59:59'); 
                            $varValue=(new Mod_Register())->getDataAvg($info[$i]['e_id'],$d1,$d2,$varInfo[$j]['vId']);
                            if($SDR==1) $info[$i]['e_last_date']= "Last 24h average";
                            elseif($SDR==7) $info[$i]['e_last_date']= "Last week average";
                            elseif($SDR==30) $info[$i]['e_last_date']= "Last month average";
                        }
                        
                        $var2Table[] = Array('vId'=>$varInfo[$j]['vId'],'vName'=>$varInfo[$j]['vName'],'vFabricante'=> $varInfo[$j]['vFabricante'] ,'vTooltip'=>$varInfo[$j]['vTooltip'], 'vLow_Low'=>$lLowLow,'vLow'=>$lLow,'vHigh_High'=>$lHighHigh,'vHigh'=>$lHigh,'vDetail'=>$varInfo[$j]['vDetail'],'vDetailType'=>$varInfo[$j]['vDetailType'],
                        'data'=> $varValue);
                        }
                        else
                        {
                            if($varInfo[$j]['vId']==-1) $showType=true;
                            if($varInfo[$j]['vId']==-2) $showState=true;
                            if($varInfo[$j]['vId']==-4) $showFabricante=true;
                            if($varInfo[$j]['vId']==-3) $showCommType = true;
                        }
                  }
                }
                $info[$i]['varValues'] = $var2Table;
              }
              $data = Array('varInfo'=>$info,'showType'=>$showType, 'showFabricante' => $showFabricante,'showState'=>$showState,'showCommType'=>$showCommType);
        }
        return array('STATUS_CODE' => 1, 'STATUS_DESCRIPTION' => RESPONSE::$STATUS[1],'DATA' =>$data);
    }
        
}
