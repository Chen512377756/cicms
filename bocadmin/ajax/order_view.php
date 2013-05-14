<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(800);
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
<p>订单号：<?=$rs->order_id?></p>
<ul class="halfside">
	<li>收货人姓名：<?=$rs->title?></li>
	<li>手机号码：<?=$rs->mobile?></li>
</ul>
<ul class="halfside">
	<li>邮政编码：<?=$rs->postal?></li>
	<li>收货地址：<?=$rs->content?></li>
</ul>
<ul class="halfside">
	<li>总金额：<?=$rs->num?></li>
	<li>会员：<?php $users = $this->db->get_where ( "users", array('id'=>$rs->user_id))->row ();echo $users->title;?></li>
</ul>
<!--
<p>地址：<?=$rs->addr?></p>
<p>标题：<?=$rs->title?></p>
-->
<p>
</p>
<p>备注：<br /><?=nl2br(strip_tags($rs->note))?></p>
<style>
.order_view{ background:url(../img/bg06.jpg) no-repeat top center}
.order_view th{ font:bold 12px/32px "宋体"}
.order_view td{ padding:12px 0 8px; border-bottom:1px solid #ddd; text-align:center}
.order_view td img{ border:1px solid #e3e3e3; float:left; margin:0 13px; display:inline}
.order_view td h2{ font:bold 14px/22px "微软雅黑"; ; text-align:center}
.order_view td span{ color:#fd1131;}
.order_view td input{ width:38px; height:20px; border:1px solid #ddd; text-align:center; padding:0}
.order_view td a{ font-family:"宋体"; color:#0070bd}
.order_view td a:hover{ color:#967b38}
</style>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="order_view">
	<tr>
		<th>订单商品</th>
		<th>单价(元)</th>
		<th>数量</th>
		<th>小计(元)</th>
	</tr>
	
		<?php foreach (unserialize($rs->order) as $order){?>
		<tr>
			<td>
				<a href="../products.php?info/<?php echo $order['id'];?>" target="_blank"><img src="<?php echo $order['options']['pic1'];?>" width="80" height="80" /></a>
		<h2><?php echo $order['options']['title'];?></h2>
		<h2><?php echo $order['options']['size'];?></h2>
		</td>
		<td><span>￥<?php echo $order['price'];?></span></td>
		<td><?php echo $order['qty'];?></td>
		<td><span>￥<?php echo $order['price']*$order['qty'];?></span></td>
		</tr>
		<?php }?>
</table> 

<!-- <p>产品名称 / 产品数量 / 产品尺寸 / 单价：<br /><?php $price = unserialize($rs->price);$size = unserialize($rs->size);$r = unserialize($rs->name);$qty = unserialize($rs->qty);?>
<div style="float:left;">
<?php foreach ($r as $k=>$rs){ ?>
<?php echo $rs."<br/>" ?>
<?php }?>
</div>
<div style="float:left;display:inline;margin-left:20px;">
<?php foreach ($qty as $k=>$rs){ ?>
<?php echo $rs."<br/>" ?>
<?php }?>	
</div>
<div style="float:left;display:inline;margin-left:40px;">
<?php foreach ($size as $k=>$rs){ ?>
<?php echo $rs."<br/>" ?>
<?php }?>	
</div>
<div style="float:left;display:inline;margin-left:60px;">
<?php foreach ($price as $k=>$rs){ ?>
<?php echo $rs."<br/>" ?>
<?php }?>	
</div>
<br clear="all">
</p> -->


</div>
<div class="dialog-bottom">
	<div class="dbleft">
		<a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a>
	</div>
	<div class="clear"></div>
</div>
<?=form_close()?>