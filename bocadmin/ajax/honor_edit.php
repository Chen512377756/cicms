<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
    'title'=>'荣誉图片（197px*147px同比例大小）',
    'thumb'=>'',
	'record'=>isset($rs)?$rs:null
	))?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$(".form input:text:first").focus();
	<?=$upload_js?>
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
		<?=form_edit_input('content',$rs,'class="text"')?></li>
	<li>状态<br />
        <label><?=form_edit_checkbox('show','1',$rs)?> 可见</label></li>
</ul>
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