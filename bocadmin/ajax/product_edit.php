<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
	'title'=>'产品图片（342px*266px）',
	'thumb'=>'',
	'record'=>isset($rs)?$rs:null
	))?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(860);
	$(".form input:first").focus();
	dialog_editor("#content");
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
	<li>产品名称<br />
		<?=form_edit_input('title',$rs,'class="text m"')?>
		</li>
	<li>发布时间 / 状态<br />
		<?=form_edit_input('timeline',$rs,'class="text mydatetime"')?>
		&nbsp;&nbsp;
		<label><?=form_edit_checkbox('show','1',$rs)?> 台可见</label>
		</li>
	<i class="clear"></i>
	<li>适用<br />
		<?=form_edit_input('ex1',$rs,'class="text m"')?></li>
	<li>作用<br />
		<?=form_edit_input('ex2',$rs,'class="text m"')?></li>
	<li>主要成份<br />
		<?=form_edit_input('ex3',$rs,'class="text m"')?></li>
    <li>规格<br />
        <?=form_edit_input('ex4',$rs,'class="text m"')?></li>
    <li>单价<br />
        <?=form_edit_input('ex5',$rs,'class="text m"')?></li>
</ul>
<?=$upload_htm?>
<p>产品配方<br />
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