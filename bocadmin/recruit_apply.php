<?php require_once("index.php")?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="JavaScript" type="text/javascript">
var nav_index=<?=$nav_index?>,sub_index=<?=$sub_index?>;
</script>
<?php $this->load->view("inc/header")?>
<?=admin_set_search("$title/search/$type_id")?>
</head>
<body>
<div id="wrap">
<?=$this->load->view("inc/navi")?>
<div id="container">
<div id="side">
<?=$this->load->view("inc/side")?>
</div>
<!-- end side -->


<div id="wrapin">
<div id="main">


<?=form_open("$title/access/$type_id/$page/$order/$each/$cond")?>
<div class="box">
<b class="b1"></b><b class="b2"></b><b class="b3"></b>
<div class="bdiv">
	<div class="btop">
		<div class="page right"><?=$page_link?></div>
		<h2><a href="<?=site_url("$title/index/$type_id")?>"><?=$caption?></a></h2>
		<p><?=$total?>个符合&nbsp;|&nbsp;&nbsp;<?=admin_each_page("$title/index/$type_id/$page/$order/{each}/$cond",$each)?></p>
	</div>
	<div class="bmain">  
<table class="datable" border="0" cellpadding="0" cellspacing="0" height="100%">
	<thead>
		<tr>
			<th>应聘岗位</th>
			<th>姓名</th>
			<th>性别</th>
			<th>出生日期</th>
			<th>文化程度</th>
			<th>专业</th>
			<th>提交日期</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($data as $r){?>
		<tr rel="<?=$r->id?>" value="">
			<td><?=strcut($r->position,22)?></td>
			<td><a href="<?=site_url("$title/edit/$type_id/{$r->id}/$page/$order/$each/$cond")?>" title="查看详细" rel="ajax"><?=$r->name?></a></td>
			<td><?=$r->gender?></td>
			<td><?=$r->birthday?></td>
			<td><?=$r->edu?></td>
			<td><?=$r->major?></td>
			<td><?=date('Y-m-d',$r->timeline)?></td>
			<td><a href="<?=site_url("$title/edit/$type_id/{$r->id}/$page/$order/$each/$cond")?>" rel="ajax">查看</a> | <a href="<?=site_url("$title/excel/{$r->id}")?>" target="_blank">导出Excel</a> | <a rel="delete" href="javascript:js_delete(<?=$r->id?>,'<?=site_url("$title/delete/$r->id")?>');">删除</a></td>
		</tr>
	<?php }?>
	</tbody>
</table>
	</div>  
</div>  
<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b>  
</div>

<div class="table-option">
<ul>
	<li><a class="button" rel="check-all"><span>全选</span></a></li>
	<li><a class="button" rel="check-off"><span>不选</span></a></li>
	<li><a class="button" rel="delete-all"><span>删除</span></a></li>
</ul>
<div class="page right"><?=$page_link?></div>
<div class="i10"></div>
</div>
<?=form_close()?>


</div>
<!-- end main -->
</div>
<!-- end warpin -->
</div>
<!-- end container -->
</div><!-- end wrap -->
</body>
</html>