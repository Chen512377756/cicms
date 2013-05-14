<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Message extends Controller {
	var $title='message';
	var $view='message';
	var $table='feedback';
	
	var $type_id=1;

	var $rule_post=array(
		array(
			'field'=>'username',
			'label'=>'姓名',
			'rules'=>'trim|required'),
        array(
            'field'=>'phone',
            'label'=>'固话',
            'rules'=>'trim'),
        array(
            'field'=>'email',
            'label'=>'电子邮件',
            'rules'=>'trim|required'),
		array(
			'field'=>'tel',
			'label'=>'联系电话',
			'rules'=>'trim'),
		array(
			'field'=>'content',
			'label'=>'留言',
			'rules'=>'required|xss_clean'),
		array(
			'field'=>'captcha',
			'label'=>'验证码',
			'rules'=>'trim|required|max_lenx[4]|callback__check_captcha'),
	);

	function Message(){
		parent::Controller();
		$this->load->model("dataset_atom","my_model");
		$this->my_model->table_name($this->table);
	}
	
	function _check_captcha($s){
		$this->load->model('m_captcha');
		if($this->m_captcha->verify($s)){
		 	return true;
		}else{
			$this->form_validation->set_message('_check_captcha','验证码错误');
			return false;
		}
	}
	
	function _check_tel($s){
		$telrule1='/^[1][3,4,5,8][0-9]{9}$/';
		$telrule2='/^[0][1-9]{2,3}-[0-9]{6,8}$/';
		$telrule3='/^[0-9]{6,12}$/';
		if(!preg_match($telrule1,$s) && !preg_match($telrule2,$s) && !preg_match($telrule3,$s)){
			$this->form_validation->set_message('_check_tel','电话格式错误');
			return false;
		}else{
			return true;
		}
	}
	
	function index(){
		$this->load->view($this->view,get_defined_vars());
	}

	function post(){
		$this->load->library('form_validation',$this->rule_post);
		$words = '';
		if(!$this->form_validation->run()){
			foreach($this->rule_post as $r){
				if(form_error($r['field'])!=''){
					$words=strip_tags(form_error($r['field']));
					break;
				}
			}
		}else{
			$form=$this->form_validation->to_array();
			$form['timeline']=time();
			$form['type_id']=$this->type_id;
			$form['show']=1;
			unset($form['captcha']);
	
			$this->load->database();
			if(!$this->db->insert($this->table,$form)){
				$words="提交失败";
			}else{
				$ins_id=$this->db->insert_id();
				$this->db->update($this->table,array('sort_id'=>$ins_id),array('id'=>$ins_id));
				$words="suc";
			}
		}
		echo $words;
	}
}