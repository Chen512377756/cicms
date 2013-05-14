<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Clearing extends Controller {
	var $view = 'clearing';
	var $table = 'order';
	function index() {
		$this->load->database ();
		$this->load->library ( 'cart' );
		$username = $this->userdata->get ( "userName" );
		$passWord = $this->userdata->get ( "passWord" );
		$rsname = $this->db->select ( "*" )->get_where ( "users", array (
				'username' => $username,
				'password' => $passWord 
		) );
		$users = $rsname->row ();
		$this->load->helper ( 'form' );
// 		foreach ( $this->cart->contents () as $k => $v ) {
// 			$name [] = $v ['name'];
// 			$product_id [] = $v ['id'];
// 			$qty [] = $v ['qty'];
// 			$price [] = $v ['price'];
// 			$pic [] = $v ['options'] ['pic1'];
// 			$size [] = $v ['options'] ['size'];
// 		}
// 		if (is_array ( $name )) {
// 			$form ['name'] = serialize ( $name );
// 		}
// 		if (is_array ( $product_id )) {
// 			$form ['product_id'] = serialize ( $product_id );
// 		}
// 		if (is_array ( $price )) {
// 			$form ['price'] = serialize ( $price );
// 		}
// 		if (is_array ( $pic )) {
// 			$form ['pic'] = serialize ( $pic );
// 		}
// 		if (is_array ( $qty )) {
// 			$form ['qty'] = serialize ( $qty );
// 		}
// 		if (is_array ( $size )) {
// 			$form ['size'] = serialize ( $size );
// 		}
        
        
		$form ['content'] = $_POST ['content1'];
		$form ['title'] = $_POST ['title1'];
		$form ['mobile'] = $_POST ['mobile1'];
		$form ['postal'] = $_POST ['postal1'];
		$form ['note'] = $_POST ['note'];
		$form ['user_id'] = $users->id;
		$form ['show'] = 0;
		$form ['num'] = $this->cart->total ();
		$form ['type_id'] = $this->cart->total_items();
		$form ['order'] = serialize($this->cart->contents ());
		$form ['timeline'] = time ();
		$form ['order_id'] = time () . $users->id;
		if (! $this->db->insert ( $this->table, $form )) {
			goto_message ( '订单处理失败', 'statement' );
		}
		if (count($this->cart->contents ()) < 1) {
			goto_message ( '购物车没有产品，请购买你需要的产品', 'statement' );
		}
		if (! ($ins = $this->db->insert_id ())) {
			return false;
		}
		$this->db->update ( "order", array (
				'sort_id' => $ins
		), array (
				'id' => $ins
		) );
        
        
        $order_id=0;
	    $pro_title=0;
		foreach ($this->cart->contents() as $k=>$v)
		{
		    $order_id=time () . $users->id; 
			$pro_title=$v['options']['title'];
            $pro_size=$v['options']['size'];
		}
		$data = array(
				'order_id'      => $order_id,
				'pro_title'     => $pro_title,
                'pro_size'      => $pro_size
		);
		//print_r($data);die;
	    if(!$this->cart->insert($data)){
	    	
		}else{
			echo 2;
		}
        
		$this->load->view ( $this->view, get_defined_vars () );
	}
}