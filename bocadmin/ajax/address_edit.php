<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php

list ( $upload_js, $upload_htm ) = admin_upload_pic_list ( array (
		'title' => '图片（大小为132px*39px）',
		'thumb' => '85x54',
		'record' => isset ( $rs ) ? $rs : null 
) )?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$(".form input:text:first").focus();
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
		<li>收货人姓名<br />
		<?php echo $rs->title;?></li>
		<li>邮编<br />
		<?php echo $rs->postal;?></li>
		<i class="clear"></i>
		<li>电话号码<br />
		<?php echo $rs->mobile;?></li>
		<li>电话---分机号<br /><?php echo $rs->telephone.$rs->telephone1;?>---<?php echo $rs->telephone2;?></li>

		<i class="clear"></i>
		<?php $users = $this->db->get_where ( "users", array('id'=>$rs->type_id))->row ();?>
		<li>会员<br />
		<?php echo $users->title;?></li>
	</ul>
		<p>地区<br />
		<?php echo $rs->info10.$rs->info11.$rs->info12;?></p>
		<p>详细地址<br /><?php echo $rs->content;?></p>	
</div>
<div class="dialog-bottom">
	<div class="dbleft">
		<a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消
				&nbsp;</span></a>
	</div>
	<div class="clear"></div>
</div>
<?=form_close()?>