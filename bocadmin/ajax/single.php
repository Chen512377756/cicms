<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	var editor;
	dialog_size(860);
	$(".form input:first").focus();
	editor=dialog_editor("#content",360);
	editor.focus();
}
</script>
<?=form_open_multipart("$title/put/$id")?>
<div class="dialog-title">
<h2>编辑"<?=$caption?>"的内容</h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<p><?=form_textarea('content',set_edit_value('content',$rs),'id="content"')?>
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