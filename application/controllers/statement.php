<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Statement extends Controller {
	var $view = 'statement';
	var $table = 'address';
	
	function index() {
		$this->load->library('cart');
		$this->load->database ();
		$username=$this->userdata->get("userName");
		$passWord=$this->userdata->get("passWord");
		$rsname=$this->db->select("*")->get_where("users",array('username'=>$username,'password'=>$passWord));
		$users=$rsname->row();
				$where = array (
						'show' => 1,
						'type_id'=>$users->id
				);
				$this->db->order_by ( "timeline", "desc" );
				$member = $this->db->get_where ( "address", $where)->result ();
		$this->load->view ( $this->view, get_defined_vars () );
	}
}