<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video extends Controller{
	var $view='video';
	var $title='video';
	var $table='video';
	
	function Video(){
		parent::Controller();
		$this->load->model("dataset_atom","my_model");
		$this->my_model->table_name($this->table);
	}
	
	function index(){
		$this->load->database();
		
		$where =array('show'=>1, 'type_id'=>1);
		$this->db->order_by('sort_id', 'DESC');
		$arr_video = $this->db->get_where($this->table, $where)->result();
		
		$this->load->view($this->view,get_defined_vars());
		
		//echo $this->db->last_query();exit;
	}
}