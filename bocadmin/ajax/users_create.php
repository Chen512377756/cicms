<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(860);
	$(".form input:first").focus();
	dialog_editor("#content");
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
		<li>用户名<br />
		<?=form_new_input('username','','class="text"')?></li>
		<li>姓名<br />
		<?=form_new_input('title','','class="text"')?></li>
		<i class="clear"></i>
		<li>密码<br />
		<?=form_new_password('password','','class="text"')?></li>
		<li>重复密码<br />
		<?=form_new_password('passconf','','class="text"')?></li>
		<i class="clear"></i>
		<li>添加时间<br />
		<?=form_new_input('timeline',date('Y-m-d H:i:s'),'class="text mydatetime"')?></li>
		<li>可用与否<br /> <label><?=form_new_checkbox('show','1',true)?>可用</label></li>
		<i class="clear"></i>
		<li>电话<br />
		<?=form_new_input('tel','','class="text"')?></li>
		<li>邮箱<br />
		<?=form_new_input('email','','class="text"')?></li>
		<i class="clear"></i>
		<li>地址<br />
		<?=form_new_input('addr','','class="text m"')?></li>
		<i class="clear"></i>
	</ul>
	<p>
		详细内容<br />
	<?=form_textarea('content',set_value('content'),'id="content"')?>
</p>
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