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
 * Loader Class
 *
 * Loads views and files
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Loader
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class CI_Loader {

	// All these are set automatically. Don't mess with them.
	var $_ci_ob_level;
	var $_ci_view_path		= '';
	var $_ci_is_php5		= FALSE;
	var $_ci_is_instance 	= FALSE; // Whether we should use $this or $CI =& get_instance()
	var $_ci_cached_vars	= array();
	var $_ci_classes		= array();
	var $_ci_loaded_files	= array();
	var $_ci_models			= array();
	var $_ci_helpers		= array();
	var $_ci_plugins		= array();
	var $_ci_varmap			= array('unit_test' => 'unit', 'user_agent' => 'agent');
	
	var $_ci_layout_path = '';  // current layout path
	var $_ci_layout_class = '';  // current layout controller name
	

	/**
	 * Constructor
	 *
	 * Sets the path to the view files and gets the initial output buffering level
	 *
	 * @access	public
	 */
	function CI_Loader()
	{	
		$this->_ci_is_php5 = (floor(phpversion()) >= 5) ? TRUE : FALSE;
		// Xtend: setup view path for cooperate mode
		$this->_ci_view_path = FCPATH;
		// $this->_ci_view_path = APPPATH.'views/';
		$this->_ci_ob_level  = ob_get_level();
				
		log_message('debug', "Loader Class Initialized");
	}
	
	function &get_instance()
	{
		$CI =& get_instance();

		if (!empty($this->_ci_layout_path))
		{
			$layout_class_name = $this->_ci_layout_class;
			return $CI->$layout_class_name;
		}
		else
		{
			return $CI;
		}
	}
	
	function layout($layout_uri, $vars = array(), $return = FALSE)
	{
		if ($layout_uri == '')
		{
			return;
		}

		$layout_uri = trim($layout_uri, '/');

		// Load the routes.php file.
		@include(APPPATH.'config/routes'.EXT);
		$my_routes = ( ! isset($route) OR ! is_array($route)) ? array() : $route;
		unset($route);

		// Set the default controller so we can display it in the event
		// the URI doesn't correlated to a valid controller.
		$default_controller = ( ! isset($my_routes['default_controller']) OR $my_routes['default_controller'] == '') ? FALSE : strtolower($my_routes['default_controller']);

		// Is the layout in a sub-folder? If so, parse out the filename and path.
		if (strpos($layout_uri, '/') === FALSE)
		{
			$path = '';
			$layout = $layout_uri;
			$controller = $default_controller;
			$method = 'index';
			$segments = array();
		}
		else
		{
			$segments = explode('/', $layout_uri);

			if (file_exists(APPPATH.'layouts/'.$segments[0].'/controllers/'.$segments[1].EXT))
			{
				$path = '';
				$layout = $segments[0];
				$controller = $segments[1];
				$method = isset($segments[2]) ? $segments[2] : 'index';
			}
			// Is the controller in a sub-folder?
			elseif (is_dir(APPPATH.'layouts/'.$segments[0].'/'.$segments[1].'/controllers'))
			{
				// Set the directory and remove it from the segment array
				$path = $segments[0];
				$segments = array_slice($segments, 1);

				if (count($segments) > 0)
				{
					// has any layout in sub folder?
					if (is_dir(APPPATH.'layouts/'.$path.'/'.$segments[0].'/controllers'))
					{
						$layout = $segments[0];
						$controller = isset($segments[1]) ? $segments[1] : $default_controller;
						$method = isset($segments[2]) ? $segments[2] : 'index';
					}
				}
				else
				{
					show_error('Unable to locate the layout you have specified: '.$path);
				}
			}
			else
			{
				show_error('Unable to locate the layout you have specified: '.$layout_uri);
			}

			if ($path != '')
			{
				$path = rtrim($path, '/') . '/';
			}
		}

		$layout = strtolower($layout);

		// class naming role: Layout_dir_contoller
		$class_name = 'Layout_'.str_replace('/','_',$path.$layout.'_'.$c);

		if ( ! file_exists(APPPATH.'layouts/'.$path.$layout.'/controllers/'.$controller.EXT))
		{
			show_error('Unable to locate the layout you have specified: '.$path.$layout.'/controllers/'.$controller.EXT);
		}

		if ( ! class_exists('layout'))
		{
			load_class('layout', FALSE);
		}

		$this->_ci_layout_path = $path.$layout;
		$this->_ci_layout_class = $class_name;

		$CI =& get_instance();
		if (!isset($CI->$class_name))
		{
			// 模块有自己的目录
			require_once(APPPATH.'layouts/'.$path.$layout.'/controllers/'.$controller.EXT);

			$CI->$class_name = new $class_name();
			$CI->$class_name->_model_load_queue();
		}
		else
		{
			$CI->$class_name->_init();
			$CI->$class_name->_model_load_queue();
		}

		if (method_exists($CI->$class_name, $method))
		{
			ob_start();

			$last_layout_path = $this->_ci_layout_path;
			$last_layout_class = $this->_ci_layout_class;

			// Call the requested method.
			// Any URI segments present (besides the class/function) will be passed to the method for convenience
			$output = call_user_func_array(array($CI->$class_name, $method), $this->_ci_object_to_array($vars));

			$this->_ci_layout_path = $last_layout_path;
			$this->_ci_layout_class = $last_layout_class;

			$buffer = ob_get_contents();
			@ob_end_clean();

			$result = ($output) ? $output : $buffer;

			if ($return === TRUE)
			{
				return $result;
			}
			else
			{
				if (ob_get_level() > 0)
				{
					echo $result;
				}
				else
				{
					global $OUT;
					$OUT->append_output($result);
				}
			}
		}
		else
		{
			show_error('Unable to locate the '.$method.' method you have specified: '.$class_name);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Class Loader
	 *
	 * This function lets users load and instantiate classes.
	 * It is designed to be called from a user's app controllers.
	 *
	 * @access	public
	 * @param	string	the name of the class
	 * @param	mixed	the optional parameters
	 * @param	string	an optional object name
	 * @return	void
	 */	
	function library($library = '', $params = NULL, $object_name = NULL)
	{
		if ($library == '')
		{
			return FALSE;
		}

		if ( ! is_null($params) AND ! is_array($params))
		{
			$params = NULL;
		}

		if (is_array($library))
		{
			foreach ($library as $class)
			{
				$this->_ci_load_class($class, $params, $object_name);
			}
		}
		else
		{
			$this->_ci_load_class($library, $params, $object_name);
		}
		
		$this->_ci_assign_to_models();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Model Loader
	 *
	 * This function lets users load and instantiate models.
	 *
	 * @access	public
	 * @param	string	the name of the class
	 * @param	string	name for the model
	 * @param	bool	database connection
	 * @param	mixed	extra param
	 * @return	void
	 */	
	function model($model, $name = '', $db_conn = FALSE, $extra = NULL)
	{		
		if (is_array($model))
		{
			foreach($model as $babe)
			{
				if(is_array($name)){
					$this->model($babe,$name);
				}else{
					$this->model($babe);
				}
			}
			return;
		}

		if ($model == '')
		{
			return;
		}
	
		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (strpos($model, '/') === FALSE)
		{
			$path = '';
		}
		else
		{
			$x = explode('/', $model);
			$model = end($x);			
			unset($x[count($x)-1]);
			$path = implode('/', $x).'/';
		}

		if ($name == '')
		{
			$name = $model;
		}
		//mod of layout
		//$model = strtolower($name);

		$is_inside_layout = ($this->_ci_layout_path != '');
		$is_layout = false;

		$my_model_path = APPPATH.'layouts/'.$this->_ci_layout_path.'/models/'.$path.$model.EXT;
		$common_model_path = APPPATH.'models/'.$path.$model.EXT;

		if ($is_inside_layout and file_exists($my_model_path))
		{
			$is_layout = true;
			$model_path = $my_model_path;
		}
		else if(file_exists($common_model_path))
		{
			$is_layout = false;
			$model_path = $common_model_path;
		}
		else
		{
			show_error('Unable to locate the model you have specified: '.$model);
		}

		if ($is_inside_layout)
		{
			// current layout class name
			$layout_class_name = $this->_ci_layout_class;
		}

		// add on options parameter feature
		if(is_array($name)){
			$options=$name;
			if(is_null($extra)){
				$name='';
			}else{
				$name=$db_conn;
				$db_conn=$extra;
			}
		}
	
		if ($is_inside_layout and $is_layout)
		{
			$CI =& get_instance();

			if ($name == '')
			{
				$name = str_replace('/', '_', $this->_ci_layout_path.'_'.$model);
			}

			if (isset($CI->$layout_class_name->$name))
			{
				return;
			}
		}
		else
		{
			if ($name == '')
			{
				$name = $model_name;
			}

			if (in_array($name, $this->_ci_models, TRUE))
			{
				return;
			}

			$CI =& get_instance();
			if (isset($CI->$name))
			{
				show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
			}
		}
		//end of mod
				
		if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
		{
			if ($db_conn === TRUE)
				$db_conn = '';
		
			$CI->load->database($db_conn, FALSE, TRUE);
		}
	
		if ( ! class_exists('Model'))
		{
			load_class('Model', FALSE);
		}

		require_once($model_path);
		// require_once(APPPATH.'models/'.$path.$model.EXT);

		$model = ucfirst($model);
		
		if ($is_inside_layout and $is_layout)
		{
			if (isset($CI->$layout_class_name))
			{
				if(isset($options)){
					$CI->$layout_class_name->$model = new $name($options);
				}else{
					$CI->$layout_class_name->$model = new $name();
				}
				$CI->$layout_class_name->$model->_assign_libraries();
			}
			else
			{
				if (!isset($CI->_ci_layout_model_queue))
				{
					$CI->_ci_layout_model_queue = array();
				}
				array_push($CI->_ci_layout_model_queue, "$layout_class_name,$model,$name");
			}
		}
		else
		{
			if(isset($options)){
				$CI->$name = new $model($options);
			}else{
				$CI->$name = new $model();
			}
			$CI->$name->_assign_libraries();

			if ($is_inside_layout)
			{
				$CI->$layout_class_name->$name = $CI->$name;
			}

			$this->_ci_models[] = $name;
		}
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Database Loader
	 *
	 * @access	public
	 * @param	string	the DB credentials
	 * @param	bool	whether to return the DB object
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	object
	 */	
	function database($params = '', $return = FALSE, $active_record = FALSE)
	{
		// Grab the super object
		$CI =& get_instance();
		
		// Do we even need to load the database class?
		if (class_exists('CI_DB') AND $return == FALSE AND $active_record == FALSE AND isset($CI->db) AND is_object($CI->db))
		{
			return FALSE;
		}	
	
		require_once(BASEPATH.'database/DB'.EXT);

		if ($return === TRUE)
		{
			return DB($params, $active_record);
		}
		
		// Initialize the db variable.  Needed to prevent   
		// reference errors with some configurations
		$CI->db = '';
		
		// Load the DB class
		$CI->db =& DB($params, $active_record);	
		
		// Assign the DB object to any existing models
		$this->_ci_assign_to_models();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Load the Utilities Class
	 *
	 * @access	public
	 * @return	string		
	 */		
	function dbutil()
	{
		if ( ! class_exists('CI_DB'))
		{
			$this->database();
		}
		
		$CI = $this->get_instance();
		// $CI =& get_instance();

		// for backwards compatibility, load dbforge so we can extend dbutils off it
		// this use is deprecated and strongly discouraged
		$CI->load->dbforge();
	
		require_once(BASEPATH.'database/DB_utility'.EXT);
		require_once(BASEPATH.'database/drivers/'.$CI->db->dbdriver.'/'.$CI->db->dbdriver.'_utility'.EXT);
		$class = 'CI_DB_'.$CI->db->dbdriver.'_utility';

		$CI->dbutil =& instantiate_class(new $class());

		$CI->load->_ci_assign_to_models();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Load the Database Forge Class
	 *
	 * @access	public
	 * @return	string		
	 */		
	function dbforge()
	{
		if ( ! class_exists('CI_DB'))
		{
			$this->database();
		}
		
		$CI = $this->get_instance();
		// $CI =& get_instance();
	
		require_once(BASEPATH.'database/DB_forge'.EXT);
		require_once(BASEPATH.'database/drivers/'.$CI->db->dbdriver.'/'.$CI->db->dbdriver.'_forge'.EXT);
		$class = 'CI_DB_'.$CI->db->dbdriver.'_forge';

		$CI->dbforge = new $class();
		
		$CI->load->_ci_assign_to_models();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load View
	 *
	 * This function is used to load a "view" file.  It has three parameters:
	 *
	 * 1. The name of the "view" file to be included.
	 * 2. An associative array of data to be extracted for use in the view.
	 * 3. TRUE/FALSE - whether to return the data or load it.  In
	 * some cases it's advantageous to be able to return data so that
	 * a developer can process it in some way.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	function view($view, $vars = array(), $return = FALSE)
	{
		if ($this->_ci_layout_path == '')
		{
			return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
		}
		
		$ext = pathinfo($view, PATHINFO_EXTENSION);
		$view = ($ext == '') ? $view.EXT : $view;
		$path = APPPATH.'layouts/'.$this->_ci_layout_path.'/views/'.$view;

		if (file_exists($path))
		{
			return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_path' => $path, '_ci_return' => $return));
		}
		else
		{
			return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load File
	 *
	 * This is a generic file loader
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function file($path, $return = FALSE)
	{
		return $this->_ci_load(array('_ci_path' => $path, '_ci_return' => $return));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set Variables
	 *
	 * Once variables are set they become available within
	 * the controller class and its "view" files.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function vars($vars = array(), $val = '')
	{
		if ($val != '' AND is_string($vars))
		{
			$vars = array($vars => $val);
		}
	
		$vars = $this->_ci_object_to_array($vars);
	
		if (is_array($vars) AND count($vars) > 0)
		{
			foreach ($vars as $key => $val)
			{
				$this->_ci_cached_vars[$key] = $val;
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Helper
	 *
	 * This function loads the specified helper file.
	 *
	 * @access	public
	 * @param	mixed
	 * @return	void
	 */
	function helper($helpers = array())
	{
		if ( ! is_array($helpers))
		{
			$helpers = array($helpers);
		}
	
		foreach ($helpers as $helper)
		{		
			$helper = strtolower(str_replace(EXT, '', str_replace('_helper', '', $helper)).'_helper');

			if (isset($this->_ci_helpers[$helper]))
			{
				continue;
			}
			
			$ext_helper = APPPATH.'helpers/'.config_item('subclass_prefix').$helper.EXT;

			// Is this a helper extension request?			
			if (file_exists($ext_helper))
			{
				$base_helper = BASEPATH.'helpers/'.$helper.EXT;
				
				if ( ! file_exists($base_helper))
				{
					show_error('Unable to load the requested file: helpers/'.$helper.EXT);
				}
				
				include_once($ext_helper);
				include_once($base_helper);
			}
			elseif (file_exists(APPPATH.'helpers/'.$helper.EXT))
			{ 
				include_once(APPPATH.'helpers/'.$helper.EXT);
			}
			else
			{		
				if (file_exists(BASEPATH.'helpers/'.$helper.EXT))
				{
					include_once(BASEPATH.'helpers/'.$helper.EXT);
				}
				else
				{
					show_error('Unable to load the requested file: helpers/'.$helper.EXT);
				}
			}

			$this->_ci_helpers[$helper] = TRUE;
			log_message('debug', 'Helper loaded: '.$helper);	
		}		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Helpers
	 *
	 * This is simply an alias to the above function in case the
	 * user has written the plural form of this function.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function helpers($helpers = array())
	{
		$this->helper($helpers);
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Loads a language file
	 *
	 * @access	public
	 * @param	array
	 * @param	string
	 * @return	void
	 */
	function language($file = array(), $lang = '')
	{
		$CI = $this->get_instance();
		// $CI =& get_instance();

		if ( ! is_array($file))
		{
			$file = array($file);
		}

		foreach ($file as $langfile)
		{	
			$CI->lang->load($langfile, $lang);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Loads a config file
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function config($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$CI = $this->get_instance();
		// $CI =& get_instance();
		$CI->config->load($file, $use_sections, $fail_gracefully);
	}

	// --------------------------------------------------------------------
		
	/**
	 * Loader
	 *
	 * This function is used to load views and files.
	 * Variables are prefixed with _ci_ to avoid symbol collision with
	 * variables made available to view files
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _ci_load($_ci_data)
	{
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val)
		{
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}

		// Set the path to the requested file
		if ($_ci_path == '')
		{
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;
			$_ci_path = $this->_ci_view_path.$_ci_file;
		}
		else
		{
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		
		if ( ! file_exists($_ci_path))
		{
			show_error('Unable to load the requested file: '.$_ci_file);
		}
	
		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5
		
		if ($this->_ci_is_instance())
		{
			$_ci_CI = $this->get_instance();
			// $_ci_CI =& get_instance();
			foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var)
			{
				if ( ! isset($this->$_ci_key))
				{
					$this->$_ci_key =& $_ci_CI->$_ci_key;
				}
			}
		}

		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->load_vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */	
		if (is_array($_ci_vars))
		{
			$this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
		}
		extract($this->_ci_cached_vars);
				
		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be
		 * post-processed by the output class.  Why do we
		 * need post processing?  For one thing, in order to
		 * show the elapsed page load time.  Unless we
		 * can intercept the content right before it's sent to
		 * the browser and then stop the timer it won't be accurate.
		 */
		ob_start();
				
		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.
		
		if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE)
		{
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else
		{
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name
		}
		
		log_message('debug', 'File loaded: '.$_ci_path);
		
		// Return the file data if requested
		if ($_ci_return === TRUE)
		{		
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 *
		 */	
		if (ob_get_level() > $this->_ci_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			// PHP 4 requires that we use a global
			global $OUT;
			$OUT->append_output(ob_get_contents());
			@ob_end_clean();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Load class
	 *
	 * This function loads the requested class.
	 *
	 * @access	private
	 * @param 	string	the item that is being loaded
	 * @param	mixed	any additional parameters
	 * @param	string	an optional object name
	 * @return 	void
	 */
	function _ci_load_class($class, $params = NULL, $object_name = NULL)
	{	
		// Get the class name, and while we're at it trim any slashes.  
		// The directory path can be included as part of the class name, 
		// but we don't want a leading slash
		$class = str_replace(EXT, '', trim($class, '/'));

		// Was the path included with the class name?
		// We look for a slash to determine this
		$subdir = '';
		if (strpos($class, '/') !== FALSE)
		{
			// explode the path so we can separate the filename from the path
			$x = explode('/', $class);	
			
			// Reset the $class variable now that we know the actual filename
			$class = end($x);
			
			// Kill the filename from the array
			unset($x[count($x)-1]);
			
			// Glue the path back together, sans filename
			$subdir = implode($x, '/').'/';
		}

		// We'll test for both lowercase and capitalized versions of the file name
		foreach (array(ucfirst($class), strtolower($class)) as $class)
		{
			$subclass = APPPATH.'libraries/'.$subdir.config_item('subclass_prefix').$class.EXT;

			// Is this a class extension request?			
			if (file_exists($subclass))
			{
				$baseclass = BASEPATH.'libraries/'.ucfirst($class).EXT;
				
				if ( ! file_exists($baseclass))
				{
					log_message('error', "Unable to load the requested class: ".$class);
					show_error("Unable to load the requested class: ".$class);
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($subclass, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied.  If so, we'll
					// return a new instance of the object
					if ( ! is_null($object_name))
					{
						$CI =& get_instance();
						if ( ! isset($CI->$object_name))
						{
							return $this->_ci_init_class($class, config_item('subclass_prefix'), $params, $object_name);			
						}
					}
					
					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}
	
				include_once($baseclass);				
				include_once($subclass);
				$this->_ci_loaded_files[] = $subclass;
	
				return $this->_ci_init_class($class, config_item('subclass_prefix'), $params, $object_name);			
			}
		
			// Lets search for the requested library file and load it.
			$is_duplicate = FALSE;		
			for ($i = 1; $i < 3; $i++)
			{
				$path = ($i % 2) ? APPPATH : BASEPATH;	
				$filepath = $path.'libraries/'.$subdir.$class.EXT;
				
				// Does the file exist?  No?  Bummer...
				if ( ! file_exists($filepath))
				{
					continue;
				}
				
				// Safety:  Was the class already loaded by a previous call?
				if (in_array($filepath, $this->_ci_loaded_files))
				{
					// Before we deem this to be a duplicate request, let's see
					// if a custom object name is being supplied.  If so, we'll
					// return a new instance of the object
					if ( ! is_null($object_name))
					{
						$CI =& get_instance();
						if ( ! isset($CI->$object_name))
						{
							return $this->_ci_init_class($class, '', $params, $object_name);
						}
					}
				
					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}
				
				include_once($filepath);
				$this->_ci_loaded_files[] = $filepath;
				return $this->_ci_init_class($class, '', $params, $object_name);
			}
		} // END FOREACH

		// One last attempt.  Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir == '')
		{
			$path = strtolower($class).'/'.$class;
			return $this->_ci_load_class($path, $params);
		}
		
		// If we got this far we were unable to find the requested class.
		// We do not issue errors if the load call failed due to a duplicate request
		if ($is_duplicate == FALSE)
		{
			log_message('error', "Unable to load the requested class: ".$class);
			show_error("Unable to load the requested class: ".$class);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Instantiates a class
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @param	string	an optional object name
	 * @return	null
	 */
	function _ci_init_class($class, $prefix = '', $config = FALSE, $object_name = NULL)
	{	
		// Is there an associated config file for this class?
		if ($config === NULL)
		{
			// We test for both uppercase and lowercase, for servers that
			// are case-sensitive with regard to file names
			if (file_exists(APPPATH.'config/'.strtolower($class).EXT))
			{
				include_once(APPPATH.'config/'.strtolower($class).EXT);
			}			
			elseif (file_exists(APPPATH.'config/'.ucfirst(strtolower($class)).EXT))
			{
				include_once(APPPATH.'config/'.ucfirst(strtolower($class)).EXT);
			}
		}
		
		if ($prefix == '')
		{			
			if (class_exists('CI_'.$class)) 
			{
				$name = 'CI_'.$class;
			}
			elseif (class_exists(config_item('subclass_prefix').$class)) 
			{
				$name = config_item('subclass_prefix').$class;
			}
			else
			{
				$name = $class;
			}
		}
		else
		{
			$name = $prefix.$class;
		}
		
		// Is the class name valid?
		if ( ! class_exists($name))
		{
			log_message('error', "Non-existent class: ".$name);
			show_error("Non-existent class: ".$class);
		}
		
		// Set the variable name we will assign the class to
		// Was a custom class name supplied?  If so we'll use it
		$class = strtolower($class);
		
		if (is_null($object_name))
		{
			$classvar = ( ! isset($this->_ci_varmap[$class])) ? $class : $this->_ci_varmap[$class];
		}
		else
		{
			$classvar = $object_name;
		}

		// Save the class name and object name		
		$this->_ci_classes[$class] = $classvar;

		// Instantiate the class		
		$CI =& get_instance();
		if ($config !== NULL)
		{
			$CI->$classvar = new $name($config);
		}
		else
		{		
			$CI->$classvar = new $name;
		}	
	} 	
	
	// --------------------------------------------------------------------
	
	/**
	 * Autoloader
	 *
	 * The config/autoload.php file contains an array that permits sub-systems,
	 * libraries, plugins, and helpers to be loaded automatically.
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _ci_autoloader()
	{	
		if(defined('SUBPATH')&&file_exists(APPPATH.'config/autoload/'.rtrim(SUBPATH,'/').EXT)){
			include_once(APPPATH.'config/autoload/'.rtrim(SUBPATH,'/').EXT);
		}else{
			include_once(APPPATH.'config/autoload'.EXT);
		}
		
		if ( ! isset($autoload))
		{
			return FALSE;
		}
		
		// Load any custom config file
		if (count($autoload['config']) > 0)
		{
			$CI = $this->get_instance();
			// $CI =& get_instance();
			foreach ($autoload['config'] as $key => $val)
			{
				$CI->config->load($val);
			}
		}		

		// Autoload helpers and languages
		foreach (array('helper', 'language') as $type)
		{			
			if (isset($autoload[$type]) AND count($autoload[$type]) > 0)
			{
				$this->$type($autoload[$type]);
			}		
		}

		// A little tweak to remain backward compatible
		// The $autoload['core'] item was deprecated
		if ( ! isset($autoload['libraries']))
		{
			$autoload['libraries'] = $autoload['core'];
		}
		
		// Load libraries
		if (isset($autoload['libraries']) AND count($autoload['libraries']) > 0)
		{
			// Load the database driver.
			if (in_array('database', $autoload['libraries']))
			{
				$this->database();
				$autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
			}

			// Load all other libraries
			foreach ($autoload['libraries'] as $item)
			{
				$this->library($item);
			}
		}		

		// Autoload models
		if (isset($autoload['model']))
		{
			$this->model($autoload['model']);
		}

	}
	
	// --------------------------------------------------------------------

	/**
	 * Assign to Models
	 *
	 * Makes sure that anything loaded by the loader class (libraries, plugins, etc.)
	 * will be available to models, if any exist.
	 *
	 * @access	private
	 * @param	object
	 * @return	array
	 */
	function _ci_assign_to_models()
	{
		if (count($this->_ci_models) == 0)
		{
			return;
		}
	
		if ($this->_ci_is_instance())
		{
			$CI = $this->get_instance();
			// $CI =& get_instance();
			foreach ($this->_ci_models as $model)
			{			
				$CI->$model->_assign_libraries();
			}
		}
		else
		{		
			foreach ($this->_ci_models as $model)
			{			
				$this->$model->_assign_libraries();
			}
		}
	}  	

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class variables to array key/vals
	 *
	 * @access	private
	 * @param	object
	 * @return	array
	 */
	function _ci_object_to_array($object)
	{
		return (is_object($object)) ? get_object_vars($object) : $object;
	}

	// --------------------------------------------------------------------

	/**
	 * Determines whether we should use the CI instance or $this
	 *
	 * @access	private
	 * @return	bool
	 */
	function _ci_is_instance()
	{
		if ($this->_ci_is_php5 == TRUE)
		{
			return TRUE;
		}
	
		global $CI;
		return (is_object($CI)) ? TRUE : FALSE;
	}

}

/* End of file Loader.php */
/* Location: ./system/libraries/Loader.php */