<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banned extends Controller{
	function index(){
		if(!$this->userdata->get('admin_ban')){
			return redirect('login');
		}
		
		$this->load->language('admin/login');
		$sub_title=$this->lang->line("banned_title");
		$this->load->view('banned',get_defined_vars());
	}
}