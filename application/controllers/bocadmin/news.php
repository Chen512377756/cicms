<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends Backdata_controller{
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'标题',
			'rules'=>'required|max_length[255]'),
		array(
			'field'=>'author',
			'label'=>'作者',
			'rules'=>'trim'),
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
			'field'=>'recommend',
	        'rules'=>'intval'),
		array(
			'field'=>'content',
	        'label'=>'新闻内容',
	        'rules'=>'required'),
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
		$this->load->model('dataset_atom','my_model');
		//$this->load->model('dataset_flow','my_flow');
		$this->load->library('category');
		$this->category->table_name('news_type');
		$type_tree=$this->category->type_tree();
		$this->load->vars(array('type_tree'=>$type_tree));
	}
	
	function on_post(){
		return array('rule'=>$this->rule_post);
	}
	function searchnews(){
		$type_id=$this->uri->segment(3,-1);
		$this->backview->view("ajax/{$this->view}_searchnews",get_defined_vars());
	}
	function searchnewspost(){
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
	
	function on_put(){
		return array('rule'=>$this->rule_post);
	}
}