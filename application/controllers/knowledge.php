<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Knowledge extends Controller{
	var $title='knowledge';
	var $view='knowledge';
	var $table='download';
	
	function Knowledge(){
		parent::Controller();
		$this->load->model("dataset_atom","my_model");
		$this->my_model->table_name($this->table);
	}
	
	function index(){
		$this->load->library('pager');
		$this->load->database();
		$where=array('show'=>1);
		$page=intval($this->uri->segment(3,1));
		$each=8;
		$this->pager->init(array(
			'table'=>$this->my_model->table_name(),
			'link'=>site_url("{$this->title}/index/{page}"),
			'where'=>$where,
			'page'=>$page,
			'each'=>$each,
		));
		$this->db->order_by("sort_id","desc");
		list($page_link,$data,$page,$total)=$this->pager->create_link();
		
		$this->load->view($this->view,get_defined_vars());
	}
}