<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recruit extends Backdata_controller{
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'招聘岗位',
			'rules'=>'required|max_lenx[255]'),
		array(
			'field'=>'type_id',
			'rules'=>'intval'),
		array(
			'field'=>'title_require',
			'rules'=>'trim'),
		array(
			'field'=>'show',
			'rules'=>'intval'),
		array(
			'field'=>'amount',
			'rules'=>'intval'),
		array(
			'field'=>'timeline',
			'rules'=>'trim'),
		array(
			'field'=>'expire',
			'rules'=>'trim'),
		array(
			'field'=>'department',
			'rules'=>'trim'),
		array(
			'field'=>'gender',
			'rules'=>'trim'),
		array(
			'field'=>'experience',
			'rules'=>'trim'),
		array(
			'field'=>'age',
			'rules'=>'intval'),
		array(
			'field'=>'age_max',
			'rules'=>'intval'),
		array(
			'field'=>'content',
			'rules'=>'trim'),
		array(
			'field'=>'place',
			'rules'=>'trim'),
		array(
			'field'=>'edu',
			'rules'=>'trim'),
		array(
			'field'=>'major',
			'rules'=>'trim'),
		array(
			'field'=>'intro',
			'rules'=>'trim'),
        array(
            'field'=>'contents',
            'rules'=>'trim'),
		);
	
	function on_load(){
		$this->view=$this->table=strtolower(get_class($this));
		$this->table_type=$this->table.'_type';
	}
	
	function on_post(){
		return array('rule'=>$this->rule_post);
	}
	
	function on_insert(&$form){
		$form['expire']=strtotime($form['expire']);
	}
	
	function on_edit(&$rs){
		$rs->timeline=date('Y-m-d',strtotime($rs->timeline));
		$rs->expire=date('Y-m-d',$rs->expire);
	}
	
	function on_put(){
		return array('rule'=>$this->rule_post);
	}
	
	function on_update(&$form){
		$form['expire']=strtotime($form['expire']);
	}
}