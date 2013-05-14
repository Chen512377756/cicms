<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(850);
	$(".form input:first").focus();
    dialog_editor("#intro",135);
    dialog_editor("#content",135);
    dialog_editor("#contents",135);
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
	<li>招聘岗位&nbsp;<br />
		<?=form_edit_input('title',$rs,'class="text m"')?></li>
	<li>状态<br />
		<label><?=form_edit_checkbox('show','1',$rs)?>前台可见</label></li>
	<i class="clear"></i>
	<li>招聘人数<span>填0表示不限制</span><br />
		<?=form_edit_input('amount',$rs,'class="text m"')?></li>
	<li>招聘有效期<br />
		<?=form_edit_input('timeline',$rs,'class="text mydate s"')?>
		至
		<?=form_edit_input('expire',$rs,'class="text mydate s"')?></li>
	<i class="clear"></i>
	<li>招聘部门<br />
		<?=form_edit_input('department',$rs,'class="text m"')?></li>
	<li>性别要求<br />
		<label><?=form_edit_radio('gender','不限',$rs)?>不限</label>&nbsp; &nbsp;
		<label><?=form_edit_radio('gender','男',$rs)?>男</label>&nbsp; &nbsp;
		<label><?=form_edit_radio('gender','女',$rs)?>女</label>&nbsp; &nbsp;
		</li>
	<i class="clear"></i>
	<li>工作经验<br />
		<?=form_edit_input('experience',$rs,'class="text m"')?></li>
    <li>专业要求<br />
        <?=form_edit_input('major',$rs,'class="text m"')?></li>
	<li>工作地点<br />
		<?=form_edit_input('place',$rs,'class="text m"')?></li>
	<li>学历要求<br />
		<?=form_dropdown('edu',$this->lang->line('edu_tree_for_edit'),set_edit_value('edu',$rs))?></li>
</ul>
<p>岗位职责<br />
	<?=form_textarea('intro',set_edit_value('intro',$rs),'id="intro"')?>
</p>
<p>任职要求<br />
    <?=form_textarea('content',set_edit_value('content',$rs),'id="content"')?>
</p>
<p>公司福利<br />
    <?=form_textarea('contents',set_edit_value('contents',$rs),'id="contents"')?>
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