<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style type="text/css">
.join_table{ background:#ddd;}
.join_table td{ color:#666; padding:5px; background:#fafafa;}
.join_table td *{ color:#666;}
.join_table td i{ color:#f00;}
</style>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(680);
}
</script>
<div class="dialog-title">
<h2>查看应聘信息 &raquo; <a href="<?=site_url("$title/excel/$id")?>">导出<b>Excel</b></a></h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="dialog-note">
	<p>申请时间:<?=$rs->timeline?></p>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="datable">
	<tr>
		<td>姓名:</td>
		<td><?=$rs->name?></td>
		<td>性别:</td>
		<td><?=$rs->gender?></td>
		<td>婚姻:</td>
		<td><?=$rs->marriage?></td>
		</tr>
	<tr>
		<td>E-mail:</td>
		<td><?=$rs->email?></td>
		<td>民族:</td>
		<td><?=$rs->nation?></td>
		<td>出生日期:</td>
		<td><?=$rs->birthday?></td>
	</tr>
	<tr>
		<td>政治面貌:</td>
		<td><?=$rs->politic?></td>
		<td>籍贯:</td>
		<td><?=$rs->birthplace?></td>
		<td>文化程度:</td>
		<td><?=$rs->edu?></td>
	</tr>
	<tr>
		<td>毕业学校:</td>
		<td><?=$rs->school?></td>
		<td>专业:</td>
		<td><?=$rs->major?></td>
		<td>毕业时间:</td>
		<td><?=$rs->graduation?></td>
	</tr>
	<tr>
		<td>外语水平:</td>
		<td><?=$rs->language?></td>
		<td>应聘职位:</td>
		<td><?=$rs->position?></td>
		<td>联系电话:</td>
		<td><?=$rs->tel?></td>
	</tr>
	<tr>
		<td>简历下载:</td>
		<td colspan="5">
			<?php if(empty($rs->file)){?>
			(没有上传简历)
			<?php }else{?>
			<a href="<?=$rs->file?>" target="_blank">点击下载</a>
			<?php }?>
		</td>
	</tr>
	<tr>
		<td>个人简历:</td>
		<td colspan="5"><?=nl2br($rs->intro)?></td>
	</tr>
</table>
<div class="dialog-bottom">
	<div class="dbleft">
		<a href="javascript:close_dialog();" class="button"><span>&nbsp; 关闭 &nbsp;</span></a>
	</div>
	<div class="dbright">
		<a href="<?=site_url("$title/excel/$id")?>" class="button"><span>导出Excel</span></a>
	</div>
	<div class="clear"></div>
</div>