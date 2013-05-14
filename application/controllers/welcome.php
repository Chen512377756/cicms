<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends Controller {
	function index(){

        //首页Banner切换
        $where =array('show'=>1);
        $this->db->order_by('sort_id', 'DESC');
        $this->db->where('length(photo)>', 10, FALSE);
        $arr_banner = $this->db->get_where('showcase', $where)->result();

        //首页推荐最新通知
        $where = array('show'=>1, 'recommend'=>1, 'type_id'=>2);
        $this->db->order_by('sort_id', 'DESC');
        $arr_news_list = $this->db->get_where('news', $where)->result();

        //首页友情链接
        $where =array('show'=>1);
        $this->db->order_by('sort_id', 'DESC');
        $this->db->where('length(photo)>', 10, FALSE);
        $arr_friendlink = $this->db->get_where('friend_link', $where)->result();

		$this->load->view('welcome',get_defined_vars());
	}
}