<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Network extends Backdata_controller{
	
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'销售网点',
			'rules'=>'required|max_length[100]'),
		array(
			'field'=>'tel',
			'rules'=>'trim'),
		array(
			'field'=>'fax',
			'rules'=>'trim'),
		array(
			'field'=>'timeline',
			'label'=>'发布时间',
			'rules'=>'required'),
		array(
			'field'=>'show',
	        'rules'=>'intval'),
		array(
			'field'=>'addr',
	        'rules'=>'trim'),
		array(
			'field'=>'url',
	        'rules'=>'trim'),
		array(
			'field'=>'city_id',
	        'rules'=>'intval'),
	);
	
	function on_load(){
		$this->view=$this->table=strtolower(get_class($this));
		
		$this->load->database();
		$this->db->order_by('id','asc');
		$city=$this->db->get($this->table.'_city');
		$city=$city->result_array();
		foreach($city as $r){
			$tree[$r['id']]=$r['title'];
		}
		$this->load->vars('type_tree',$tree);
	}
	
	function on_post(){
		return array('rule'=>$this->rule_post);
	}
	
	function on_put(){
		return array('rule'=>$this->rule_post);
	}
}