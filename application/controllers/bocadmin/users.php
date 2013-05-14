<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Backdata_controller{
	var $title="users";
	var $view='users';
	var $table='users';
	var $rule_put=array(
		array(
			'field'=>'timeline',
	        'rules'=>'trim'),
		array(
			'field'=>'show',
	        'rules'=>'intval'),
		array(
			'field'=>'title',
	        'label'=>'姓名',
	        'rules'=>'trim|required|max_length[100]'),
		array(
			'field'=>'tel',
	        'label'=>'电话',
	        'rules'=>'trim|required|max_length[50]'),
		array(
			'field'=>'email',
	        'label'=>'邮箱',
	        'rules'=>'trim|required|valid_email|max_length[50]'),
		array(
			'field'=>'addr',
	        'label'=>'地址',
	        'rules'=>'trim|required'),
		array(
			'field'=>'content',
	        'label'=>'详细内容',
	        'rules'=>'trim'),
	);
	
	function _check_account($s){
		$type_id=$this->uri->segment(3,1);
		$this->load->database();
		$this->db->select('id');
		$q=$this->db->get_where($this->table,array('username'=>$s,'type_id'=>$type_id),1);
		$data=$q->result_array();
		if($data){
			$this->form_validation->set_message("_check_account","这个用户名已经有了，请换一个");
			return false;
		}
		return true;
	}
	
	function Users(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->model('dataset_atom','my_model');
		$this->load->model('dataset_flow','my_flow');
		$this->my_model->table_name($this->table);
		$this->my_flow->set_atom($this->my_model);
		$this->my_flow->set_ctrl($this);
	}
	
	function on_load(){
		$this->view=$this->table=strtolower(get_class($this));
	}
	
	function post(){
		$backward='create';
		$this->load->database();
		
		$type_id=$this->uri->segment(3,1);
		
		$post_info[0]=array(
			'field'=>'username',
			'label'=>'用户名',
			'rules'=>'trim|required|callback__check_account');
		$post_info[1]=array(
			'field'=>'password',
			'label'=>'密码',
			'rules'=>'trim|required');
		$post_info[2]=array(
			'field'=>'passconf',
	        'label'=>'重复密码',
	        'rules'=>'trim|required|matches[password]');
		
		$post_info=array_merge($post_info,$this->rule_put);
		$this->load->library('form_validation',$post_info);
		if(!$this->form_validation->run()){
			return $this->$backward();
		}
		$form=$this->form_validation->to_array();
		$form['type_id']=$type_id;
		$form['timeline']=strtotime($form['timeline']);
		$form['click']=0;
		$form['password']=md5($form['password']);
		unset($form['passconf']);
		
		if(!$this->my_model->post($form)){
			$this->form_validation->set_error('title','item_put_failure');
			return $this->$backward();
		}
		
		$this->backview->success($this->lang->line('item_post_success')
			,array('继续添加'=>"ajax:{$this->title}/create/{$type_id}"
				,'完成'=>"javascript:location.reload();"));
	}
	
	function put(){
		$backward='edit';
		
		$type_id=$this->uri->segment(3,1);
		$id=$this->uri->segment(4,1);
		$this->load->database();
		$this->my_model->table_name($this->table);
		if(!($rs=$this->my_model->get($id))){
			return goto_message($this->lang->line('item_not_found'),'index');
		}
		$put_info[0]=array(
			'field'=>'password',
			'label'=>'密码',
			'rules'=>'trim');
		$put_info[1]=array(
			'field'=>'passconf',
	        'label'=>'重复密码',
	        'rules'=>'trim|matches[password]');
		
		$put_info=array_merge($put_info,$this->rule_put);
		$this->load->library('form_validation',$put_info);
		if(!$this->form_validation->run()){
			return $this->$backward();
		}
		$form=$this->form_validation->to_array();
		if(!empty($form['password'])){
			$form['password']=md5($form['password']);
		}else{
			unset($form['password']);
		}
		unset($form['passconf']);
		$form['timeline']=strtotime($form['timeline']);
		
		if(!$this->my_model->put($id,$form)){
			$this->form_validation->set_error('title','item_put_failure');
			return $this->$backward();
		}
		
		$this->backview->success($this->lang->line('item_put_success')
			,array('继续添加'=>"ajax:{$this->title}/create/{$type_id}"
				,'完成'=>"javascript:location.reload();"));
	}
}