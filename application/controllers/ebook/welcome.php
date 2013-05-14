<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends Controller{
	var $table='ebook';
	var $table_child='epage';
	
	function index(){
		$this->load->database();
		$id=intval($this->uri->segment(3,-1));
		if($id<1){
			exit('期刊读取错误');
		}
		$rs=$this->db->get_where($this->table,array('id'=>$id));
		if($rs->num_rows()<1){
			exit('期刊读取错误');
		}
		$rs=$rs->row();
		
		$this->load->view("welcome",get_defined_vars());
	}
	
	function xml(){
		$id=intval($this->uri->segment(3,-1));
		if($id<1){
			exit('期刊读取错误');
		}
		$rs=$this->db->get_where($this->table,array('id'=>$id));
		if($rs->num_rows()<1){
			exit('期刊读取错误');
		}
		$rs=$rs->row();
		
		$this->db->order_by("sort_id","desc");
		$data=$this->db->get_where($this->table_child,array('book_id'=>$id));
		$data=$data->result();
		
		$this->load->view("xml",get_defined_vars());
	}
}