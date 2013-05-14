<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Picture_atom extends Model{
	function Picture_atom(){
		parent::Model();
		$this->load->database();
	}
	
	function upload(&$form,$rs=null,$key='userfile_list'){
		$b=$this->input->post('upload_clear');
		if(is_array($b)){
			$this->load->helper('upload_clear');
			foreach($b as $v){//make sure photo and thumb cleared together
				if(strpos($v,'_thumb.')){
					$v=str_replace('_thumb.','.',$v);
					if(!in_array($v,$b)){
						$b[]=$v;
					}
				}
			}
			upload_clear($b);
			foreach($b as $v){
				if(isset($rs->thumb)&&$rs->thumb==$v){
					$form['thumb']='';
				}
				if(isset($rs->photo)&&$rs->photo==$v){
					$form['photo']='';
				}
			}
		}
		$a=$this->input->post($key);
		if(is_array($a)&&count($a)>0){
			foreach($a as $v){
				$k=$this->_url_to_path($v);
				if(empty($k)||!is_file($k)){
					continue;
				}
				if(strpos($v,'_thumb')){
					$form['thumb']=$v;
				}else{
					$form['photo']=$v;
				}
			}
		}
	}
	
	function _url_to_path($url){
		$upurl=rtrim(config_item('upload.url'),'/\\').'/';
		$updir=rtrim(config_item('upload.dir'),'/\\').'/';
		if(empty($url)||is_int($url)){
			return '';
		}
		if(!strncasecmp($url,$upurl,strlen($upurl))){
			$url=$updir.substr($url,strlen($upurl));
		}
		return $url;
	}
	
	function get($id,$select='*'){
		$this->db->select($select);
		$q=$this->db->get_where($this->_table,array("id"=>$id));
		if($q->num_rows()<1){
			if($id<0){
				return false;
			}
			$q=new stdClass;
			$q->id=$id;
			$q->title='';
			$this->db->insert($this->_table,$q);
		}else{
			$q=$q->row();
		}
		return $q;
	}
	
	function put($id,$form){
		return($this->db->update($this->_table,$form,array("id"=>$id))?true:false);
	}
}