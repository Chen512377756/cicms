<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dataset_flow extends Model{
	var $atom;
	var $ctrl;
	
	function Dataset_flow(){
		parent::Model();
	}
	
	function set_atom(&$atom){
		$this->atom=&$atom;
	}
	
	function set_ctrl(&$ctrl){
		$this->ctrl=&$ctrl;
	}
	
	function segment(){
		$this->load->library('pager');
		list($type_id,$page,$order,$each,$cond,$keyword)=$this->pager->type_segment();
		return get_defined_vars();
	}
	
	function edit_segment(){
		$this->load->library('pager');
		list($type_id,$id,$page,$order,$each,$cond,$keyword)=$this->pager->type_edit_segment();
		return get_defined_vars();
	}
	
	//多级产品搜索
	function _split_type($table_name,$max_level){
		$types=array();
		$id_lookup=array(0=>0);
		$q=getTreeData($table_name,0,2);
		foreach($q as $k=>$r){
			$id_lookup[$r['id']]=$r['type_id'];
		}
		for($i=0;$i<$max_level;$i++){
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
	function _match_type($type_id,&$types,$max_level){
		$type_in=array($type_id);
		for($i=0;$i<$max_level;$i++){
			foreach($types[$i] as $k=>$r){
				if($k!=$type_id)continue;
				$type_in=array_merge($type_in,array_keys($r));
			}
		}
		$type_in=array_flip(array_flip($type_in));
		//print_r($type_in);
		return $type_in;
	}
	
	function pager($option=array()){
		extract($this->segment());
		$where=array();
		if(isset($option['where'])){
			$where=$option['where'];
		}else if($type_id>-1){
			if(!isset($option['type_level'])){
				$where=array('type_id'=>$type_id);
			}else{//多级产品搜索
				$types=$this->_split_type($option['type_table'],$option['type_level']);
				$type_in=$this->_match_type($type_id,$types,$option['type_level']);
			}
		}
		if(isset($option['search_field'])){
			$search_field=$option['search_field'];
		}else{
			$search_field='title';
		}
		$link_method=(empty($option['link_method'])?'index':$option['link_method']);
		if(isset($type_in)){//多级产品搜索
			$this->db->where_in('type1',$type_in);
		}
		
		//新的搜索 2012-03-02 更新
		if($cond){
			$result=$this->uri->decode($cond);
			$result=@unserialize($result);
			if(isset($result['title']) && $result['title']){
				$this->db->where("title",$result['title']);
			}
			if(isset($result['expire']) && $result['expire']){
				$this->db->where('FROM_UNIXTIME(timeline,"%Y-%m-%d") >=', $result['expire']); 
			}
			if(isset($result['timeline']) && $result['timeline']){
				$this->db->where('FROM_UNIXTIME(timeline,"%Y-%m-%d") <=', $result['timeline']); 
			}
		}
		//新的搜索 2012-03-02 更新
		$this->pager->init(array(
			'table'=>$this->atom->table_name(),
			'where'=>$where,
			'search_field'=>$search_field,
			'link'=>site_url("{$this->ctrl->title}/$link_method/$type_id/{page}/$order/$each/$cond"),
		));
		if(isset($type_in)){//多级产品搜索
			$this->db->where_in('type1',$type_in);
		}
		//新的搜索 2012-03-02 更新
		if($cond){
			$result=$this->uri->decode($cond);
			$result=@unserialize($result);
			if(isset($result['title']) && $result['title']){
				$this->db->where("title",$result['title']);
			}
			if(isset($result['expire']) && $result['expire']){
				$this->db->where('FROM_UNIXTIME(timeline,"%Y-%m-%d") >=', $result['expire']); 
			}
			if(isset($result['timeline']) && $result['timeline']){
				$this->db->where('FROM_UNIXTIME(timeline,"%Y-%m-%d") <=', $result['timeline']); 
			}
		}
		//新的搜索 2012-03-02 更新
		
		//var_dump($this->db->last_query());exit;
		list($page_link,$data,$page,$total)=$this->pager->create_link();
		
		return get_defined_vars();
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
	
	function post($option=array()){
		$this->load->library('form_validation',$option['rule']);
		if(!$this->ctrl->form_validation->run()){
			return false;
		}
		$form=$this->form_validation->to_array();
		if(!isset($form['show'])){
			$form['show']=1;
		}
		if(isset($form['timeline'])){
			$form['timeline']=strtotime($form['timeline']);
		}
		if(isset($option['form'])){
			$form=array_merge($form,$option['form']);
		}
		$this->atom->upload($form);
		if(method_exists($this->atom,'upfile')){
			$this->atom->upfile($form);
		}
		
		if(isset($option['on_post'])){
			$this->ctrl->{$option['on_post']}($form);
		}
		
		if(!$this->atom->post($form)){
			$this->form_validation->set_error('title','lang:item_put_failure');
			return false;
		}
		
		return true;
	}
	
	function edit($option=array()){
		extract($this->edit_segment());
		
		if(!($rs=$this->atom->get($id))){
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
		extract($this->edit_segment());
		
		if(!($rs=$this->atom->get($id))){
			$this->backview->is_iframe_post(1);
			$this->backview->failure($this->lang->line('item_not_found'));
			return false;
		}
		
		$this->load->library('form_validation',$option['rule']);
		if(!$this->form_validation->run()){
			return false;
		}
		$form=$this->form_validation->to_array();
		$form['show']=1;
		if(isset($form['timeline'])){
			$form['timeline']=strtotime($form['timeline']);
		}
		if(isset($option['form'])){
			$form=array_merge($form,$option['form']);
		}
		$this->atom->upload($form,$rs);
		if(method_exists($this->atom,'upfile')){
			$this->atom->upfile($form,$rs);
		}
		
		if(isset($option['on_put'])){
			$this->ctrl->{$option['on_put']}($form);
		}
		
		if(!$this->atom->put($id,$form)){
			$this->form_validation->set_error('title','item_put_failure');
			return false;
		}
		
		return true;
	}
	
	function delete($id){
		return $this->atom->delete($id);
	}
	
	function toggle($option=array()){
		static $def=array('id_segment'=>3,'column_segment'=>4);
		$option+=$def;
		$id=$this->uri->segment($option['id_segment'],-1);
		$col=$this->uri->segment($option['column_segment'],'');
		if(empty($col)||!preg_match('/^[a-z0-9_]+$/iD',$col)){
			return array('result'=>1);
		}
		if(isset($option['value'])){
			$val=$this->atom->toggle($id,$col,$option['value']);
		}else{
			$val=$this->atom->toggle($id,$col);
		}
		if($val===false){
			return array('result'=>2);
		}
		return array('result'=>0,'text'=>admin_yes_no($val));
	}
	
	function moveup($option=array()){
		extract($this->edit_segment());
		
		if(!($rs=$this->atom->get($id,"id,sort_id"))){
			return 1;
		}
		
		$where=array();
		if(isset($option['where'])){
			$where=array_merge($where,$option['where']);
		}
		if(isset($where['type_id'])&&$where['type_id']<1){
			unset($where['type_id']);
		}
		if(!($q2=$this->atom->get_prev($rs->sort_id,$where,'id,sort_id'))){
			return 2;
		}
		
		if(!$this->atom->swap_sort_id($rs,$q2)){
			return 3;
		}
		
		return get_defined_vars();
	}
	
	function movedown($option=array()){
		extract($this->edit_segment());
		
		if(!($rs=$this->atom->get($id,"id,sort_id"))){
			return 1;
		}
		
		$where=array();
		if(isset($option['where'])){
			$where=array_merge($where,$option['where']);
		}
		if(isset($where['type_id'])&&$where['type_id']<1){
			unset($where['type_id']);
		}
		if(!($q2=$this->atom->get_next($rs->sort_id,$where,'id,sort_id'))){
			return 2;
		}
		
		if(!$this->atom->swap_sort_id($rs,$q2)){
			return 3;
		}
		
		return get_defined_vars();
	}
	
	function access($opt=array()){
		static $def=array('method'=>'index');
		extract($this->segment());
		$opt+=$def;
		$method=$opt['method'];
		
		$goto="{$this->title}/$method/$type_id/$page/$order/$each/$cond";
		
		if(empty($_POST['access'])||!method_exists($this->ctrl,'_access_'.$_POST['access'])){
			return array('result'=>1,'goto'=>$goto);
		}
		
		if(!isset($_POST['checked'])||!is_array($_POST['checked'])||count($_POST['checked'])<1){
			return array('result'=>2,'goto'=>$goto);
		}
		
		return array('result'=>0,'goto'=>$goto);
	}
	
	function access_delete($ids,$opt=array()){
		static $def=array('on_delete'=>null);
		$opt+=$def;
		foreach($ids as $id){
			if(!$this->atom->delete($id)){
				return false;
			}
			if(!empty($opt['on_delete'])){
				$this->ctrl->{$opt['on_delete']}($id);
			}
		}
		return true;
	}
}