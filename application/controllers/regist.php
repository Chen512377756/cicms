<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Regist extends Controller {
	var $table = 'users';
	var $rule_post = array (
			
			array (
					'field' => 'type_id',
					'rules' => 'intval' 
			),
			array (
					'field' => 'username',
					'label' => '用户名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'password',
					'label' => '密码',
					'rules' => 'trim|md5|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'title',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'gender',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'birth',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'company',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'addr',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'tel',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'email',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			) 
	);
	var $rule_post1 = array (
			array (
					'field' => 'username',
					'label' => '用户名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'title',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'gender',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'birth',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'company',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'addr',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'tel',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			),
			array (
					'field' => 'email',
					'label' => '姓名',
					'rules' => 'trim|max_length[50]|xss_clean' 
			) 
	);
	var $rule_login = array (
			array (
					'field' => 'username',
					'label' => '用户名',
					'rules' => 'required|xss_clean' 
			),
			array (
					'field' => 'password',
					'label' => '密码',
					'rules' => 'required' 
			),
			array (
					'field' => 'remember',
					'rules' => 'intval' 
			) 
	);
	var $member_xx = array (
			array (
					'field' => 'info10',
					'rules' => 'trim' 
			),
			array (
					'field' => 'info11',
					'rules' => 'trim' 
			),
			
			array (
					'field' => 'info12',
					'rules' => 'trim' 
			),
			array (
					'field' => 'postal',
					'rules' => 'trim' 
			),
			array (
					'field' => 'content',
					'label' => '用户名',
					'rules' => 'trim' 
			),
			array (
					'field' => 'title',
					'label' => '用户名',
					'rules' => 'trim' 
			),
			
			array (
					'field' => 'telephone',
					'rules' => 'trim' 
			),
			array (
					'field' => 'telephone1',
					'rules' => 'trim' 
			),
			array (
					'field' => 'telephone2',
					'rules' => 'trim' 
			),
			array (
					'field' => 'mobile',
					'rules' => 'trim' 
			),
			array (
					'field' => 'type_id',
					'rules' => 'intval' 
			) 
	);
	function Register() {
		parent::Controller ();
		$this->load->library ( 'userdata' );
		$this->load->language ( 'admin/login' );
		$this->load->helper ( 'form' );
		$this->load->helper ( 'url' );
	}
	function index() {
		$this->load->helper ( 'form' );
		
		$this->load->view ( 'regist', get_defined_vars () );
	}
	function post() {
		$this->load->library ( 'form_validation', $this->rule_post );
		if (! $this->form_validation->run ()) {
			return $this->index ();
		}
		$form = $this->form_validation->to_array ();
		$email = $this->db->select ( 'email' )->get_where ( 'users', array (
				'email' => $form ['email']
		) );
		if ($email->num_rows () > 0) {
			echo '<script>alert("对不起，该邮箱已存在！");location.href = document.referrer</script>';
			exit ();
		}
		$rs = $this->db->select ( 'username' )->get_where ( 'users', array (
				'username' => $form ['username'] 
		) );
		if ($rs->num_rows () > 0) {
			echo '<script>alert("对不起，该用户名已存在！");location.href = document.referrer</script>';
			exit ();
		}
		$form ['timeline'] = time ();
		$form ['show'] = 0;
		unset ( $form ['captcha'] );
		$this->load->database ();
		if (! $this->db->insert ( $this->table, $form )) {
			$this->form_validation->set_error ( 'username', $this->lang->line ( 'item_put_failure' ) );
			return $this->index ();
		}
		
		goto_message ( '注册成功', 'login' );
	}
	function login() {
		$this->load->library ( 'form_validation', $this->rule_login );
		if (! $this->form_validation->run ()) {
			echo '<script>window.location.href="login.php"</script>';
		}
		$form = $this->form_validation->to_array ();
		$this->load->database ();
		$this->db->select ( 'id,username,show,audit,password,solve' );
		$rs = $this->db->get_where ( 'users', array (
				'username' => $form ['username'],
				'password' => md5 ( $form ['password'] ) 
		) );
		if ($rs->num_rows () < 1) {
			goto_message ( "账号或者密码错误" );
		} else {
			$rs = $rs->row_array ();
			$this->input->set_cookie ( 'admin_username', $rs ['username'], set_value ( 'remember' ) ? 3600 * 24 : '' );
		}
		$this->userdata->set ( "userName", $rs ['username'] );
		$this->userdata->set ( "passWord", $rs ['password'] );
		$this->userdata->set ( "userId", $rs ['id'] );
		goto_message ( '您已成功登录，欢迎！', 'member' );
	}
	function update() {
		$this->load->library ( 'form_validation', $this->rule_post1 );
		if (! $this->form_validation->run ()) {
			echo '<script>window.location.href="login.php"</script>';
		}
		$form = $this->form_validation->to_array ();
		$this->load->database ();
		$username = $this->userdata->get ( "userName" );
		$passWord = $this->userdata->get ( "passWord" );
		$rsname = $this->db->select ( "*" )->get_where ( "users", array (
				'username' => $username,
				'password' => $passWord 
		) );
		$users = $rsname->row ();
		$row = $this->db->update ( "users", $form, array (
				'id' => $users->id 
		) );
		if ($row) {
			$this->userdata->set ( "userName", $form ['username'] );
			$this->userdata->set ( "passWord", $passWord );
		}
		goto_message ( '修改成功', 'member_zl' );
	}
	function delete() {
		$this->userdata->delete ( 'userName' );
		$this->userdata->delete ( 'passWord' );
		goto_message ( "您已成功退出，欢迎下次登陆！", "index" );
	}
	function member_xx() {
		$this->load->library ( 'form_validation', $this->member_xx );
		if (! $this->form_validation->run ()) {
			return $this->index ();
		}
		$form = $this->form_validation->to_array ();
		$username = $this->userdata->get ( "userName" );
		$passWord = $this->userdata->get ( "passWord" );
		$rsname = $this->db->select ( "*" )->get_where ( "users", array (
				'username' => $username,
				'password' => $passWord 
		) );
		$users = $rsname->row ();
		$form ['type_id'] = $users->id;
		$form ['timeline'] = time ();
		$this->load->database ();
		if (! $this->db->insert ( 'friend_link', $form )) {
			return $this->index ();
		}
		
		goto_message ( '创建新地址完成', 'member_xx' );
	}
	function statement() {
		$this->load->library ( 'form_validation', $this->member_xx );
		if (! $this->form_validation->run ()) {
			return $this->index ();
		}
		$form = $this->form_validation->to_array ();
		$username = $this->userdata->get ( "userName" );
		$passWord = $this->userdata->get ( "passWord" );
		$rsname = $this->db->select ( "*" )->get_where ( "users", array (
				'username' => $username,
				'password' => $passWord 
		) );
		$users = $rsname->row ();
		$form ['type_id'] = $users->id;
		$form ['timeline'] = time ();
		$this->load->database ();
		if (! $this->db->insert ( 'address', $form )) {
			return $this->index ();
		}
		
		goto_message ( '创建新地址完成', 'statement' );
	}
	function member_update() {
		$this->load->library ( 'form_validation', $this->member_xx );
		if (! $this->form_validation->run ()) {
			echo '<script>window.location.href="index.php"</script>';
		}
		$form = $this->form_validation->to_array ();
		$this->load->database ();
		$id = $_POST ['id'];
		$form ['info10'] = $_POST ['info14' . $id . ''];
		$form ['info11'] = $_POST ['info15' . $id . ''];
		$form ['info12'] = $_POST ['info16' . $id . ''];
		$form ['timeline'] = time ();
		$username = $this->userdata->get ( "userName" );
		$passWord = $this->userdata->get ( "passWord" );
		$rsname = $this->db->select ( "*" )->get_where ( "users", array (
				'username' => $username,
				'password' => $passWord 
		) );
		$users = $rsname->row ();
		$form ['type_id'] = $users->id;
		$row = $this->db->update ( "friend_link", $form, array (
				'id' => $id 
		) );
		
		goto_message ( '修改成功', 'member_xx' );
	}
	function del() {
		$this->load->database ();
		$id = $this->uri->segment ( 3, 1 );
		$row = $this->db->delete ( "address", array (
				'id' => $id 
		) );
		if ($row) {
			echo '<script>window.location.href="member_xx.php"</script>';
			//goto_message ( '删除成功', 'member_xx' );
		}
	}
	function change_password() {
		$this->load->database ();
		$id = $_POST ['id'];
		$password = $_POST ['password'];
		$row = $this->db->update ( "users",array('password'=>md5($password)), array ('id' => $id ) );
		if ($row) {
			$this->userdata->delete ( 'userName' );
			$this->userdata->delete ( 'passWord' );
			goto_message ( '修改成功', 'login' );
		}
	}
	function security() {
		$this->load->database();
		$email = $_POST ['email'];
		$this->db->select('id,email,title,username');
		$rs=$this->db->get_where('users',array('email'=>$email));
		if($rs->num_rows()<1){
			goto_message ( '邮箱不存在！', 'forgot' );
		}
		$rs=$rs->row_array();
		//发送Email
		$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
		$html = $html.$rs['title'].'，您好：<br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;点击设置新的密码：<br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://192.168.0.82/med/security.php?'.$rs['id'].'/">修改密码</a><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('Y年m月d日');
		$html = $html.'</body></html>';
		
		require_once('class.phpmailer.php');
		require_once('class.smtp.php');
		//new一个PHPMailer对象出来
		$mail = new PHPMailer();
		//对邮件内容进行必要的过滤
		$body = eregi_replace("[\]",'',$html);
		//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->CharSet ="utf-8";
		//设定使用SMTP服务
		$mail->IsSMTP();
		//启用SMTP调试功能
		$mail->SMTPDebug  = 1;
		//1 = errors and messages
		//2 = messages only
		$mail->SMTPAuth   = true;                  		//启用 SMTP 验证功能
		$mail->SMTPSecure = "";                    		//安全协议
		$mail->Host       = "smtp.126.com";      		//SMTP 服务器
		$mail->Port       = 25;                   		//SMTP服务器的端口号
		$mail->Username   = "bocweb@126.com";  	        //SMTP服务器用户名
		$mail->Password   = "bocweb123456";            	//SMTP服务器密码
		$mail->SetFrom('bocweb@126.com', 'bocweb@126.com');
		$mail->AddReplyTo($rs['email'],$rs['email']);
		$mail->Subject    = '找回密码';
		$mail->AltBody    = ""; 						//optional, comment out and test
		$mail->MsgHTML($body);
		$address = $to = $rs['email'];
		$mail->AddAddress($address, '');
		//$mail->AddAttachment('images/phpmailer.gif');      // attachment
		if($mail->Send()){
			//goto_message($this->lang->line('site_post_success'),'recharge');
			goto_message ( '邮箱发送成功，注意接收。', 'forgot' );
		}else{
			echo "邮件发送失败，请重试！&nbsp;<a href=javascript:history.back(-1)>点击返回</a>";
			exit;
		}
	}
}