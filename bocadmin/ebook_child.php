<?php require_once("index.php")?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="JavaScript" type="text/javascript">
var nav_index=<?=$nav_index?>,sub_index=<?=$sub_index?>;
</script>
<?php $this->load->view("inc/header")?>
<?=admin_set_search("$title/child_search/$type_id")?>
<script language="JavaScript" type="text/javascript">
(function($){
$(function(){
	$("input[rel=index]").focus(function(){
		$(this).select();
	}).change(function(){
		var id=parseInt($(this).attr("id").substring(5)),val=$(this).attr("value");
		$(this).parent().find("img,font").remove();
		$(this).parent().append("<img src='a/loading.gif' style='width:16px;height:16px;' align='absmiddle' />");
		$.getJSON("ebook.php?child_jsChangeIndex/<?php echo $type_id; ?>/"+id+"/"+val,function(json){
			var o=$("#index"+json.id).parent();
			o.find("font,img").remove();
			if(json.result==0){
				o.append("<font color='green'><b>√</b></font>")
			}else{
				o.append("<font color='red'><b>Ｘ</b></font>")
			}
			//window.location.href='news.php';
			window.location.history.back();
		});
		return;
	});
});
})(jQuery);
</script>
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


<?=form_open("$title/child_access/$type_id/$page/$order/$each/$cond")?>
<div class="box">
<b class="b1"></b><b class="b2"></b><b class="b3"></b>
<div class="bdiv">
	<div class="btop">
		<div class="page right"><?=$page_link?></div>
		<h2><a href="<?=site_url("$title/child/$type_id")?>"><?=$parent_rs->title?>的图片管理</a><em><a href="<?=site_url("$title/child_create/$type_id")?>" rel="ajax"><img src="a/i-add.gif" align="absmiddle" />添加</a></em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em><a href="<?=site_url($title)?>">返回上级>></a></em></h2>
		<p><?=$total?>个符合&nbsp;|&nbsp;&nbsp;<?=admin_each_page("$title/child/$type_id/$page/$order/{each}/$cond",$each)?></p>
	</div>

	<div class="bmain">  
<table class="datable" border="0" cellpadding="0" cellspacing="0" height="100%">
	<thead>
		<tr>
			<th>上/下移</th>
			<th>排序</th>
			<th>图</th>
			<th>标题</th>
			<th>显隐</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($data as $r){?>
		<tr rel="<?=$r->id?>" value="">
			<td><?=admin_moveup(site_url("$title/child_moveup/$type_id/{$r->id}/$page/$order/$each/$cond")).admin_movedown(site_url("$title/child_movedown/$type_id/{$r->id}/$page/$order/$each/$cond"))?></td>
			<td><input rel="index" id="index<?=$r->id;?>" type="text" value="<?=$r->sort_id;?>" style="width:35px;" title="即时更改,越大越靠前" onmouseover="this.focus()" onfocus="this.select()" class="text tip" /></td>
			<td><?=admin_photo($r->photo,$r->thumb,$r->title)?></td>
			<td><a href="<?=site_url("$title/child_edit/$type_id/{$r->id}/$page/$order/$each/$cond")?>" title="点击编辑" rel="ajax"><?=strcut($r->title,22)?></a></td>
			<td><?=admin_toggle(site_url("$title/child_toggle/$type_id/$r->id/show"),$r->show)?></td>
			<td><a href="<?=site_url("$title/child_edit/$type_id/{$r->id}/$page/$order/$each/$cond")?>" rel="ajax">编辑</a> | <a rel="delete" href="javascript:js_delete(<?=$r->id?>,'<?=site_url("$title/child_delete/$type_id/$r->id")?>');">删除</a></td>
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
	<li><a href="<?=site_url($title)?>" class="button"><span>返回上级>></span></a></li>
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