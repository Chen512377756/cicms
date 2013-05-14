<?php

class CI_Category{
	var $table='undefined';
	var $parent_type='Parent Type';
	var $child_mark='&nbsp;|-&nbsp;';
	var $child_space='&nbsp;&nbsp;';
	
	var $field_id='id';
	var $field_parent_id='parent_id';
	var $field_type_id='type_id';
	var $field_title='title';
	var $field_path='path';
	var $field_depth='depth';
	
	var $db;
	var $max_depth=-1;
	
	function CI_Category($config=null){
		if(is_array($config))
			foreach ($config as $k=>$v)
				if(isset($this->$k))$this->$k=$v;
		
		$CI=&get_instance();
		$CI->load->database();
		$this->db=&$CI->db;
		
		log_message('debug','CI_Category class initialized');
	}
	
	function _ready_table($table){
		if(empty($table))$table=$this->table;
		return $table;
	}
	
	function table_name($table=''){
		if(!empty($table)){
			$this->table=$table;
		}
		return $this->table;
	}
	
	function get_all($table=''){
		$this->db->order_by('path','asc');
		$q=$this->db->get($this->_ready_table($table));
		return $q->result();
	}
	
	function get($id=0,$table=''){
		$q=$this->db->get_where($this->_ready_table($table),
			array($this->field_id=>$id));
		if($q->num_rows()<1)return false;
		return $q->row();
	}
	
	function get_by_type_id($id=0,$table=''){
		$q=$this->db->get_where($this->_ready_table($table),
			array($this->field_type_id=>$id));
		if($q->num_rows()<1)return false;
		return $q->row();
	}
	
	function get_by_parent_id($id=0,$table=''){
		$this->db->order_by('path','asc');
		$q=$this->db->get_where($this->_ready_table($table),
			array($this->field_type_id=>$id));
		return $q->result();
	}
	
	function filter_parent($tree,$id_or_title){
		$a=array();
		if(isset($tree[0])){
			$a[0]=$tree[0];
		}
		$found=false;
		$key=is_int($id_or_title)?'k':'v';
		foreach($tree as $k=>$v){
			if(!$found){
				if($$key==$id_or_title){
					$found=true;
				}
			}else{//add child node
				if(strpos($v,$this->child_mark)===false){
					return $a;
				}
				$a[$k]=$v;
			}
		}
		return $a;
	}
	
	function type_tree($table=''){
		return $this->_tree($table,$this->field_type_id,false);
	}
	
	function type_tree_for_edit($table=''){
		return $this->_tree($table,$this->field_type_id,true);
	}
	
	function id_tree($table=''){
		return $this->_tree($table,$this->field_id,false);
	}
	
	function id_tree_for_edit($table=''){
		return $this->_tree($table,$this->field_id,true);
	}
	
	function _tree($table,$field,$for_edit){
		$menu=$this->get_all($table);
		if(is_array($menu)&&count($menu)>0){
			$a=$for_edit?array($this->parent_type):array();
			foreach($menu as $k=>$v){
				if(intval($v->{$this->field_parent_id})>0){
					$a[$v->{$field}]=str_repeat($this->child_space,$v->{$this->field_depth}-1).$this->child_mark.$v->{$this->field_title};
				}else{
					$a[$v->$field]=$v->{$this->field_title};
				}
			}
			return $a;
		}
		
		$menu=$for_edit?array($this->parent_type):array();
		return $menu;
	}
	
	function post($set,$table=''){
		$table=$this->_ready_table($table);
		
		$set[$this->field_parent_id]=intval($set[$this->field_parent_id]);
		if(!$this->db->insert($table,$set)){
			return 1;
		}
		$new_id=$this->db->insert_id();
		
		if($set[$this->field_parent_id]>0){
			$q=$this->db->get_where($table,array($this->field_id=>$set[$this->field_parent_id]));
			if($q->num_rows()<1){
				return 2;
			}
			$parent=$q->row_array();
			$new=array($this->field_path=>$parent[$this->field_path].','.$new_id);
			$new[$this->field_parent_id]=$parent[$this->field_id];
			$new[$this->field_depth]=count(explode(',',$new[$this->field_path]))-1;
		}else{
			$new=array($this->field_path=>$new_id);
		}
		$new[$this->field_type_id]=$new_id;
		
		if(!$this->db->update($table,$new,array($this->field_id=>$new_id))){
			return 3;
		}
		return 0;
	}
	
	function put($set,$id,$table=''){
		$table=$this->_ready_table($table);
		
		$id=intval($id);
		$set[$this->field_parent_id]=intval($set[$this->field_parent_id]);
		if($set[$this->field_parent_id]==$id){
			return 3;
		}
		
		if($set[$this->field_parent_id]>0){
			$q=$this->db->get_where($table,array($this->field_id=>$set[$this->field_parent_id]));
			if($q->num_rows()<1){
				return 2;
			}
			$parent=$q->row_array();
			$set[$this->field_path]=$parent[$this->field_path].','.$id;
			$set[$this->field_depth]=count(explode(',',$set[$this->field_path]))-1;
		}else{
			$set[$this->field_path]=$id;
			$set[$this->field_depth]=0;
		}
		
		if(!$this->db->update($table,$set,array($this->field_id=>$id))){
			return 1;
		}
		return 0;
	}
	
	function delete($id,$table=''){
		$table=$this->_ready_table($table);
		
		$id=intval($id);
		if(!$this->db->delete($table,array($this->field_id=>$id))){
			return 1;
		}
		return 0;
	}
	
	function get_max_depth($depth=0,$table=''){
		if($depth>0){
			return $depth;
		}
		$table=$this->_ready_table($table);
		if($this->max_depth<0){
			$this->db->select_max('depth');
			$q=$this->db->get($table);
			$this->max_depth=intval($q->row('depth'))+1;
		}
		return $this->max_depth;
	}
	
	//多级产品搜索
	function split_type($max_depth=0,$table=''){
		$table=$this->_ready_table($table);
		$max_depth=$this->get_max_depth($max_depth,$table);
		$types=array();
		$id_lookup=array(0=>0);
		$this->db->order_by('path','asc');
		$this->db->select('id,type_id,parent_id,title,depth,path');
		$q=$this->db->get($table);
		$q=$q->result_array();
		foreach($q as $k=>$r){
			$id_lookup[$r['id']]=$r['type_id'];
		}
		for($i=0;$i<$max_depth;$i++){
			$types[$i]=array();
			foreach(array_keys($q) as $k){
				$r=&$q[$k];
				$r['depth']=intval($r['depth']);
				$r['parent_id']=intval($r['parent_id']);
				if($r['depth']!=$i||(!isset($id_lookup[$r['parent_id']])&&$r['depth']>0))continue;
				$parent_type=$id_lookup[$r['parent_id']];
				$types[$i][$parent_type][$r['type_id']]=&$r;
			}
		}
		return $types;
	}
	
	//获得该分类和他的所有子分类的type_id
	function get_match_type($type_id,&$types,$max_depth=0,$table=''){
		$table=$this->_ready_table($table);
		$max_depth=$this->get_max_depth($max_depth,$table);
		$type_in=array($type_id);
		for($i=0;$i<$max_depth;$i++){
			foreach($types[$i] as $k=>$r){
				if($k!=$type_id)continue;
				$type_in=array_merge($type_in,array_keys($r));
			}
		}
		return array_flip(array_flip($type_in));
	}
	
}