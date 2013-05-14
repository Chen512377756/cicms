<?php

/**
 * Setup controller auto extends
 * 
 * SUBPATH is availiable
 * 
 * <code>
 * //all root app controller will include Site_controller class automantically
 * $config['extends']['Controller']='Site_controller';
 * 
 * //when exec login controller, the Login_controller will be loaded
 * $config['extends']['Login']='Login_controller';
 * </code>
 */
$config['extends']=array(
	'Ebook_type'=>'Backtype_controller',
	'Files'=>'Backdata_controller',
	'Friend_link'=>'Backdata_controller',
    'Download'=>'Backdata_controller',
	'Honor'=>'Backdata_controller',
	'Honor_type'=>'Backtype_controller',
	'Network'=>'Backdata_controller',
	'News'=>'Backdata_controller',
	'News_type'=>'Backtype_controller',
	'Product'=>'Backdata_controller',
	'Product_type'=>'Backtype_controller',
	'Question'=>'Backdata_controller',
	'Record'=>'Backdata_controller',
	'Recruit'=>'Backdata_controller',
	'Recruit_type'=>'Backtype_controller',
	'Recruit_apply'=>'Backdata_controller',
	'Showcase'=>'Backdata_controller',
	'Store'=>'Backdata_controller',
	'Video'=>'Backdata_controller',
	'Users'=>'Backdata_controller',
	'Seo'=>'Backdata_controller',
    'Order'=>'Backdata_controller',
    'Address'=>'Backdata_controller',
);
