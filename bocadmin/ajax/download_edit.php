<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
    'title'=>'杂志封面（146px*163px）',
    'thumb'=>'',
    'record'=>isset($rs)?$rs:null
))?>
<?php list($updoc_js,$updoc_htm)=admin_upload_doc_list(array(
	'title'=>'上传文件',
	'record'=>isset($rs)?$rs:null
	))?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(600);
	$(".form input:text:first").focus();
    <?=$upload_js?>
	<?=$updoc_js?>
}
</script>
<?=form_open_multipart("$title/put/$type_id/$id/$page/$order/$each/$cond")?>
<div class="dialog-title">
<h2><?=admin_edit_caption($caption)?></h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<ul class="halfside">
	<li>标题<br />
		<?=form_edit_input('title',$rs,'class="text"')?></li>
	<li>发布时间&nbsp;/ 状态<br />
		<?=form_edit_input('timeline',$rs,'class="text mydatetime"')?>&nbsp;&nbsp;<label><?=form_edit_checkbox('show','1',$rs)?>&nbsp;可见</label></li>
</ul>
<?=$upload_htm?>
<?=$updoc_htm?>
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