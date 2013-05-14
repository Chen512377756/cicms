<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Member extends Controller {
	var $view = 'member';
	var $table = 'order';
	function Member() {
		parent::Controller ();
		$this->load->model ( "dataset_atom", "my_model" );
		$this->my_model->table_name ( $this->table );
	}
	function index() {
		$this->load->database ();
		$username=$this->userdata->get("userName");
		$passWord=$this->userdata->get("passWord");
		$rsname=$this->db->select("*")->get_where("users",array('username'=>$username,'password'=>$passWord));
		$users=$rsname->row();
				$where = array (
						'show' => 1,
						'recommend'=>1
				);
				$this->db->order_by ( "sort_id", "desc" );
				$product = $this->db->get_where ( "product", $where)->result ();
				$this->db->order_by ( "sort_id", "desc" );
				$order = $this->db->get_where ( "order", array('user_id'=>$users->id))->result ();
		$this->load->view ( $this->view, get_defined_vars () );
	}
	function info() {
		$this->load->database ();
		$id = $this->uri->segment ( 3, - 1 );
		if ($id < 1) {
			return goto_message ( $this->lang->line ( 'item_not_found' ), 'index' );
		}
	
		if (! ($rs = $this->my_model->get ( $id ))) {
			return goto_message ( $this->lang->line ( 'item_not_found' ), 'index' );
		}
		$this->load->view ( "{$this->view}_info", get_defined_vars () );
	}
	
}