<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(860);
	$(".form input:first").focus();
	dialog_editor("#content");
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
		<li>用户名<br />
		<?php echo $rs->username; ?></li>
		<li>姓名<br />
		<?=form_edit_input('title',$rs,'class="text"')?></li>
		<i class="clear"></i>
		<li>密码<span>(留空表示不更改)</span><br />
		<?=form_new_password('password','','class="text"')?></li>
		<li>重复密码<br />
		<?=form_new_password('passconf','','class="text"')?></li>
		<i class="clear"></i>
		<li>添加时间<br />
		<?=form_edit_input('timeline',$rs,'class="text mydatetime"')?></li>
				<li>公司地址<br />
		<?=form_edit_input('addr',$rs,'class="text m"')?></li>
		<i class="clear"></i>
		<li>电话<br />
		<?=form_edit_input('tel',$rs,'class="text"')?></li>
		<li>邮箱<br />
		<?=form_edit_input('email',$rs,'class="text"')?></li>
		<i class="clear"></i>
		<li>出生日期<br />
		<?=form_edit_input('birth',$rs,'class="text"')?></li>
		<li>公司名称<br />
		<?=form_edit_input('company',$rs,'class="text"')?></li>
		<i class="clear"></i>
	</ul>
	<p>性别：
	<label><?=form_edit_radio('gender','男',$rs)?>男</label>&nbsp;
	<label><?=form_edit_radio('gender','女',$rs)?>女</label>
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