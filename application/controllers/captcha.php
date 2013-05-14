<?php
class Captcha extends Controller{
	function index(){
		$this->load->model("m_captcha");
		$this->m_captcha->create_image();
		$this->m_captcha->stroke();
		exit;
	}
}