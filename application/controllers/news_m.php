<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_m extends Controller{
    var $title='news_m';
    var $view='news_m';
    var $table='news';
    var $table_type='news_type';

    function News_m(){
        parent::Controller();
        $this->load->model("dataset_atom","my_model");
        $this->my_model->table_name($this->table);
    }

    function index(){
        $this->load->database();
        $type_id=2;

        //指定新闻类型
        if($type_id<1){
            $where=array('depth'=>0);
            $this->db->select('id,type_id,photo');
            $this->db->order_by("path","asc");
            $this->db->limit(1);
            $rs_news_type = $this->db->get_where($this->table_type, $where)->row();
            $type_id=$rs_news_type->type_id;
        }

        //首页推荐新闻列表
        $where = array('type_id'=>$type_id, 'show'=>1, 'recommend'=>1);
        $this->db->order_by('sort_id', 'DESC');
        $arr_rec_news = $this->db->get_where('news', $where, 1)->row();

        $title='';
        $flag=0;
        $data=array();
        $arr_news_types=$this->_get_news_type();
        if($type_id>=1){
            $this->load->library('pager');
            $where1=array('type_id'=>$type_id,'show'=>1);
            $page=intval($this->uri->segment(3,1));
            $each=5;
            $this->pager->init(array(
                'table'=>$this->my_model->table_name(),
                'link'=>site_url("{$this->title}/index/{page}"),
                'where'=>$where1,
                'page'=>$page,
                'each'=>$each,
            ));
            $this->db->order_by("sort_id","desc");
            list($page_link,$data,$page,$total)=$this->pager->create_link();
        }
        $this->load->view($this->view,get_defined_vars());
    }

    function _get_news_type(){
        $this->load->database();
        $where=array('depth'=>0);
        $this->db->select('id,type_id,title');
        $this->db->order_by("path","asc");
        $rs_news_type = $this->db->get_where($this->table_type, $where)->result_array();
        $arr_news_type = array();
        if($rs_news_type){
            foreach ($rs_news_type as $k => $r){
                $arr_news_type[$r['type_id']]=$r;
                $arr_news_type[$r['type_id']]['flag']=$k;
            }
        }
        return $arr_news_type;
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

        //点击率
        $this->load->library('session');
        $click=$rs->click;
        if(!($this->session->userdata($id)))
        {
            $click++;
            $data=array('click'=>$click);
            $this->db->where('id',$rs->id);
            $this->db->update($this->table,$data);
            $this->session->set_userdata(array($id=>1));
        }

        $type_id=$rs->type_id;
        $sort_id=$rs->sort_id;
        $sub_title=strip_tags($rs->title);
        list($prev,$next)=$this->_prev_next_item($type_id, $sort_id);
        $this->load->view("{$this->view}_info",get_defined_vars());
    }

    function _prev_next_item($type_id,$sort_id){
        $this->load->database();
        $where=array('type_id'=>$type_id,'show'=>1);
        $this->db->where("sort_id>",$sort_id,false);
        $this->db->order_by("sort_id","asc");
        $prev=$this->db->get_where($this->table,$where,1);
        $this->db->where("sort_id<",$sort_id,false);
        $this->db->order_by("sort_id","desc");
        $next=$this->db->get_where($this->table,$where,1);

        return array($prev->row(),$next->row());
    }
}