<?php

/*
 * --------------------------------------------------------------------
 * XTEND: OVERRIDE "SELF" FOR SUBPATH STARTUP
 * --------------------------------------------------------------------
 */
	define('SELF',pathinfo(__FILE__,PATHINFO_BASENAME));

/*
 * --------------------------------------------------------------------
 * XTEND: OVERRIDE "FCPATH" FOR SUBPATH STARTUP
 * --------------------------------------------------------------------
 */
	define('FCPATH',str_replace(SELF,'',__FILE__));

/*
 * --------------------------------------------------------------------
 * XTEND: STARTS FROM THIS ENTRY
 * --------------------------------------------------------------------
 */
require_once dirname(FCPATH).'/'.SELF;

/* End of file index.php */
/* Location: [subpath]/index.php */