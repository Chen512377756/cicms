<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Videos extends Controller{
	var $view='videos';
	var $title='videos';
	var $table='video';
	
	function Videos(){
		parent::Controller();
		$this->load->model("dataset_atom","my_model");
		$this->my_model->table_name($this->table);
	}
	
	function index(){
		$this->load->database();
		
		$where =array('show'=>1, 'type_id'=>2);
		$this->db->order_by('sort_id', 'DESC');
		$arr_video = $this->db->get_where($this->table, $where)->result();
		
		$this->load->view($this->view,get_defined_vars());
		
		//echo $this->db->last_query();exit;
	}
}