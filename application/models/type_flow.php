<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Type_flow extends Model{
	var $atom;
	var $ctrl;
	var $category;
	
	function Type_flow(){
		parent::Model();
	}
	
	function set_atom(&$atom){
		$this->atom=&$atom;
	}
	
	function set_ctrl(&$ctrl){
		$this->ctrl=&$ctrl;
	}
	
	function set_category(&$category){
		$this->category=&$category;
	}
	
	function pager($option=array()){
		$cond=$this->uri->segment(3,'null');
		list($if,$keyword)=$this->_resolve_keyword($cond);
		if(!isset($option['search_field'])){
			$option['search_field']='title';
		}
		$this->_resolve_like_condition($if,$option['search_field']);
		$data=$this->category->get_all();
		$total=count($data);
		return get_defined_vars();
	}
	
	function _resolve_keyword($cond){
		$if=array();
		$keyword='';
		if(!empty($cond)&&'null'!=$cond){
			$cond=@$this->uri->decode($cond);
			$cond=@unserialize($cond);
			if(is_array($cond)){
				$if=$cond;
			}
		}
		if(isset($if['keyword'])){
			$keyword=$if['keyword'];
		}
		return array($if,$keyword);
	}
	
	function _resolve_like_condition(&$like,$search_field='title'){
		//解决了AR条件AND和OR没有括号区别，导致搜索到错误的结果
		if(count($like)>0&&count($search_field)>0){
			if(is_string($search_field)){
				$search_field=explode(',',$search_field);
			}
			foreach($search_field as $k=>$key){
				if(strpos($key,'.')){
					$search_field[$k]=$this->db->_protect_identifiers($key,true,false,true);
				}else{
					$search_field[$k]=$this->db->protect_identifiers($key);
				}
			}
			foreach($like as $val){
				$a=array();
				$val = $this->db->escape_like_str($val);
				foreach($search_field as $key){
					$a[]="$key LIKE '%{$val}%'";
				}
				$a=join(' OR ',$a);
				$this->db->where("($a)",'',false);
			}
		}
	}
	
	function search($option=array()){
		$keyword='';
		$cond='null';
		$if=array('keyword'=>'');
		if(empty($_POST['keyword'])){
			return array('',$cond);
		}
		
		$keyword=trim($_POST['keyword']);
		if(!strcmp($keyword,'搜索内容')){
			return array('',$cond);
		}
		$keyword=trim(strtr($keyword,array(','=>' ','|'=>' ')));
		if(empty($keyword)){
			return array('',$cond);
		}
		$if['keyword']=$keyword;
		
		$cond=$this->uri->encode(serialize($if));
		return array($keyword,$cond);
	}
	
	function create(){
		$id_tree=$this->category->id_tree_for_edit();
		$parent_id=$this->uri->segment(3,0);
		return get_defined_vars();
	}
	
	function post($option=array()){
		$this->load->library('form_validation',$option['rule']);
		if(!$this->ctrl->form_validation->run()){
			return false;
		}
		$form=$this->form_validation->to_array();
		$form['timeline']=time();
		if(isset($option['form'])){
			$form=array_merge($form,$option['form']);
		}
		$this->atom->upload($form);
		
		$this->db->select_max("id","max_id");
		$max_id=$this->db->get_where($this->atom->table_name());
		$form['type_id']=$max_id=intval($max_id->row("max_id"))+1;
		
		if(isset($option['on_post'])){
			$this->ctrl->{$option['on_post']}($form);
		}
		
		$result=$this->category->post($form);
		switch($result){
			case 1:$this->form_validation->set_error('title','数据创建失败，请重试');break;
			case 2:$this->form_validation->set_error('parent_id','类别信息读取错误');break;
			case 3:$this->form_validation->set_error('parent_id','数据更新失败，请重试');break;
		}
		if($result!=0){
			return false;
		}
		
		return true;
	}
	
	function edit($option=array()){
		$id=$this->uri->segment(3,0);
		$id_tree=$this->category->id_tree_for_edit();
		
		if(!($rs=$this->category->get($id))){
			$this->backview->is_iframe_post(1);
			$this->backview->failure($this->lang->line('item_not_found'));
			return false;
		}
		
		$rs->timeline=date('Y-m-d H:i:s',$rs->timeline);
		
		if(isset($option['on_edit'])){
			$this->ctrl->{$option['on_edit']}($rs);
		}
		
		return get_defined_vars();
	}
	
	function put($option=array()){
		$this->load->library('form_validation',$option['rule']);
		if(!$this->ctrl->form_validation->run()){
			return false;
		}
		
		$id=$this->uri->segment(3,0);
		if(!($rs=$this->category->get($id))){
			$this->form_validation->set_error('title',$this->lang->line('item_not_found'));
			return false;
		}
		
		$form=$this->form_validation->to_array();
		$form['timeline']=time();
		if(isset($option['form'])){
			$form=array_merge($form,$option['form']);
		}
		$this->atom->upload($form,$rs);
		
		if(isset($option['on_post'])){
			$this->ctrl->{$option['on_post']}($form,$id);
		}
		
		$result=$this->category->put($form,$id);
		switch($result){
			case 1:$this->form_validation->set_error('title','数据创建失败，请重试');break;
			case 2:$this->form_validation->set_error('parent_id','类别信息读取错误');break;
			case 3:$this->form_validation->set_error('parent_id','类别不可以是自己或自己的子类');break;
			case 4:$this->form_validation->set_error('move_des','独立类别不可选择类别之前或类别之后');break;
		}
		if($result!=0){
			return false;
		}
		
		return true;
	}
	
	function delete($id){
		return $this->category->delete($id);
	}
	
	function moveup($option=array()){
		$id=intval($this->uri->segment(3,-1));
		if($id<0){
			return 1;
		}
		if(!($rs=$this->atom->get($id))){
			return 2;
		}
		if(!($target=$this->atom->get_prev($rs->id))){
			return 3;
		}
		if(!$this->atom->swap_item($rs,$target)){
			return 4;
		}
		return 0;
	}
	
	function movedown($option=array()){
		$id=intval($this->uri->segment(3,-1));
		if($id<0){
			return 1;
		}
		if(!($rs=$this->atom->get($id))){
			return 2;
		}
		if(!($target=$this->atom->get_next($rs->id))){
			return 3;
		}
		if(!$this->atom->swap_item($rs,$target)){
			return 4;
		}
		return 0;
	}
	
	function access(){
		$goto="{$this->title}/index";
		
		if(empty($_POST['access'])||!method_exists($this->ctrl,'_access_'.$_POST['access'])){
			return array('result'=>1,'goto'=>$goto);
		}
		
		if(!isset($_POST['checked'])||!is_array($_POST['checked'])||count($_POST['checked'])<1){
			return array('result'=>2,'goto'=>$goto);
		}
		
		return array('result'=>0,'goto'=>$goto);
	}
	
	function access_delete($ids){
		foreach($ids as $id){
			if(0!=$this->category->delete($id)){
				return false;
			}
		}
		return true;
	}
}