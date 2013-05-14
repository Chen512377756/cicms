<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$(".form input:first").focus();
}
</script>
<?=form_open_multipart("$title/post")?>
<div class="dialog-title">
<h2>添加销售网点</h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<ul class="halfside">
	<li>销售网点<br />
		<?=form_new_input('title','','class="text"')?></li>
	<li>发布时间<br />
		<?=form_new_input('timeline',date('Y-m-d H:i:s'),'class="text mydatetime"')?></li>
	<li>电话<br />
		<?=form_new_input('tel','','class="text"')?></li>
	<li>状态<br />
		<label><?=form_new_checkbox('show','1',true)?>前台可见</label>
		</li>
	<i class="clear"></i>
	<li>传真<br />
		<?=form_new_input('fax','','class="text"')?></li>
	<li>地区<br />
		<?=form_dropdown('city_id',$type_tree)?></li>
</ul>
<p>地址<br />
	<?=form_new_input('addr','','class="text x"')?></p>
<p>网址<span>请以 http:// 开头</span><br />
	<?=form_new_input('url','','class="text x"')?></p>
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