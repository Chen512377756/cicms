<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<content width="890" height="1208" bgcolor="cccccc" loadercolor="ffffff" panelcolor="5d5d61" buttoncolor="5d5d61" textcolor="ffffff">
<?php foreach ($data as $r){?>
<page src="<?php echo $r->photo?>" />
<?php }?>
</content>