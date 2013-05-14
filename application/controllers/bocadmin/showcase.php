<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Showcase extends Backdata_controller{

	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'名称',
			'rules'=>'required|max_length[255]'),
		array(
			'field'=>'intro',
			'label'=>'内容提要',
			'rules'=>'trim'),
		array(
			'field'=>'timeline',
			'label'=>'发布时间',
			'rules'=>'required'),
		array(
			'field'=>'show',
	        'rules'=>'intval'),
		array(
			'field'=>'content',
	        'label'=>'详细内容',
	        'rules'=>'trim'),
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