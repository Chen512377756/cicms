<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store extends Backdata_controller{
	
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'标题',
			'rules'=>'trim|required'),
		array(
			'field'=>'show',
	        'rules'=>'intval'),
	);
	
	function on_load(){
		$this->view=$this->table=strtolower(get_class($this));
	}
	
	function on_post(){
		return array('rule'=>$this->rule_post);
	}
	
	function on_put(){
		return array('rule'=>$this->rule_post);
	}
}