<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(600);
	$(".form input:first").focus();
	
	$("#outexcel_form").attr("target","_self").submit(function(){
		close_dialog();
	});
}
</script>
<?=form_open("product/searchproductpost/$type_id",'id="outexcel_form"')?>
<div class="dialog-title">
<h2>产品搜索</h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<ul class="halfside">
	
	产品标题：<?=form_new_input('title','','class="text m"')?><br /><br />
	
	添加时间：<?=form_new_input('expire',date('Y-m-d',time()-3600*24*90),'class="text mydate s"')?>
		
		至
		<!--?=form_new_input('expire',date('Y-m-d'),'class="text mydate s"')?-->
		<?=form_new_input('timeline',date('Y-m-d'),'class="text mydate s"')?>
</ul>
</div>
<div class="dialog-bottom">
	<div class="dbleft">
		<a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a>
	</div>
	<div class="dbright">
		<span class="submit"><input type="submit" value="搜索" /></span>
	</div>
	<div class="clear"></div>
</div>
<?=form_close()?>