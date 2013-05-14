<?php require 'index.php';?><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>flash player</title>
</head>
<body>
<?php if(isset($is_outer)){?>
<?=$flv?>
<?php }else{?>
<object type="application/x-shockwave-flash" data="flv.swf" width="640" height="480" id="vcastr3">
<param name="movie" value="flv.swf"/> 
<param name="allowFullScreen" value="true" />
<param name="FlashVars" value="xml=
	<vcastr>
		<channel>
			<item>
				<source><?=$flv?></source>
				<duration></duration>
				<title></title>
			</item>
		</channel>
		<config>
		</config>
	</vcastr>"/>
</object>
<?php }?>
</body>
</html>
