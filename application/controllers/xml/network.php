<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Network extends Controller{
	var $view='network';
	var $table='network';
	var $table_city='network_city';
	
	function index(){
		$this->load->database();
		
		$this->db->order_by('id','asc');
		$city=$this->db->get($this->table_city);
		$city=$city->result_array();
		
		$this->db->order_by('city_id asc,sort_id desc');
		$q=$this->db->get_where($this->table,array('show'=>1));
		$q=$q->result_array();
		
		$a=array();
		foreach($q as $r){
			$a[$r['city_id']][]=$r;
		}
		foreach($city as $k=>$r){
			if(isset($a[$r['id']])){
				$city[$k]['cities']=$a[$r['id']];
			}else{
				$city[$k]['cities']=array();
			}
		}
		//var_dump($city);exit;
		unset($a,$q,$k);
		
		$this->load->view($this->view,get_defined_vars());
	}
}