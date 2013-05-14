<?php include_once('index.php')?>
<html>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<title><?php echo strip_tags($rs->title)?></title>
<style type="text/css"> 
<!--
body{
	background-color:#ccc;
	margin:0px;
}
--> 
</style>
</head> 
<body>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="100%" height="100%">
<param name="movie" value="swf/Magazine.swf?id=<?php echo $rs->id?>" />
<param name="quality" value="high" />
<param name="bgcolor" value="#cccccc" />
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="sameDomain" />
<param name="wmode" value="transparent">
<embed src="swf/Magazine.swf?id=<?php echo $rs->id?>" width="100%" height="100%" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent" allowFullScreen="true" allowScriptAccess="sameDomain"></embed>
</object>
</body>
</html>