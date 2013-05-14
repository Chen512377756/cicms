<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
	'title'=>'图片',
	'thumb'=>'158x119',
	'record'=>isset($rs)?$rs:null
	))?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$(".form input:first").focus();
	<?=$upload_js?>
}
</script>
<?=form_open_multipart("$title/put/$id")?>
<div class="dialog-title">
<h2>编辑"<?=$caption?>"的内容</h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<p>名称<br />
	<?=form_edit_input('title',$rs,'class="text m"')?></p>
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