<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shortcut extends Controller{
	var $view='shortcut';
	var $caption;
	var $title;
	var $iframe;
	
	function Shortcut(){
		parent::Controller();
		$this->load->vars(array(
			'title'=>$this->title=strtolower(get_class()),
			));
		
		$this->load->model('admin_user_model','my_model');
	}
	
	function index(){
		$account=$this->backauth->get_account();
		
		if(!($rs=$this->my_model->get($account))){
			$this->backview->is_iframe_post(1);
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		$rs->shortcut=explode(',',$rs->shortcut);
		
		$this->backview->view("ajax/{$this->view}",get_defined_vars());
	}
	
	function put(){
		$backward='index';
		
		$account=$this->backauth->get_account();
		
		if(!($rs=$this->my_model->get($account))){
			$this->backview->is_iframe_post(1);
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		$form=array();
		
		$a=$this->input->post('shortcut');
		if(!is_array($a)){
			$a=array();
		}
		$form['shortcut']=join(',',$a);
		
		if(!$this->my_model->put($account,$form)){
			$this->form_validation->set_error('password','item_put_failure');
			return $this->$backward();
		}
		
		$this->backview->success($this->lang->line('item_put_success')
			,array('完成'=>"javascript:location.reload();"));
	}
}