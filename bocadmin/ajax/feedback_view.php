<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	var idx,ob,t,link;
	idx=4;
	ob=$("tr[rel=<?=$rs->id?>] td:eq("+idx+") a:first");
	link="<?=site_url("$title/toggle_read/$rs->id/audit")?>";
	t=ob.hide().after('<img src="a/loading.gif" width="16" height="16" align="absmiddle" id="'+ob.attr("id")+'_loading" />').parents("tr");
	t.hasClass("active")?t.removeClass("active"):t.addClass("active");
	$.ajax({
		url:link,
		type:"GET",
		dataType:"json",
		success:function(json){
			var oload=$("#"+ob.attr("id")+"_loading"),err='<a href="javascript:location.reload();" style="color:#f30;">失败</a>';
			if(json.result!=0){
				oload.hide().after(err);
			}else{
				oload.hide();
				ob.html(json.text).show();
			}
		},
		error:function(xhr){
			$("#"+ob.attr("id")+"_loading").hide()
				.after('<a href="javascript:location.reload();" style="color:#f30;">请刷新</a>');
		}
	});
	
	dialog_size(521);
	dialog_obj.find("form:first textarea:last").focus();
}
</script>
<?=form_open_multipart("$title/put/-1/$id")?>
<div class="dialog-title">
<h2><?=$caption?></h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
<ul class="halfside">
	<li>姓名：<?=$rs->username?></li>
	<li>固话：<?=$rs->phone?></li>
    <li>电子邮箱：<?=$rs->email?></li>
    <li>手机：<?=$rs->tel?></li>
</ul>
<p>留言内容：<br /><?=nl2br(strip_tags($rs->content))?></p>
<p>处理意见：
	<label><?=form_edit_radio('solve','0',$rs)?>没有处理</label>&nbsp;
	<label><?=form_edit_radio('solve','1',$rs)?>已处理</label>
	<br />
	<?=form_textarea('answer',set_edit_value('answer',$rs))?>
</p>
</div>
<div class="dialog-bottom">
	<div class="dbleft">
		<a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a>
	</div>
	<div class="dbright">
		<span class="submit"><input type="submit" value="保存意见" /></span>
	</div>
	<div class="clear"></div>
</div>
<?=form_close()?>