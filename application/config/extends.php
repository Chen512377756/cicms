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
$config['extends']=array();
