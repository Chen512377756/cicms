<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//wide-char=1,ascii=0.5
function strcut($src,$cutlength,$dot='…'){
	$ret='';
	$i=$n=$ulen=0;
	$strlen=strlen($src);
	while(($n<$cutlength)&&($i<=$strlen)){
		$temp=substr($src,$i,1);
		$ascnum=ord($temp);
		if($ascnum>=224){
			$ret=$ret.substr($src,$i,3);
			$i+=3;$n++;
		}else if($ascnum>=192){
			$ret=$ret.substr($src,$i,2);
			$i+=2;$n++;
		}else if($ascnum>=65&&$ascnum<=90){
			$ret=$ret.substr($src,$i,1);
			$i++;$n++;
		}else{
			$ret=$ret.substr($src,$i,1);
			$i++;$n+=0.5;
		}
	}
	//if($strlen>$cutlength)
	if(strcmp($src,$ret))$ret.=$dot;
	return $ret;
}
function htmlchars($string){
	$string = preg_replace("/\s(?=\s)/", '', trim(strip_tags($string)));
	return str_replace("&nbsp;","",$string);
}
function myEscape($str){
	preg_match_all("/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e",$str,$r);
	$str=$r[0];
	$l=count($str);
	for($i=0;$i<$l;$i++){
		$value=ord($str[$i][0]);
		if($value<223){
			$str[$i]=rawurlencode(utf8_decode($str[$i]));
			//先将utf8编码转换为ISO-8859-1编码的单字节字符，urlencode单字节字符.
			//utf8_decode()的作用相当于iconv("UTF-8","CP1252",$v)。
		//}else{
		//	$str[$i]="%u".strtoupper(bin2hex(iconv("UTF-8","UCS-2",$str[$i])));
		//}

		}else if(DIRECTORY_SEPARATOR!='/'){
        	//red hat和一些linux服务器要注释掉下面一行，否则js getcookiex乱码
        	$str[$i] = "%u".strtoupper(bin2hex(iconv("UTF-8","UCS-2",$str[$i])));
        }
	}
	return join("",$str);
}

function myUnescape($str){
	$ret='';
	$len=strlen($str);
	for($i=0;$i<$len;$i++){
		if($str[$i]=='%'&&$str[$i+1]=='u'){
			$val=hexdec(substr($str,$i+2,4));
			if($val<0x7f)$ret.=chr($val);
			elseif($val<0x800)$ret.=chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
			else $ret.=chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
			$i+=5;
		}elseif($str[$i]=='%'){
			$ret.=urldecode(substr($str,$i,3));
			$i+=2;
		}else $ret.=$str[$i];
	}
	return $ret;
}

//符合 uri segment 标准的64编码
//在 javascript 端请用 $.base64.decodex() 解码
function myBase64Encode($s,$in_charset='UTF-8'){
	if(strtoupper($in_charset)!='UTF-8')
		$s=iconv($in_charset,'UTF-8',$s);
	return str_replace('/','|',base64_encode($s));
}

//符合 uri segment 标准的64解码
//在 javascript 端请用 $.base64.encodex() 编码
function myBase64Decode($s, $out_charset='UTF-8'){
	$s=base64_decode(str_replace('|','/',$s));
	if(strtoupper($out_charset)!='UTF-8')
		$s=iconv('UTF-8',$out_charset,$s);
	return $s;
}

//获取树形数据
function getTreeData($table,$parent_id,$data_type){
	$CI=&get_instance();
	$CI->load->database();
	$CI->db->where(array('parent_id'=>$parent_id));
	$CI->db->order_by('order_id','asc');
	$q=$CI->db->get($table);
	$rs_data=array();
	$arr_child=array();
	if($data_type==1){
		$rs_type=$q->result();
		if($rs_type){
			foreach($rs_type as $r){
				$rs_data[]=$r;
				$rs_child=getTreeData($table,$r->id,$data_type);
				if($rs_child){
					$arr_child[$r->id]=1;
					$rs_data=array_merge($rs_data,$rs_child);
				}else{
					$arr_child[$r->id]=0;
				}
			}
		}
		if($rs_data){
			foreach ($rs_data as $k => $r){
				if(isset($arr_child[$r->id])){
					$rs_data[$k]->is_havechild=$arr_child[$r->id];
				}
			}
		}
	}else{
		$rs_type=$q->result_array();
		if($rs_type){
			foreach($rs_type as $r){
				$rs_data[]=$r;
				$rs_child=getTreeData($table,$r['id'],$data_type);
				if($rs_child){
					$arr_child[$r['id']]=1;
					$rs_data=array_merge($rs_data,$rs_child);
				}else{
					$arr_child[$r['id']]=0;
				}
			}
		}
		if($rs_data){
			foreach ($rs_data as $k => $r){
				if(isset($arr_child[$r['id']])){
					$rs_data[$k]['is_havechild']=$arr_child[$r['id']];
				}
			}
		}
	}
	return $rs_data;
}
//获取真实IP
function getIP()
{
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

/**
 * 获取 IP  地理位置
 * 淘宝IP接口
 * @Return: array
 */
function getCity($ip)
{
$url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
$ip=json_decode(file_get_contents($url)); 
if((string)$ip->code=='1'){
  return false;
  }
  $data = (array)$ip->data;
return $data; 
}
