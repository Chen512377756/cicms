<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Single_atom extends Model{
	function Single_atom(){
		parent::Model();
		$this->load->database();
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
			$q->content=$q->content2=$q->content3=$q->content4=$q->content5=$q->content6='';
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