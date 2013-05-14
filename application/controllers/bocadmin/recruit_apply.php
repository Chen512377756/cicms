<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recruit_apply extends Backdata_controller{


	function on_load(){
		$this->view=$this->table=strtolower(get_class($this));
		$this->table_type='recruit_type';
	}

	function excel(){
		$id=intval($this->uri->segment(3,-1));
		if(!($rs=$this->model->get($id))){
			$this->backview->failure($this->lang->line('item_not_found'));
			return;
		}

		$rs->timeline=date('Y-m-d H:i:s',$rs->timeline);

		$filename=$rs->name."—申请职位—".$rs->position.".xls";
		$filename=iconv('utf-8','gbk',$filename);
		header('ContentType:application/vnd.ms-excel');
		header("content-disposition:application/vnd.ms-excel:attachment;filename=$filename");
		$this->backview->view("ajax/{$this->view}_excel",get_defined_vars());
	}

	//应聘控制器没有下面这几个事件
	function create(){show_404();}
	function post(){show_404();}
	function put(){show_404();}
}