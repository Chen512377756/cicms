<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(680);
}
</script>
<?=form_open("$title/put")?>
<div class="dialog-title">
<h2>我的连接</h2>
<span><a href="javascript:close_dialog();">关闭 [X]</a></span>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<table class="datable" border="0" cellpadding="0" cellspacing="0" height="100%">
	<thead>
		<tr>
			<th width="33%">后台栏目</th>
			<th width="33%">后台栏目</th>
			<th width="33%">后台栏目</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$id_tree=array();
		foreach($menu_data as $k=>$r){
			if($r[7]!=1){
				$id_tree[$k]=$r[0];
			}
		}
		$id_keys=array_keys($id_tree);
		$id_keys_count=count($id_keys);
		$t='<label><input type="checkbox" name="shortcut[]" value="%s" %s />%s</label>';
		for($i=0;$i<$id_keys_count;$i++){
			$a=array('&nbsp;','&nbsp;','&nbsp;','&nbsp;');
			$v=(in_array($id_keys[$i],$rs->shortcut)?'checked':'');
			$a[0]=sprintf($t,$id_keys[$i],$v,$id_tree[$id_keys[$i]]);
			if(isset($id_keys[++$i])){
				$v=(in_array($id_keys[$i],$rs->shortcut)?'checked':'');
				$a[1]=sprintf($t,$id_keys[$i],$v,$id_tree[$id_keys[$i]]);
			}
			if(isset($id_keys[++$i])){
				$v=(in_array($id_keys[$i],$rs->shortcut)?'checked':'');
				$a[2]=sprintf($t,$id_keys[$i],$v,$id_tree[$id_keys[$i]]);
			}
		?>
		<tr>
			<td><?=$a[0]?></td>
			<td><?=$a[1]?></td>
			<td><?=$a[2]?></td>
		</tr>
	<?php }?>
	</tbody>
</table>
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