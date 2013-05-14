<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(860);
	$(".form input:first").focus();
}
</script>
<?=form_open_multipart("$title/put/$type_id/$id/$page/$order/$each/$cond")?>
<div class="dialog-title">
<h2><?=admin_edit_caption($caption)?></h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<p>页面模块<br />
<?php
$title=array('index'=>'首页','about'=>'公司简介','news'=>'新闻中心','product'=>'产品展示','marketing'=>'营销中心','seavers'=>'客户服务','recruit'=>'人力资源','contact'=>'联系我们');
foreach($title as $k=>$r){
?>
<input type="radio" name="title" value="<?=$k?>" <?php if($rs->title==$k){echo "checked";}?> /><?=$r?>
<?php
}
?>
</p>
<p>标题<br />
	<?=form_edit_input('ktitle',$rs,'class="text m"')?>
</p>
<p>状态<?=form_edit_checkbox('show','1',$rs)?>前台可见</p>
<p>关键字<br />
	<?=form_textarea('intro',set_edit_value('intro',$rs),'id="intro"')?>
</p>
<p>描述<br />
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