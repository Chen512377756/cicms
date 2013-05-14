<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Denied extends Controller{
	function index(){
		$this->load->view('denied',get_defined_vars());
	}
}