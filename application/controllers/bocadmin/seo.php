<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Seo extends Backdata_controller{
	
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'页面模块',
			'rules'=>'trim|required|max_length[255]'),
		array(
			'field'=>'ktitle',
			'label'=>'标题',
			'rules'=>'trim|max_length[1000]'),
		array(
			'field'=>'intro',
			'label'=>'关键字',
			'rules'=>'trim'),
		array(
			'field'=>'content',
			'label'=>'描述',
			'rules'=>'trim'),
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