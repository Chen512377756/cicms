<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends Backdata_controller{
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'产品名称',
			'rules'=>'required|max_lenx[255]'),
		array(
			'field'=>'show',
			'rules'=>'intval'),
		array(
			'field'=>'ex1',
			'rules'=>'trim'),
		array(
			'field'=>'ex2',
			'rules'=>'trim'),
		array(
			'field'=>'ex3',
			'rules'=>'trim'),
        array(
            'field'=>'ex4',
            'rules'=>'trim'),
        array(
            'field'=>'ex5',
            'label'=>'单价',
            'rules'=>'required|trim'),
		array(
			'field'=>'content',
			'label'=>'产品说明',
			'rules'=>'trim'),
		array(
			'field'=>'timeline',
			'rules'=>'trim'),
		);
	var $search_post=array(
		array(
			'field'=>'title',
			'label'=>'标题',
			'rules'=>'trim'),
		array(
			'field'=>'timeline',
			'rules'=>'trim'),
		array(
			'field'=>'expire',
			'rules'=>'trim'),
	);	
	
	function on_load(){
		$this->view=$this->table=strtolower(get_class($this));
		$this->table_type=$this->table.'_type';
		$this->model_name='product_atom';
	}
	
	function on_index(){
		return array('type_column'=>'type1');
	}

	function searchproduct(){
		$type_id=$this->uri->segment(3,-1);
		$this->backview->view("ajax/{$this->view}_searchproduct",get_defined_vars());
	}
	function searchproductpost(){
		$type_id=$this->uri->segment(3,-1);
		$this->load->database();
		$this->load->library('form_validation',$this->search_post);
		if(!$this->form_validation->run()){
			return $this->index();
		}
		$form=$this->form_validation->to_array();
		$form=$this->uri->encode(serialize($form));
		//var_dump($form);exit;
		$type_id=$this->uri->segment(3,-1);
		redirect("{$this->title}/index/$type_id/1/null/10/$form");

	}
	
	function on_post(){
		return array('rule'=>$this->rule_post);
	}
	
	function on_put(){
		return array('rule'=>$this->rule_post);
	}
	
	function on_moveup(){
		//产品的分类字段是type1~type6，所以不能用type_id限制条件
		return array('where'=>array("type_id"=>-1));
	}
	
	function on_movedown(){
		//产品的分类字段是type1~type6，所以不能用type_id限制条件
		return array('where'=>array("type_id"=>-1));
	}
}