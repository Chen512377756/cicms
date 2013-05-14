<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_menu_model extends Model{
	function Admin_menu_model(){
		parent::Model();
		$this->table_name('admin_act');
	}
	
	function get($id,$select='*'){
		$this->db->select($select);
		$q=$this->db->get_where($this->_table,array("id"=>$id));
		return($q->num_rows()>0?$q->row():false);
	}
	
	function get_prev($sort_id,$where=array(),$select='*'){
		$this->db->select($select);
		//$this->db->where("id>",$sort_id,false);
		$this->db->order_by("path","asc");
		$q=$this->db->get_where($this->_table,$where);
		if($q->num_rows()<1){
			return false;
		}
		$q=$q->result();
		foreach($q as $k=>$r){
			if($r->id==$sort_id){
				if(!isset($q[$k-1])){
					return false;
				}
				return $q[$k-1];
			}
		}
		return false;
	}
	
	function get_next($sort_id,$where=array(),$select='*'){
		$this->db->select($select);
		//$this->db->where("id>",$sort_id,false);
		$this->db->order_by("path","asc");
		$q=$this->db->get_where($this->_table,$where);
		if($q->num_rows()<1){
			return false;
		}
		$q=$q->result();
		foreach($q as $k=>$r){
			if($r->id==$sort_id){
				if(!isset($q[$k+1])){
					return false;
				}
				return $q[$k+1];
			}
		}
		return false;
	}
	
	function swap_item($rs,$target){
		$rs_id=$rs->id;
		$target_id=$target->id;
		unset($rs->id,$target->id);
		unset($rs->path,$target->path);
		unset($rs->depth,$target->depth);
		unset($rs->parent_id,$target->parent_id);
		$ret1=$this->db->update($this->_table,$rs,array('id'=>$target_id));
		$ret2=$this->db->update($this->_table,$target,array('id'=>$rs_id));
		if($ret1&&$ret2){
			return true;
		}
		//header('content-type:text/html;charset=utf-8');echo '<pre>';print_r($this->db->queries);echo '</pre>';exit;
		return false;
	}

	/*
	function swap_item_old($rs,$target){
		$this->db->select("id,path,parent_id");
		$this->db->order_by("path asc");
		$q=$this->db->get($this->_table);
		$q=$q->result_array();
		$a=$parent=array();
		foreach($q as $v){
			if($v['id']==$rs->id||$v['id']==$target->id)continue;
			if(strpos(' ,'.$v['path'].',',','.$rs->id.',')){
				$a[$v['id']]=trim(str_replace(','.$rs->id.',',','.$target->id.',',','.$v['path'].','),',');
			}
			if($v['parent_id']==$rs->id){
				$parent[$v['id']]=$target->id;
			}
		}
		foreach($q as $v){
			if($v['id']==$rs->id||$v['id']==$target->id)continue;
			if(strpos(' ,'.$v['path'].',',','.$target->id.',')){
				if(!isset($a[$v['id']])){
					$a[$v['id']]=trim(str_replace(','.$target->id.',',','.$rs->id.',',','.$v['path'].','),',');		
				}else if(strpos(' ,'.$a[$v['id']].',',','.$target->id.',')){
					$a[$v['id']]=trim(str_replace(','.$target->id.',',','.$rs->id.',',','.$a[$v['id']].','),',');
				}
			}
			if($v['parent_id']==$target->id){
				$parent[$v['id']]=$rs->id;
			}
		}
		//echo '<pre>';print_r($a);print_r($parent);echo '</pre>';exit;
		$rs_id=$rs->id;
		$target_id=$target->id;
		unset($rs->id,$target->id);
		unset($rs->path,$target->path);
		unset($rs->depth,$target->depth);
		unset($rs->parent_id,$target->parent_id);
		$ret1=$this->db->update($this->_table,$rs,array('id'=>$target_id));
		$ret2=$this->db->update($this->_table,$target,array('id'=>$rs_id));
		return true;
		
		
		$rs_a=explode(',',$rs->path);
		$target_a=explode(',',$target->path);
		foreach($rs_a as $k=>$v){
			if($v==$rs_id){
				$rs_a[$k]=$target_id;
			}
		}
		foreach($target_a as $k=>$v){
			if($v==$target_id){
				$target_a[$k]=$rs_id;
			}
		}
		//$rs->path=join(',',$rs_a);
		//$target->path=join(',',$target_a);
		$ret1=$this->db->update($this->_table
			,array(
				'path'=>$rs->path,
				'depth'=>$rs->depth,
				'parent_id'=>$rs->parent_id,
				)
			,array('id'=>$target_id));
		$ret2=$this->db->update($this->_table
			,array(
				'path'=>$target->path,
				'depth'=>$target->depth,
				'parent_id'=>$target->parent_id,
				)
			,array('id'=>$rs_id));
		return true;

		
		$rs_a=explode(',',$rs->path);
		$target_a=explode(',',$target->path);
		$rs_a[count($rs_a)-1]=$target->id;
		$target_a[count($target_a)-1]=$rs->id;
		//$rs->path=join(',',$rs_a);
		//$target->path=join(',',$target_a);
		unset($rs->id,$target->id,$rs_a,$target_a);
		unset($rs->depth,$target->depth);
		unset($rs->parent_id,$target->parent_id);
		$ret1=$this->db->update($this->_table,$rs,array('id'=>$target_id));
		$ret2=$this->db->update($this->_table,$target,array('id'=>$rs_id));
		//echo '<pre>';print_r($this->db->queries);echo '</pre>';exit;
		if($ret1&&$ret2){
			foreach($a as $k=>$v){
				$this->db->update($this->_table,array('path'=>$v),array('id'=>$k));
			}
			foreach($parent as $k=>$v){
				$this->db->update($this->_table,array('parent_id'=>$v),array('id'=>$k));
			}
		}else{
			return false;
		}
		//header('content-type:text/html;charset=utf-8');echo '<pre>';print_r($this->db->queries);echo '</pre>';exit;
		return true;
	}
	*/
}