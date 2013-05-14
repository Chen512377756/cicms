<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fbook extends Controller{
	var $caption;
	var $title;
	var $iframe;
	
	var $view='fbook';
	var $table='product';
	var $table_type='fbook_type';
	var $table_child='fpage';
	
	var $rule_post=array(
		array(
			'field'=>'title',
			'label'=>'标题',
			'rules'=>'trim|required'),
		array(
			'field'=>'timeline',
			'label'=>'发布时间',
			'rules'=>'trim'),
		array(
			'field'=>'show',
	        'rules'=>'intval'),
		array(
			'field'=>'recommend',
	        'rules'=>'intval'),
		array(
			'field'=>'type_id',
	        'rules'=>'intval'),
	);
	
	var $rule_child_post=array(
		array(
			'field'=>'title',
			'label'=>'标题',
			'rules'=>'trim'),
		array(
			'field'=>'show',
	        'rules'=>'intval'),
	);
	
	function Fbook(){
		parent::Controller();
		list($this->caption,$nav,$sub)=$this->backview->menu_caption_index();
		$this->load->vars(array(
			'title'=>$this->title=strtolower(get_class()),
			'caption'=>$this->caption,
			'nav_index'=>$nav,
			'sub_index'=>$sub));
		
		$this->load->model('dataset_atom','my_model');
		$this->load->model('dataset_flow','my_flow');
		$this->my_model->table_name($this->table);
		$this->my_flow->set_atom($this->my_model);
		$this->my_flow->set_ctrl($this);

		$this->load->library('category');
		$this->category->table_name($this->table_type);
	}

	// index.php?ctrl/func/type_id/page/order/each/cond
	
	function index(){
		$this->load->vars('type_tree',$this->category->type_tree());
		$this->load->view($this->view,$this->my_flow->pager(array(
			'search_field'=>'title',
		)));
	}
	
	function search(){
		$this->load->vars('type_tree',$this->category->type_tree());
		list($keyword,$cond)=$this->my_flow->search();
		$type_id=$this->uri->segment(3,-1);
		redirect("{$this->title}/index/$type_id/1/null/10/$cond");
	}
	
	function create(){
		$this->load->vars('type_tree',$this->category->type_tree());
		$type_id=$this->uri->segment(3,-1);
		$this->backview->view("ajax/{$this->view}_create",get_defined_vars());
	}
	
	function post(){
		$type_id=$this->uri->segment(3,-1);
		if(!$this->my_flow->post(array(
			'rule'=>$this->rule_post,
			'form'=>array(
				//'type_id'=>$type_id,
				'timeline'=>time(),
				),
			))){
			return $this->create();
		}
		
		$this->backview->success($this->lang->line('item_post_success')
			,array('继续添加'=>"ajax:{$this->title}/create/$type_id"
				,'完成'=>"{$this->title}/index/$type_id"));
	}
	
	function edit(){
		$this->load->vars('type_tree',$this->category->type_tree());
		if($vars=$this->my_flow->edit()){
			$this->backview->view("ajax/{$this->view}_edit",$vars);
		}
	}
	
	function put(){
		$type_id=$this->uri->segment(3,-1);
		if(!$this->my_flow->put(array(
			'rule'=>$this->rule_post,
			'form'=>array(
				//'type_id'=>$type_id,
				'timeline'=>time(),
				),
			))){
			return $this->edit();
		}
		
		$this->backview->success($this->lang->line('item_put_success')
			,array('完成'=>"javascript:location.reload();"));
	}
	
	function delete(){
		$id=$this->uri->segment(3,-1);
		if(!$this->my_flow->delete($id)){
			return print '{"result":1}';
		}
		$this->_delete_child($id);
		echo '{"result":0}';
	}
	
	function _delete_child($id){
		$this->db->select('photo,thumb');
		$q=$this->db->get_where($this->table_child,array('book_id'=>$id));
		$a=array();
		foreach($q->result_array() as $r){
			if(!empty($r['photo'])){
				$a[]=$r['photo'];
			}
			if(!empty($r['thumb'])){
				$a[]=$r['thumb'];
			}
		}
		$this->load->helper('upload_clear');
		upload_clear($a);
		$this->db->delete($this->table_child,array('book_id'=>$id));
	}
	
	function toggle(){
		$this->load->helper('js');
		print js_encode($this->my_flow->toggle());
	}
	
	function moveup(){
		$result=$this->my_flow->moveup(
			array('where'=>array("type_id"=>$this->uri->segment(3,-1))));
		switch($result){
			case 1:
				return $this->backview->failure($this->lang->line('item_not_found'));
			case 2:
				return $this->backview->failure('已经移动到最上面了');
			case 3:
				return $this->backview->failure('移动失败，请重试');
			default:if(!is_array($result))
				return $this->backview->failure('未定义的错误');
		}
		extract($result,EXTR_OVERWRITE);
		redirect("{$this->title}/index/$type_id/$page/$order/$each/$cond");
	}
	
	function movedown(){
		$result=$this->my_flow->movedown(
			array('where'=>array("type_id"=>$this->uri->segment(3,-1))));
		switch($result){
			case 1:
				return $this->backview->failure($this->lang->line('item_not_found'));
			case 2:
				return $this->backview->failure('已经移动到最下面了');
			case 3:
				return $this->backview->failure('移动失败，请重试');
			default:if(!is_array($result))
				return $this->backview->failure('未定义的错误');
		}
		extract($result,EXTR_OVERWRITE);
		redirect("{$this->title}/index/$type_id/$page/$order/$each/$cond");
	}
	
	function access(){
		$ret=$this->my_flow->access();
		switch($ret['result']){
			case 1:
				return $this->backview->failure('未指定的操作，可能您点击了错误的链接',$ret['goto']);
			case 2:
				return $this->backview->failure('请至少选择一个项目',$ret['goto']);
		}
		$this->{'_access_'.$_POST['access']}($_POST['checked'],$ret['goto']);
	}
	
	function _access_delete($ids,$goto=''){
		if(!$this->my_flow->access_delete($ids,array(
			'on_delete'=>'_delete_child',
			))){
			return $this->backview->failure($this->lang->line('item_delete_failure'));
		}
		return $this->backview->success($this->lang->line('access_delete_success')
			,array('转到列表'=>$goto));
	}
	
	//
	//child series
	//
	
	function child(){
		$type_id=$this->uri->segment(3,-1);
		if(!($parent_rs=$this->my_model->get($type_id))){
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		$this->load->vars(array(
			'parent_rs'=>$parent_rs,
			));
		$this->my_model->table_name($this->table_child);
		$this->load->view("{$this->view}_child",$this->my_flow->pager(array(
			'where'=>array('book_id'=>$type_id),
			'link_method'=>'child',
			'search_field'=>'title',
		)));
	}
	
	function child_search(){
		list($keyword,$cond)=$this->my_flow->search();
		$type_id=$this->uri->segment(3,-1);
		redirect("{$this->title}/child/$type_id/1/null/10/$cond");
	}
	
	function child_create(){
		$type_id=$this->uri->segment(3,-1);
		$this->backview->view("ajax/{$this->view}_child_create",get_defined_vars());
	}
	
	function child_post(){
		$type_id=$this->uri->segment(3,-1);
		$form=array(
			'book_id'=>$type_id,
			'show'=>1,
			'timeline'=>time(),
			);
		$this->my_model->table_name($this->table_child);
		$b=$this->input->post('upload_clear');
		if(is_array($b)){
			$this->load->helper('upload_clear');
			upload_clear($b);
		}
		$a=$this->input->post('userdoc_list');
		if(is_array($a)&&count($a)>0){
			$photo=$thumb=array();
			foreach($a as $v){
				$k=$this->_url_to_path($v);
				if(empty($k)||!is_file($k)){
					continue;
				}
				if(strpos($v,'_thumb')){
					$thumb[str_replace('_thumb','',$v)]=$v;
				}else{
					$photo[$v]=1;
				}
			}
			foreach($photo as $pic=>$k){
				$tmb=(isset($thumb[$pic])?$thumb[$pic]:$pic);
				$form['file']=$pic;
				$form['thumb']=$tmb;
				$this->my_model->post($form);
			}
		}
		
		$this->backview->success($this->lang->line('item_post_success')
			,array('继续添加'=>"ajax:{$this->title}/child_create/$type_id"
				,'完成'=>"{$this->title}/child/$type_id"));
	}
	
	function _url_to_path($url){
		$upurl=rtrim(config_item('upload.url'),'/\\').'/';
		$updir=rtrim(config_item('upload.dir'),'/\\').'/';
		if(empty($url)||is_int($url)){
			return '';
		}
		if(!strncasecmp($url,$upurl,strlen($upurl))){
			$url=$updir.substr($url,strlen($upurl));
		}
		return $url;
	}
	
	function child_edit(){
		$this->my_model->table_name($this->table_child);
		if($vars=$this->my_flow->edit()){
			$this->backview->view("ajax/{$this->view}_child_edit",$vars);
		}
	}
	
	function child_put(){
		$type_id=$this->uri->segment(3,-1);
		$this->my_model->table_name($this->table_child);
		if(!$this->my_flow->put(array(
			'rule'=>$this->rule_child_post,
			'form'=>array(
				'book_id'=>$type_id,
				'timeline'=>time(),
				),
			))){
			return $this->child_edit();
		}
		
		$this->backview->success($this->lang->line('item_put_success')
			,array('完成'=>"javascript:location.reload();"));
	}
	
	function child_delete(){
		$this->my_model->table_name($this->table_child);
		if(!$this->my_flow->delete($this->uri->segment(4,-1))){
			return print '{"result":1}';
		}
		echo '{"result":0}';
	}
	
	function child_toggle(){
		$this->load->helper('js');
		$this->my_model->table_name($this->table_child);
		print js_encode($this->my_flow->toggle(array(
			'id_segment'=>4,
			'column_segment'=>5,
			)));
	}
	
	function child_moveup(){
		$this->my_model->table_name($this->table_child);
		$result=$this->my_flow->moveup(
			array('where'=>array("book_id"=>$this->uri->segment(3,-1))));
		switch($result){
			case 1:
				return $this->backview->failure($this->lang->line('item_not_found'));
			case 2:
				return $this->backview->failure('已经移动到最上面了');
			case 3:
				return $this->backview->failure('移动失败，请重试');
			default:if(!is_array($result))
				return $this->backview->failure('未定义的错误');
		}
		extract($result,EXTR_OVERWRITE);
		redirect("{$this->title}/child/$type_id/$page/$order/$each/$cond");
	}
	
	function child_movedown(){
		$this->my_model->table_name($this->table_child);
		$result=$this->my_flow->movedown(
			array('where'=>array("book_id"=>$this->uri->segment(3,-1))));
		switch($result){
			case 1:
				return $this->backview->failure($this->lang->line('item_not_found'));
			case 2:
				return $this->backview->failure('已经移动到最下面了');
			case 3:
				return $this->backview->failure('移动失败，请重试');
			default:if(!is_array($result))
				return $this->backview->failure('未定义的错误');
		}
		extract($result,EXTR_OVERWRITE);
		redirect("{$this->title}/child/$type_id/$page/$order/$each/$cond");
	}
	
	function child_access(){
		if(isset($_POST['access'])){
			$_POST['access']='child_'.$_POST['access'];
		}
		$this->my_model->table_name($this->table_child);
		$ret=$this->my_flow->access(array(
			'method'=>'child',
			));
		switch($ret['result']){
			case 1:
				return $this->backview->failure('未指定的操作，可能您点击了错误的链接',$ret['goto']);
			case 2:
				return $this->backview->failure('请至少选择一个项目',$ret['goto']);
		}
		$this->{'_access_'.$_POST['access']}($_POST['checked'],$ret['goto']);
	}
	
	function _access_child_delete($ids,$goto=''){
		$this->my_model->table_name($this->table_child);
		if(!$this->my_flow->access_delete($ids)){
			return $this->backview->failure($this->lang->line('item_delete_failure'));
		}
		return $this->backview->success($this->lang->line('access_delete_success')
			,array('转到列表'=>$goto));
	}
}