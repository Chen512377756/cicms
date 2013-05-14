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
 * CodeIgniter Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class Controller extends CI_Base {

	var $_ci_scaffolding	= FALSE;
	var $_ci_scaff_table	= FALSE;
	
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
	
	/**
	 * Constructor
	 *
	 * Calls the initialize() function
	 */
	function Controller()
	{	
		parent::CI_Base();
		$this->_ci_initialize();
		log_message('debug', "Controller Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * Assigns all the bases classes loaded by the front controller to
	 * variables in this class.  Also calls the autoload routine.
	 *
	 * @access	private
	 * @return	void
	 */
	function _ci_initialize()
	{
		// Assign all the class objects that were instantiated by the
		// front controller to local class variables so that CI can be
		// run as one big super object.
		$classes = array(
							'config'	=> 'Config',
							'input'		=> 'Input',
							'benchmark'	=> 'Benchmark',
							'uri'		=> 'URI',
							'output'	=> 'Output',
							'lang'		=> 'Language',
							'router'	=> 'Router'
							);
		
		foreach ($classes as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		// In PHP 5 the Loader class is run as a discreet
		// class.  In PHP 4 it extends the Controller
		if (floor(phpversion()) >= 5)
		{
			$this->load =& load_class('Loader');
			$this->load->_ci_autoloader();
		}
		else
		{
			$this->_ci_autoloader();
			
			// sync up the objects since PHP4 was working from a copy
			foreach (array_keys(get_object_vars($this)) as $attribute)
			{
				if (is_object($this->$attribute))
				{
					$this->load->$attribute =& $this->$attribute;
				}
			}
		}
	}
}
// END _Controller class

/* End of file Controller.php */
/* Location: ./system/libraries/Controller.php */