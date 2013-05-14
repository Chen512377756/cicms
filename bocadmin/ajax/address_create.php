<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php

list ( $upload_js, $upload_htm ) = admin_upload_pic_list ( array (
		'title' => '图片（大小为132px*39px）',
		'thumb' => '85x54',
		'record' => isset ( $rs ) ? $rs : null 
) )?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$(".form input:text:first").focus();
	<?=$upload_js?>
}
</script>
<?=form_open_multipart("$title/post/$type_id")?>
<div class="dialog-title">
	<h2><?=admin_create_caption($caption)?></h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<ul class="halfside">
		<li>标题<br />
		<?=form_new_input('title','','class="text"')?></li>
		<li>链接<br />
		<?=form_new_input('url','http://','class="text"')?></li>
		<i class="clear"></i>
		<li>发布时间<br />
		<?=form_new_input('timeline',date('Y-m-d H:i:s'),'class="text mydatetime"')?></li>
		<li>状态<br /> <label><?=form_new_checkbox('show','1',true)?>可见</label></li>
		<i class="clear"></i>
	</ul>
<?=$upload_htm?>
</div>
<div class="dialog-bottom">
	<div class="dbleft">
		<a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消
				&nbsp;</span></a>
	</div>
	<div class="dbright">
		<span class="submit"><input type="submit" value="保存信息" /></span>
	</div>
	<div class="clear"></div>
</div>
<?=form_close()?>