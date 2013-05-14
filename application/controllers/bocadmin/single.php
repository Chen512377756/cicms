<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Single extends Controller{
	var $caption;
	var $title;
	var $iframe;
	
	var $view='single';
	var $table='single';
	
	var $rule_put=array(
		array(
			'field'=>'title',
			'label'=>'标题',
			'rules'=>'max_length[255]'),
		array(
			'field'=>'content',
			'label'=>'内容',
			'rules'=>'trim'),
	);
	
	function Single(){
		parent::Controller();
		list($this->caption,$nav,$sub)=$this->backview->menu_caption_index();
		$this->load->vars(array(
			'title'=>$this->title=strtolower(get_class()),
			'caption'=>$this->caption,
			'nav_index'=>$nav,
			'sub_index'=>$sub));
		
		$this->load->model('single_atom','my_model');
		$this->load->model('single_flow','my_flow');
		$this->my_model->table_name($this->table);
		$this->my_flow->set_atom($this->my_model);
		$this->my_flow->set_ctrl($this);
	}
	
	function index(){
		$vars=$this->my_flow->edit();
		if(is_array($vars)){
			return $this->backview->view("ajax/{$this->view}",$vars);
		}
		if(1==$vars){
			$this->backview->is_iframe_post(1);
			$this->backview->failure($this->lang->line('invalid_item_id'));
		}
	}
	
	function put(){
		switch($this->my_flow->put(array(
			'rule'=>$this->rule_put,
			))){
			case 1:case 3:
				return $this->index();
			case 2:
				return $this->backview->failure($this->lang->line('invalid_item_id'));
		}
		
		$this->backview->success($this->lang->line('single_put_success'));
	}
}