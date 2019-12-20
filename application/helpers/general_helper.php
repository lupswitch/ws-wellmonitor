<?php
 function g_colorTable() {
  $list = Array('#808080','#C0C0C0','#000000','#800000','#FF0000','#800080','#FF00FF','#008000','#00FF00','#808000','#FFFF00','#000080','#0000FF','#008080','#00FFFF','#FFA500');
  return $list;
}
function g_colorFromTable($index) {
  $list = g_colorTable();
  $count = count($list);
  return $list[ abs($index%count($list)) ];
}
function generateRandomString()
{
  return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0,6);
} 
function array_msort($array, $cols)
  {
      $colarr = array();
      foreach ($cols as $col => $order) {
          $colarr[$col] = array();
          foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
      }
      $eval = 'array_multisort(';
      foreach ($cols as $col => $order) {
          $eval .= '$colarr[\''.$col.'\'],'.$order.',';
      }
      $eval = substr($eval,0,-1).');';
      eval($eval);
      $ret = array();
      foreach ($colarr as $col => $arr) {
          foreach ($arr as $k => $v) {
              $k = substr($k,1);
              if (!isset($ret[$k])) $ret[$k] = $array[$k];
              $ret[$k][$col] = $array[$k][$col];
          }
      }
      return $ret;

}
function string_secure($inp) {
  $str = "";
  if(!empty($inp) && is_string($inp)) {
    $str = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a","''"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z',"''\\"), $inp);
  }
  return "'". $str ."'";
}
function generar_pass($cant){
  $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
  $pass = array(); //remember to declare $pass as an array
  $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
  for ($i = 0; $i < $cant; $i++) {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
  }
  return implode($pass); 
}
function is_number($dato){
  $dato=trim($dato);
  if((string)$dato=="0")
    return true;
  $aux=(int)$dato;
  if($aux==0)
    return false;
  if(strlen($aux)!=strlen($dato))
    return false;
  if($aux." "!=$dato." ")
    return false;
  return true;
}
function decimal_entero($dato){
  $dato=trim($dato);
  $regexWorks = preg_match('/^[0-9]{1,15}$|^[0-9]{1,15}\.[0-9]{1,15}$/',$dato);
  return ($regexWorks === 1)?true:false;
}
function get_next_codigo28($codigo){
   $codigo= ltrim($codigo, "0");
   $aux= base_convert($codigo, 28, 10);
   $aux++;
   return strtoupper(str_pad(base_convert($aux, 10, 28), 3, "0", STR_PAD_LEFT));
}
function filter_sql($keys,$json){
  $types=array('like','<>','in','=','<=','>=','is null','is not null');
  $datos=json_decode($json,true);
  if(count2($datos)==0)
    return array('where' =>"" ,'having'=>"");
  $sqlwhere="";
  $sqlhaving="";
  for ($i=0; $i < count2($datos); $i++) {
    if(!isset($datos[$i]['key']) || !isset($datos[$i]['type']) || !isset($datos[$i]['value']))
      continue;
    $key=trim($datos[$i]['key']);
    $type=strtolower(trim($datos[$i]['type']));
    $value=trim($datos[$i]['value']);
    if(!isset($keys[$key]))
      continue;
    if(!in_array($type,$types))
      continue;
    $datos['value']=trim(string_secure($value));

    switch ($type) {
      case 'like':
        $value="'%".$value."'";
        break;
      case 'is null':
          $value="";
          break;
      case 'is not null':
          $value="";
          break;
      default:
        $value="'$value'";
        break;
    }
    if($keys[$key]['tabla']){
      if($sqlwhere!="")
      $sqlwhere.=" AND ";
      if(isset($datos[$i]['array'])){
        $arr_values=json_decode($datos[$i]['value'],true);
        $sql_arr="";
        if(count2($arr_values)>0)
          $sql_arr=" ( ";
        for ($j=0; $j < count2($arr_values) ; $j++) { 
          if($j>0)
            $sql_arr.=" ".$datos[$i]['array']." ";
          $sql_arr.=" ".$keys[$key]['key']." $type ".$arr_values[$j];
        }
        $sql_arr.=($sql_arr!="")?" ) ":"";
        $sqlwhere.=$sql_arr;
      }else{
        $sqlwhere.="  ".$keys[$key]['key']." $type $value ";
      }
    }else{
      if($sqlhaving!="")
      $sqlhaving.=" AND ";
      $sqlhaving.=" ".$keys[$key]['key']." $type $value ";
    }

  }
  return array('where' =>$sqlwhere ,'having'=>$sqlhaving);
}
function filter_parameters($arr,$parameters){
  if(count2($parameters)==0)
  return $arr;
  $is_matrix=false;
  foreach ($arr as $key => $value) {
    if(is_array($value))
      $is_matrix=true;
    break;
  }
  $arr2=array();
  if($is_matrix){
  for ($i=0; $i <count2($arr) ; $i++) {
    $arr_aux=array();
      foreach ($arr[$i] as $key => $value) {
        if(in_array($key,$parameters))
          $arr_aux[$key]=$value;
      }
      $arr2[]=$arr_aux;
  }
}else{
  foreach ($arr as $key => $value) {
    if(in_array($key,$parameters))
      $arr2[$key]=$value;
  }
}
  return $arr2;
}
function page_data($arr,$parametros){
  $arr2 = array();
  if(!isset($parametros['count']) || !isset($parametros['page']))
  return $arr;
  if(!es_numero($parametros['count']) || !es_numero($parametros['page']))
  return $arr;
  $cant_datos=count2($arr);
  $pagina=($parametros['page']==1)?0:$parametros['page'];
  $limit=($pagina-1);
  $arr2 = array_slice($arr,$limit*$parametros['count'],$parametros['count']);
  return $arr2;
}
function order_sql($keys,$parameters){
  $order=array('asc','desc');
  $json=json_decode($parameters,true);
  if(count2($json)==0)
  return "";
  $sqlorder="";
  for ($i=0; $i <count2($json); $i++) {
    if(!isset($json[$i]['key']) || !isset($json[$i]['order']))
      return "";
    $key=trim($json[$i]['key']);
    $typeorder=strtolower(trim($json[$i]['order']));
    if(!isset($keys[$key]))
      return "";
    if(!in_array($typeorder,$order))
      return "";
    if($sqlorder!="")
      $sqlorder.=" , ";
      $sqlorder.=" ".$keys[$key]['key']."  $typeorder ";
  }
  if(count2($sqlorder)!=0)
  return " ORDER BY ".$sqlorder;
}
function integrar_api_sql($sqlselect,$sqlwhere,$KEYS,$filtros,$limit,$order){
  $arrfilter=filter_sql($KEYS,$filtros);
  if($sqlwhere=="" && $arrfilter['where']!="")
  $sqlwhere.=" WHERE ";
  if($sqlwhere!=" WHERE " && $arrfilter['where']!="")
  $sqlwhere.=" AND ";
  $sqlwhere.=$arrfilter['where'];
  if($arrfilter['having']!="")
  $sqlwhere.=" HAVING ";
  $sqlwhere.=$arrfilter['having'];
  $sqllimit="";
  if($limit!="")
  $sqllimit=" LIMIT ".$limit;
  $sqlorder=order_sql($KEYS,$order);
  $sql=$sqlselect." ".$sqlwhere." ".$sqlorder." ".$sqllimit;
  return $sql;
}
function patch($KEYS,$atributo){
  $types = array('string','int');
  $json  = json_decode($atributo,true);
  $sql   = "";
  for ($i=0; $i <count2($json) ; $i++) {
    if(!isset($json[$i]["key"])||!isset($json[$i]["type"]) ||!isset($json[$i]["value"]))
        die(json_encode(array('status_code'=>3,'status_description'=>'Error logico','response_code'=>0,'response_description'=>'Faltan campos dentro del objeto a procesar')));
    $key   = trim($json[$i]["key"]);
    $type  = strtolower(trim($json[$i]["type"]));
    $value = $json[$i]["value"];
    if(!isset($KEYS[$key]))
        die(json_encode(array('status_code'=>3,'status_description'=>'Error logico','response_code'=>0,'response_description'=>'El key y/o el type no existen')));
    if(!in_array($type,$types))
        die(json_encode(array('status_code'=>3,'status_description'=>'Error logico','response_code'=>0,'response_description'=>'El key y/o el type no existen')));
    if($KEYS[$key]['type']!=$type)
        die(json_encode(array('status_code'=>3,'status_description'=>'Error logico','response_code'=>0,'response_description'=>'El tipo de dato no corresponde al campo')));
    switch ($type) {
        case 'int':
        if($value!="" && $value!="NULL" && !es_numero($value))
          die(json_encode(array('status_code'=>3,'status_description'=>'Error logico','response_code'=>0,'response_description'=>'El tipo de dato no corresponde al campo')));
          $value = $value;
          break;
        case 'string':
        if($value!="" && $value!="NULL" && !is_string ($value))
          die(json_encode(array('status_code'=>3,'status_description'=>'Error logico','response_code'=>0,'response_description'=>'El tipo de dato no corresponde al campo')));
          $value="'".$value."'";
          break;
    }
    if($sql!="")
      $sql.=" , ";
      $sql.=" ".$KEYS[$key]['key']."=".$value;
  }
    return $sql;
  }
  function eliminar_tildes($cadena){
    $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
    $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
    $texto = str_replace($no_permitidas,$permitidas,$cadena);
    return $texto;
}
function count2($arr){
    try {
      return count($arr);
    } catch (Exception $e) {
      return 0;
    }
    
  }
  function clientIp() {
    $ip = "Unknown";
    if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
      $ip = getenv("HTTP_CLIENT_IP");
    }
    elseif(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
      $ip = getenv("HTTP_X_FORWARDED_FOR");
    }
    elseif(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
      $ip = getenv("REMOTE_ADDR");
    }
    elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    else {
      $ip = "Unknown";
    }
    return $ip;
  }
  function getLatLon4Ip($ip) {
    $ipData = Array('target' => $ip);
    $response = g_doPostRequest("http://www.whereisip.net/index.php",$ipData);

    $lat = 0.0;
    $lon = 0.0;
    if ( strlen($response)>100 ) {
      $index = strripos($response, "new google.maps.LatLng(")+23;
      $d1 = substr($response, $index, 50);
      $index2 = strripos($d1, ");");
      $d2 = substr($d1,0,$index2);
      $index = strripos($d2, ",");
      $lat = substr($d2, 0,$index);
      $lon = substr($d2,$index+1);

      if ( trim($lat)=="" ) $lat = 0;
      if ( trim($lon)=="" ) $lon = 0;
    }

    $pos = Array('lat' => $lat,'lon' => $lon);
    return $pos;
  }
?>
