<?php

class CI_Siteview{
	var $db;
	var $_ci;
	
	function CI_Siteview($config=array()){
		$this->_ci=&get_instance();
		if(is_array($config)&&count($config)>0){
			$this->init($config);
		}
		$this->header_seo();
		$this->header_content();
        $this->user_name();
	}
	
	function init($config){
		foreach($config as $k=>$v){
			if(isset($this->$k)){
				$this->$k=$v;
			}
		}
	}
	
	//SEO调用
	 function header_seo(){
    	$dir_file = $_SERVER['SCRIPT_NAME'];
   	    $filename = basename($dir_file);
   	    $filename=explode('.', $filename);
   	    $filename=trim($filename[0]);
   	    
		$this->ready_database();
		$this->db->select('ktitle,intro,content')->order_by('timeline desc');
		$q=$this->db->get_where('seo',array('title'=>$filename))->row_array();
		$this->_ci->load->vars(array('seo'=>$q));
	}
	function header_content(){
		$this->ready_database();
		$this->db->select("config,value");
		$q=$this->db->get_where("config",array("category"=>"site"));
		$q=$q->result_array();
		$a=array();
		foreach($q as $r){
			$a[$r['config']]=$r['value'];
		}
		unset($q,$r);
		$this->_ci->load->vars(array(
			'site_title'=>$a['title'],
			'site_keywords'=>$a['keywords'],
			'site_code'=>$a['code'],
			'site_icpcode'=>$a['icpcode'],
			'site_weburl'=>$a['weburl'],
			'site_description'=>$a['description'],
			));
	}

    function user_name(){ //记录用户名
        $this->ready_database();
        $this->_ci->load->helper('form');
        $CI=&get_instance();
        $CI->load->library('userdata');
        $userName=$CI->userdata->get("userName");
        $passWord=$CI->userdata->get("passWord");
        $rsname=$this->db->get_where("users",array('username'=>$userName,'password'=>$passWord));
        $users=$rsname->row();
        $CI->load->library ( 'cart' );
        $this->_ci->load->vars(get_defined_vars());
    }
	
	function ready_database(){
		if(!is_object($this->db)){
			$this->_ci->load->database();
			$this->db=&$this->_ci->db;
		}
	}
}