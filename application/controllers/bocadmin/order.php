<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends controller{
	var $caption;
	var $title;
	var $iframe;
	
	var $view='order';
	var $table='order';

	function Order(){
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
	}
	
	function index(){
		$this->load->view($this->view,$this->my_flow->pager(array(
			//'where'=>array(),
			)));
	}
	
	function edit(){
		if(!($vars=$this->my_flow->edit(array()))){
			return;
		}
		
		$this->backview->view("ajax/{$this->view}_view",$vars);
	}
	
// 	function put(){
// 		if(!$this->my_flow->put(array(
// 			'rule'=>$this->rule_post,
// 			))){
// 			return $this->edit();
// 		}
		
// 		$this->backview->success($this->lang->line('item_put_success')
// 			,array('完成'=>"javascript:location.reload();"));
// 	}
	
	function delete(){
		$this->my_flow->delete($this->uri->segment(3,-1));
		echo '{"result":0}';
	}
	
	function toggle(){
		$this->load->helper('js');
		print js_encode($this->my_flow->toggle());
	}
	
	function toggle_read(){
		$this->load->helper('js');
		print js_encode($this->my_flow->toggle(array(
			'value'=>1,
			)));
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
		foreach($ids as $id){
			if(!$this->my_model->delete($id)){
				return $this->backview->failure($this->lang->line('item_delete_failure'));
			}
		}
		
		return $this->backview->success($this->lang->line('access_delete_success')
			,array('转到列表'=>$goto));
	}
}