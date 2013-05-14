这个目录不是存放视图(view)的地方了！

视图文件都保存在网站根目录

首页的视图是welcome.php

你要给每个视图文件头部添加下面一行：
<?php require_once 'index.php'?>

这样视图就可以调用对应的控制器了

by visvoy at gmail dot com on 2011-0525