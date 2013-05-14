<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Intro_info extends Controller{
	var $view='intro_info';
	var $title='intro_info';
	var $table='epage';
	
	function Intro_info(){
		parent::Controller();
		$this->load->model("dataset_atom","my_model");
		$this->my_model->table_name($this->table);
	}
	
	function index(){
		$this->load->database();

        $id = $this->uri->segment(3,-1);

        $where1 = array('show'=>1, 'id'=>$id);
        $this->db->order_by('sort_id', 'DESC');
        $rs = $this->db->get_where('ebook', $where1,1)->row();

        $where = array('show'=>1, 'book_id'=>$id);
        $this->db->order_by('sort_id', 'DESC');
        $this->db->where('length(photo)>', 10, FALSE);
        $arr_data = $this->db->get_where('epage', $where)->result();

        $where2 = array('show'=>1, 'id'=>$id);
        $this->db->order_by('sort_id', 'DESC');
        $arr_photo = $this->db->get_where('epage', $where2,1)->row();
		
		$this->load->view($this->view,get_defined_vars());
	}
}