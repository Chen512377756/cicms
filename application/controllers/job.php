<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job extends Controller{
	var $title='job';
	var $view='job';
	var $table='recruit';
	var $table_type='recruit_type';
	var $table_apply='recruit_apply';
	
	var $rule_post=array(
		array(
			'field'=>'name',
			'label'=>'姓名',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'gender',
			'label'=>'性别',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'marriage',
			'label'=>'婚姻',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'email',
			'label'=>'E-mail',
			'rules'=>'trim|required|valid_email|xss_clean'),
		array(
			'field'=>'nation',
			'label'=>'民族',
			'rules'=>'trim|xss_clean'),
		array(
			'field'=>'birthday',
			'label'=>'出生日期',
			'rules'=>'trim|required'),
		array(
			'field'=>'politic',
			'label'=>'政治面貌',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'birthplace',
			'label'=>'籍贯',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'edu',
			'label'=>'文化程度',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'school',
			'label'=>'毕业学校',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'major',
			'label'=>'专业',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'graduation',
			'label'=>'毕业时间',
			'rules'=>'trim|required|xss_clean'),
		array(
			'field'=>'language',
			'label'=>'外语水平',
			'rules'=>'trim|xss_clean'),
		array(
			'field'=>'position',
			'label'=>'应聘职位',
			'rules'=>'trim|xss_clean'),
		array(
			'field'=>'tel',
			'label'=>'联系电话',
			'rules'=>'trim|required|xss_clean|callback__check_tel'),
		array(
			'field'=>'intro',
			'label'=>'个人简历',
			'rules'=>'trim|required|max_length[1000]|xss_clean'),
		);
		
	function Job(){
		parent::Controller();
		$this->load->model("dataset_atom","my_model");
		$this->my_model->table_name($this->table);
		$this->load->library('category');
		$this->category->table_name($this->table_type);
		$this->load->vars('type_tree',$this->category->type_tree());
	}
	
	function index(){
		$this->load->library('pager');
		$this->load->database();
		$where=array('show'=>1);
		$page=intval($this->uri->segment(3,1));
		$each=12;
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
	
	function info(){
		$id=$this->uri->segment(3,-1);
		if($id<1){
			return goto_message($this->lang->line('item_not_found'),'index');
		}
		$this->load->database();
		if(!($rs=$this->my_model->get($id))){
			return goto_message($this->lang->line('item_not_found'),'index');
		}
		$sub_title=strip_tags($rs->title);
		$this->load->view("{$this->view}_info",get_defined_vars());
	}
	
	function apply(){
		$id=$this->uri->segment(3,1);
		if($id<1){
			return goto_message($this->lang->line('item_not_found'),'index');
		}
		$this->load->database();
		if(!($rs=$this->my_model->get($id))){
			return goto_message($this->lang->line('item_not_found'),'index');
		}
		$sub_title=strip_tags($rs->title);
		$this->load->helper('form');
        $this->load->view("{$this->view}_apply",get_defined_vars());
	}
	
	function post(){
		$id=$this->uri->segment(3,-1);
		if($id<1){
			return goto_message($this->lang->line('item_not_found'),'index');
		}
		$this->load->database();
		if(!($rs=$this->my_model->get($id))){
			return goto_message($this->lang->line('item_not_found'),'index');
		}
		$this->load->library('form_validation',$this->rule_post);
		if(!$this->form_validation->run()){
			foreach($this->rule_post as $r){
				if(form_error($r['field'])!=''){
					return goto_message(strip_tags(form_error($r['field'])),"{$this->title}/_apply/{$id}");
					break;
				}
			}
		}
		$form=$this->form_validation->to_array();
		$form['timeline']=time();
		$form['audit']=0;
		$form['recruit_id']=$rs->id;
		$form['type_id']=$rs->type_id;
		
		$upfile='userfile';
		$uptag='resume';
		if(isset($_FILES[$upfile]['name'])&&strlen($_FILES[$upfile]['name'])>1){
			$upurl=rtrim($this->config->item('upload.url'),'/\\').'/'.$uptag.'/';
			$updir=rtrim($this->config->item('upload.dir'),'/\\').'/'.$uptag.'/';
			dirmake($updir);
			
			$config=array(
				'upload_path'=>$updir,
				'allowed_types'=>$this->config->item('upload.allow.doc'),
				'max_size'=>$this->config->item('upload.max.kb'),
				'encrypt_name'=>true,
				);
			$this->load->library('upload');
			$this->upload->initialize($config);
			$result=$this->upload->do_upload($upfile);
			$image=$this->upload->data();
			if(!$result){
				@unlink($image['full_path']);
				$this->form_validation->set_error('name'
					,$this->upload->display_errors('',''));
				return $this->apply();
			}
			$form['file']=$upurl.$image['file_name'];
		}

		$this->my_model->table_name($this->table_apply);
		if(!$this->my_model->post($form)){
			$this->form_validation->set_error('name'
				,$this->lang->line('item_put_failure'));
			return $this->apply();
		}
		goto_message($this->lang->line('site_post_success'),$this->view);
	}
}