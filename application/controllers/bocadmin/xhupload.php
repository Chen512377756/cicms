<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Xhupload extends Controller{
	var $inputName='filedata';//表单文件域name
	var $attachDir;//上传文件保存路径，结尾不要带/
	var $attachUrl;
	var $dirType=2;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
	var $maxAttachSize;//最大上传大小
	var $upExt='txt,rar,zip,doc,docx,ppt,pptx,xls,xlsx,pdf,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';//上传扩展名
	var $msgType=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
	var $immediate=1;//立即上传模式

	function index(){
		$this->maxAttachSize=$this->config->item('upload.max.kb')*1000;
		$this->attachUrl=rtrim($this->config->item('upload.url'),'/');
		$this->attachDir=rtrim($this->config->item('upload.dir'),'/');
		//$this->immediate=isset($_GET['immediate'])?$_GET['immediate']:0;
		
		$this->upExt=join('|',array(
			$this->config->item('upload.allow.pic'),
			$this->config->item('upload.allow.doc'),
			$this->config->item('upload.allow.flash'),
			$this->config->item('upload.allow.media')));
			
		header('Content-Type: text/html; charset=UTF-8');
		ini_set('date.timezone','Asia/Shanghai');
		
		$err = "";
		$msg = "''";
		$tempPath=$this->attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
		$localName='';

		//HTML5上传
		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){
			file_put_contents($tempPath,file_get_contents("php://input"));
			$localName=$info[2];
		}else{//标准表单式上传
			$upfile=@$_FILES[$this->inputName];
			if(!isset($upfile))$err='文件域的name错误';
			elseif(!empty($upfile['error'])){
				switch($upfile['error']){
					case '1':
						$err = '文件大小超过了php.ini定义的upload_max_filesize值';
						break;
					case '2':
						$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
						break;
					case '3':
						$err = '文件上传不完全';
						break;
					case '4':
						$err = '无文件上传';
						break;
					case '6':
						$err = '缺少临时文件夹';
						break;
					case '7':
						$err = '写文件失败';
						break;
					case '8':
						$err = '上传被其它扩展中断';
						break;
					case '999':
					default:
						$err = '无有效错误代码';
				}
			}elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none'){
				$err = '无文件上传';
			}else{
				move_uploaded_file($upfile['tmp_name'],$tempPath);
				$localName=$upfile['name'];
			}
		}

		if($err==''){
			$fileInfo=pathinfo($localName);
			$extension=$fileInfo['extension'];
			if(preg_match('/'.str_replace(',','|',$this->upExt).'/i',$extension)){
				$bytes=filesize($tempPath);
				if($bytes > $this->maxAttachSize){
					$err='请不要上传大小超过'.format_bytes($maxAttachSize).'的文件';
				}else{
					switch($this->dirType){
						case 1: $attachSubDir = 'day_'.date('ymd'); break;
						//case 2: $attachSubDir = 'month_'.date('ym'); break;
						case 2: $attachSubDir = date('Ym'); break;
						case 3: $attachSubDir = 'ext_'.$extension; break;
					}
					$this->attachDir = $this->attachDir.'/'.$attachSubDir;
					if(!is_dir($this->attachDir)){
						@mkdir($this->attachDir, 0777);
						@fclose(fopen($this->attachDir.'/index.htm', 'w'));
					}
					if(PHP_VERSION < '4.2.0'){
						mt_srand((double)microtime() * 1000000);
					}
					$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
					$targetPath = $this->attachDir.'/'.$newFilename;
					
					rename($tempPath,$targetPath);
					@chmod($targetPath,0755);
					$targetPath = $this->attachUrl.'/'.$attachSubDir.'/'.$newFilename;//变成url
					$targetPath=$this->json_string($targetPath);
					
					//这里要将attachment保存到db
					
					if($this->immediate==1)$targetPath='!'.$targetPath;
					if($this->msgType==1)$msg="'$targetPath'";
					else $msg="{'url':'".$targetPath."','localname':'".$this->json_string($localName)."','id':'1'}";//id参数固定不变，仅供演示，实际项目中可以是数据库ID
				}
			}
			else $err='上传文件扩展名必需为：'.$this->upExt;
		
			@unlink($tempPath);
		}

		echo "{'err':'".$this->json_string($err)."','msg':".$msg."}";
	}
	
	function json_string($str){
		return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
	}
	
	function format_bytes($bytes){
		if($bytes >= 1073741824) {
			$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
		} elseif($bytes >= 1048576) {
			$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
		} elseif($bytes >= 1024) {
			$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
		} else {
			$bytes = $bytes . 'Bytes';
		}
		return $bytes;
	}
}