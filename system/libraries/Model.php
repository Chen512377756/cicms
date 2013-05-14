<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class Model {

	var $_parent_name = '';

	/**
	 * @var CI_Siteview
	 */
	var $siteview;
	/**
	 * @var CI_Siteauth
	 */
	var $siteauth;
	/**
	 * @var CI_Backview
	 */
	var $backview;
	/**
	 * @var CI_Backauth
	 */
	var $backauth;
	/**
	 * @var CI_Category
	 */
	var $category;
	/**
	 * @var CI_Pager
	 */
	var $pager;
	/**
	 * @var CI_Input
	 */
	var $input;
	/**
	 * @var CI_Loader
	 */
	var $load;
	/**
	 * @var CI_DB_active_record
	 */
	var $db;
	/**
	 * @var CI_Output
	 */
	var $output;
	/**
	 * @var CI_Session
	 */
	var $session;
	/**
	 * @var CI_Userdata
	 */
	var $userdata;
	/**
	 * @var CI_Upload
	 */
	var $upload;
	/**
	 * @var CI_URI
	 */
	var $uri;
	/**
	 * @var CI_Router
	 */
	var $router;
	/**
	 * @var CI_Form_validation
	 */
	var $form_validation;
	
	var $_table;
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Model()
	{
		// If the magic __get() or __set() methods are used in a Model references can't be used.
		$this->_assign_libraries( (method_exists($this, '__get') OR method_exists($this, '__set')) ? FALSE : TRUE );
		
		// We don't want to assign the model object to itself when using the
		// assign_libraries function below so we'll grab the name of the model parent
		$this->_parent_name = ucfirst(get_class($this));
		
		log_message('debug', "Model Class Initialized");
	}
	
	function table_name($table=null){
		if(!is_null($table)){
			$this->_table=$table;
		}
		return $this->_table;
	}

	/**
	 * Assign Libraries
	 *
	 * Creates local references to all currently instantiated objects
	 * so that any syntax that can be legally used in a controller
	 * can be used within models.  
	 *
	 * @access private
	 */	
	function _assign_libraries($use_reference = TRUE)
	{
		$CI =& get_instance();				
		foreach (array_keys(get_object_vars($CI)) as $key)
		{
			if ( ! isset($this->$key) AND $key != $this->_parent_name)
			{			
				// In some cases using references can cause
				// problems so we'll conditionally use them
				if ($use_reference == TRUE)
				{
					$this->$key = NULL; // Needed to prevent reference errors with some configurations
					$this->$key =& $CI->$key;
				}
				else
				{
					$this->$key = $CI->$key;
				}
			}
		}		
	}

}
// END Model Class

/* End of file Model.php */
/* Location: ./system/libraries/Model.php */