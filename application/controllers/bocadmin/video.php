<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video extends Backdata_controller{
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'标题',
			'rules'=>'trim|required'),
		array(
			'field'=>'url',
			'label'=>'站外视频链接',
			'rules'=>'trim'),
		array(
			'field'=>'timeline',
			'label'=>'发布时间',
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