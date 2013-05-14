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
<?=form_open_multipart("$title/post/$type_id")?>
<div class="dialog-title">
<h2><?=admin_create_caption($caption)?></h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<ul class="halfside">
	<li>招聘岗位&nbsp;<br />
		<?=form_new_input('title','','class="text m"')?></li>
	<li>状态<br />
		<label><?=form_new_checkbox('show','1',true)?>前台可见</label>
		</li>
	<i class="clear"></i>
	<li>招聘人数<span>填0表示不限制</span><br />
		<?=form_new_input('amount','0','class="text m"')?></li>
	<li>招聘有效期<br />
		<?=form_new_input('timeline',date('Y-m-d'),'class="text mydate s"')?>
		至
		<?=form_new_input('expire',date('Y-m-d',time()+3600*24*90),'class="text mydate s"')?></li>
	<i class="clear"></i>
	<li>招聘部门<br />
		<?=form_new_input('department','','class="text m"')?></li>
	<li>性别要求<br />
		<label><?=form_new_radio('gender','不限',true)?>不限</label>&nbsp; &nbsp;
		<label><?=form_new_radio('gender','男')?>男</label>&nbsp; &nbsp;
		<label><?=form_new_radio('gender','女')?>女</label>&nbsp; &nbsp;
		</li>
	<i class="clear"></i>
	<li>工作经验<br />
		<?=form_new_input('experience','','class="text m"')?></li>
    <li>专业要求<br />
        <?=form_new_input('major','','class="text m"')?></li>
	<li>工作地点<br />
		<?=form_new_input('place','','class="text m"')?></li>
	<li>学历要求<br />
		<?=form_dropdown('edu',$this->lang->line('edu_tree_for_edit'))?></li>
</ul>
<p>岗位职责<br />
	<?=form_textarea('intro',set_value('intro'),'id="intro"')?>
</p>
<p>任职要求<br />
    <?=form_textarea('content',set_value('content'),'id="content"')?>
</p>
<p>公司福利<br />
    <?=form_textarea('contents',set_value('contents'),'id="contents"')?>
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