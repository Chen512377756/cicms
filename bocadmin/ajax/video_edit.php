<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php list($upflv_js,$upflv_htm)=admin_upload_flv_list(array(
	'title'=>'上传FLV视频',
	'record'=>isset($rs)?$rs:null
	))?>
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
    'title'=>'视频截图（96px*72px）',
	'thumb'=>'',
	'record'=>isset($rs)?$rs:null
	))?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(680);
	$(".form input:text:first").focus();
	<?=$upload_js?>
	<?=$upflv_js?>
}
</script>
<?=form_open_multipart("$title/put/$type_id/$id/$page/$order/$each/$cond")?>
<div class="dialog-title">
<h2><?=admin_edit_caption($caption)?></h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<p>标题<br />
	<?=form_edit_input('title',$rs,'class="text x"')?></p>
<p>插入站外视频链接<br />
	<?=form_textarea('url',set_edit_value('url',$rs))?></p>
<ul class="halfside">
	<li><?=$upflv_htm?></li>
	<li><?=$upload_htm?></li>
</ul>
<i class="clear"></i>
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