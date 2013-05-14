<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Question extends Backdata_controller{
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'提问问题',
			'rules'=>'required|max_length[255]'),
		array(
			'field'=>'timeline',
			'label'=>'发布时间',
			'rules'=>'required'),
		array(
			'field'=>'show',
	        'rules'=>'intval'),
		array(
			'field'=>'recommend',
	        'rules'=>'intval'),
		array(
			'field'=>'content',
	        'label'=>'问题解答',
	        'rules'=>'required'),
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