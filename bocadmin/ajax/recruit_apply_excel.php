<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>excel</title>
</head>
<body>
申请时间：<?=$rs->timeline?>
<table border="1">
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
</body>
</html>