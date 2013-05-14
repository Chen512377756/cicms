<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 增加浏览次数，默认+1次，字段是 click
 *
 * @param string $table 表的名字，不用加前缀，比如news
 * @param int $id 要增加浏览次数的数据的id值
 * @param int $add 增加浏览的次数，默认是1（每次+1次）
 * @param string $column 增加次数的数据字段，默认是click
 */
function add_click($table,$id,$add=1,$column="click"){
	$CI=&get_instance();
	$CI->load->library('userdata');
	if(!$this->userdata->get($table.$id)){
		$CI->load->database();
		$CI->db->set($column,$column.'+'.intval($add),false);
		$CI->db->update($table,null,array('id'=>$id));
		$this->userdata->set($table.$id,1);
	}
}

function encode_segment($s){
	$CI=&get_instance();
	$CI->load->helper('js');
	$s=js_escape($s);
	return strtr(base64_encode($s),'/','|');
}

function decode_segment($s){
	$CI=&get_instance();
	$CI->load->helper('js');
	$s=strtr($s,'|','/');
	$s=@base64_decode($s);
	return js_unescape($s);
}

function goto_message($message,$uri=-1,$toSiteUrl=true){
	$message=addslashes($message);
	if(is_int($uri))$uri='history.go('.$uri.');';
	else if($toSiteUrl)$uri='location.href="'.site_url($uri).'";';
	header("Content-type:text/html;Charset=utf-8");
	exit('<script>alert("'.$message.'");'.$uri.'</script>');
}

function tag_single($id,$column='content',$strcut=-1){
	static $a=array();
	$id=intval($id);
	if(is_int($column)){
		$strcut=$column;
		$column='content';
	}
	if(!isset($a[$id])){
		$CI=&get_instance();
		$CI->load->database();
		$a[$id]=$CI->db->get_where('single',array('id'=>$id));
		if($a[$id]->num_rows()<1){
			$a[$id]=array();
		}else{
			$a[$id]=$a[$id]->row_array();
		}
	}
	if(isset($a[$id][$column])){
		if($strcut>-1){
			return strcut(strip_tags(nl2br($a[$id][$column])),$strcut);
		}
		return $a[$id][$column];
	}
	return '';
}

function tag_picture($id,$column='photo'){
	static $a=array();
	$id=intval($id);
	if(!isset($a[$id])){
		$CI=&get_instance();
		$CI->load->database();
		$a[$id]=$CI->db->get_where('picture',array('id'=>$id));
		if($a[$id]->num_rows()<1){
			$a[$id]=array();
		}else{
			$a[$id]=$a[$id]->row_array();
		}
	}
	if(isset($a[$id][$column])){
		return $a[$id][$column];
	}
	return '';
}

function tag_infos($id){
	$CI=&get_instance();
	$CI->load->database();
	$q=$CI->db->get_where('infos',array('id'=>$id));
	$data=$q->row();
	return $data;
}