<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php list($upload_js,$upload_htm)=admin_upload_doc_list(array(
	'title'=>'上传文件(每次可上传 100 个文件)',
	'thumb'=>'480x480',
	'max_upload'=>100,
	'record'=>isset($rs)?$rs:null
	))?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(860);
	<?=$upload_js?>
}
</script>
<?=form_open_multipart("$title/child_post/$type_id",null,array('book_id'=>$type_id))?>
<div class="dialog-title">
<h2>批量添加内页</h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<?=$upload_htm?>
</div>
<div class="dialog-bottom">
	<div class="dbleft">
		<a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a>
	</div>
	<div class="dbright">
		<span class="submit"><input type="submit" value="保存信息" /></span>
	</div>
	<div class="clear"></div>
</div>
<?=form_close()?>