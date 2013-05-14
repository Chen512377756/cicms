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
<h2>编辑</h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<ul class="halfside">
	<li>提问问题<br />
		<?=form_edit_input('title',$rs,'class="text m"')?></li>
	<li>发布时间 / 状态<br />
		<?=form_edit_input('timeline',$rs,'class="text mydatetime"')?>&nbsp;&nbsp;<label><?=form_edit_checkbox('show','1',$rs)?>可见</label></li>
</ul>
<p>问题解答<br />
	<?=form_textarea('content',set_edit_value('content',$rs),'id="content"')?>
</p>
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