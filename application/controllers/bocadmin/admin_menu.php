<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_menu extends Controller{
	var $caption='后台栏目';
	var $view='admin_menu';
	var $title;
	var $iframe;
	
	var $type_id=-1;
	
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'标题',
			'rules'=>'required|max_length[100]'),
		array(
			'field'=>'url',
			'label'=>'链接',
			'rules'=>'trim'),
		array(
			'field'=>'internal',
	        'label'=>'链接类型',
	        'rules'=>'intval'),
		array(
			'field'=>'ajax',
	        'label'=>'ajax',
	        'rules'=>'intval'),
		array(
			'field'=>'parent_id',
			'label'=>'类别',
			'rules'=>'intval'),
		array(
			'field'=>'move_des',
			'rules'=>'intval'),
		);
		
	function Admin_menu(){
		parent::Controller();
		list($this->caption,$nav,$sub)=$this->backview->menu_caption_index();
		$this->load->vars(array(
			'title'=>$this->title=strtolower(get_class()),
			'caption'=>$this->caption,
			'nav_index'=>$nav,
			'sub_index'=>$sub));
		
		$this->load->model('admin_menu_model','type_model');
		$this->load->library('category');
		$this->category->table_name($this->type_model->table_name());
	}

	// index.php?ctrl/func/type_id/page/order/each/cond
	
	function index(){
		$data=$this->category->get_all();
		$total=count($data);
		
		$this->load->view($this->view,get_defined_vars());
	}
	
	function create(){
		$id_tree=$this->_get_id_tree();
		$parent_id=$this->uri->segment(3,0);
		if($parent_id>0){
			$this->load->database();
			$this->db->select('id,is_fixed');
			$rs_create=$this->db->get_where($this->category->table_name(),array('id'=>$parent_id),1)->row();
			if($rs_create->is_fixed==1){
				$this->backview->is_iframe_post(1);
				return $this->backview->failure("该栏目为固定栏目，不可新增子类"
					,"{$this->view}/index/$type_id/$page/$order/$cond/$each");
			}
		}
		$this->backview->view("ajax/{$this->view}_create",get_defined_vars());
	}
	
	function post(){
		$backward='create';
		$parent_id=$this->uri->segment(3,0);
		if($parent_id>0){
			$this->load->database();
			$this->db->select('id,is_fixed');
			$rs_create=$this->db->get_where($this->category->table_name(),array('id'=>$parent_id),1)->row();
			if($rs_create->is_fixed==1){
				$this->backview->is_iframe_post(1);
				return $this->backview->failure("该栏目为固定栏目，不可新增子类"
					,"{$this->view}/index/$type_id/$page/$order/$cond/$each");
			}
		}
		$this->load->library('form_validation',$this->rule_post);
		if(!$this->form_validation->run()){
			return $this->$backward();
		}
		$form=$this->form_validation->to_array();
		$form['timeline']=time();
		
		$this->load->database();
		
		$this->db->select_max("id","max_id");
		$max_id=$this->db->get_where($this->category->table_name()
			,array("type_id"=>$this->type_id));
		$form['type_id']=$max_id=intval($max_id->row("max_id"))+1;
		
		//post item
		$result=$this->category->post($form);
		switch($result){
			case 1:$this->form_validation->set_error('title','数据创建失败，请重试');break;
			case 2:$this->form_validation->set_error('parent_id','类别信息读取错误');break;
			case 3:$this->form_validation->set_error('parent_id','数据更新失败，请重试');break;
		}
		if($result!=0){
			return $this->$backward();
		}
		
		$this->backview->success($this->lang->line('item_post_success')
			,array('继续添加'=>"ajax:{$this->title}/create/$parent_id"
				,'完成'=>"{$this->title}"));
	}
	
	function edit(){
		$id=$this->uri->segment(3,0);
		if($id>0){
			$this->load->database();
			$this->db->select('id,is_fixed');
			$rs_edit=$this->db->get_where($this->category->table_name(),array('id'=>$id),1)->row();
			if($rs_edit->is_fixed==1){
				$this->backview->is_iframe_post(1);
				return $this->backview->failure("该栏目为固定栏目，不可编辑"
					,"{$this->view}/index/$type_id/$page/$order/$cond/$each");
			}
		}
		$id_tree=$this->_get_id_tree();
		
		if(!($rs=$this->category->get($id))){
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		$this->backview->view("ajax/{$this->view}_edit",get_defined_vars());
	}
	
	function put(){
		$backward='edit';
		
		$id=$this->uri->segment(3,0);
		if($id>0){
			$this->load->database();
			$this->db->select('id,is_fixed');
			$rs_edit=$this->db->get_where($this->category->table_name(),array('id'=>$id),1)->row();
			if($rs_edit->is_fixed==1){
				$this->backview->is_iframe_post(1);
				return $this->backview->failure("该栏目为固定栏目，不可编辑"
					,"{$this->view}/index/$type_id/$page/$order/$cond/$each");
			}
		}
		if(!$this->category->get($id)){
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		$this->load->library('form_validation',$this->rule_post);
		if(!$this->form_validation->run()){
			return $this->$backward();
		}
		$form=$this->form_validation->to_array();
		
		$form['timeline']=time();
		
		$this->load->database();
		
		$result=$this->category->put($form,$id);
		
		switch($result){
			case 1:$this->form_validation->set_error('title','数据创建失败，请重试');break;
			case 2:$this->form_validation->set_error('parent_id','类别信息读取错误');break;
			case 3:$this->form_validation->set_error('parent_id','类别不可以是自己或自己的子类');break;
			case 4:$this->form_validation->set_error('move_des','独立类别不可选择类别之前或类别之后');break;
		}
		if($result!=0){
			return $this->$backward();
		}
		
		$this->backview->success($this->lang->line('item_put_success')
			,array('完成'=>"javascript:location.reload();"));
	}
	
	function delete(){
		$id=$this->uri->segment(3,0);
		if($id>0){
			$this->load->database();
			$this->db->select('id,is_fixed');
			$rs_del=$this->db->get_where($this->category->table_name(),array('id'=>$id),1)->row();
			if($rs_del->is_fixed==1){
				$this->backview->is_iframe_post(1);
				return $this->backview->failure("该栏目为固定栏目，不可删除"
					,"{$this->view}/index/$type_id/$page/$order/$cond/$each");
			}
		}
		if($this->category->delete($id)!=0){
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		return $this->backview->success($this->lang->line('item_delete_success')
			,array('转到列表'=>"{$this->title}"));
	}
	
	function _get_id_tree(){
		$id_tree=$this->category->id_tree_for_edit();
		$this->load->database();
		$this->db->select('id,is_fixed');
		$rs_types=$this->db->get($this->category->table_name())->result_array();
		$arr_types=array();
		if($rs_types){
			foreach ($rs_types as $r){
				$arr_types[$r['id']]=$r['is_fixed'];
			}
		}
		if($id_tree){
			foreach ($id_tree as $k => $v){
				if(isset($arr_types[$k]) && $arr_types[$k]==1){
					unset($id_tree[$k]);
				}
			}
		}
		return $id_tree;
	}
	
	function moveup(){
		$id=intval($this->uri->segment(3,-1));
		if($id<0){
			return $this->backview->failure('未指定项目');
		}
		if(!($rs=$this->type_model->get($id))){
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		if(!($target=$this->type_model->get_prev($rs->id))){
			return $this->backview->failure('已经移动到最上面了');
		}
		//print_r($rs);print_r($target);exit;
		if(!$this->type_model->swap_item($rs,$target)){
			return $this->backview->failure('移动失败，请重试');
		}
		//print_r($this->db->queries);exit;
		redirect("{$this->title}/index");
	}
	
	function movedown(){
		$id=intval($this->uri->segment(3,-1));
		if($id<0){
			return $this->backview->failure('未指定项目');
		}
		if(!($rs=$this->type_model->get($id))){
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		if(!($target=$this->type_model->get_next($rs->id))){
			return $this->backview->failure('已经移动到最下面了');
		}
		//print_r($rs);print_r($target);exit;
		if(!$this->type_model->swap_item($rs,$target)){
			return $this->backview->failure('移动失败，请重试');
		}
		redirect("{$this->title}/index");
	}
	
	function access(){
		$goto="{$this->title}/index";
		
		if(empty($_POST['access'])||!method_exists($this,'_access_'.$_POST['access'])){
			return $this->backview->failure('未指定的操作，可能您点击了错误的链接',$goto);
		}
		
		if(!isset($_POST['checked'])||!is_array($_POST['checked'])||count($_POST['checked'])<1){
			return $this->backview->failure('请至少选择一个项目',$goto);
		}
		
		$this->{'_access_'.$_POST['access']}($_POST['checked'],$goto);
	}
	
	function _access_delete($ids,$goto=''){
		foreach($_POST['checked'] as $id){
			if($this->category->delete($id)!=0){
				return $this->backview->failure($this->lang->line('item_delete_failure'));
			}
		}
		
		return $this->backview->success($this->lang->line('access_delete_success')
			,array('转到列表'=>"{$this->title}"));
	}
}