<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Flv extends Controller{
	function index(){
		$this->load->helper('back');
		$flv=$this->uri->segment(3,'');
		$flv=decode_segment($flv);
		$this->load->view('flv',get_defined_vars());
	}
	
	function outer(){
		$this->load->helper('back');
		$flv=$this->uri->segment(3,'');
		$flv=decode_segment($flv);
		$n=strpos($flv,'embed ');
		if($n>0&&$n<10){
			$is_outer=true;
			$this->load->view('flv',get_defined_vars());
		}else{
			redirect($flv);
		}
	}
}