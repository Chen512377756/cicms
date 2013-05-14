<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Layout class from Hex
 *
 * @package codeigniter
 * @author Hex
 */
class Layout extends Controller
{
	var $_parent_name = '';

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Layout()
	{
		$this->_init();
	}

	// 初始化模块
	function _init()
	{
		// If the magic __get() or __set() methods are used in a Module references can't be used.
		$this->_assign_libraries( (method_exists($this, '__get') OR method_exists($this, '__set')) ? FALSE : TRUE );

		// We don't want to assign the Module object to itself when using the
		// assign_libraries function below so we'll grab the name of the Module parent
		$this->_parent_name = get_class($this);

		log_message('debug', $this->_parent_name . " Module Class Initialized");
	}
	/**
	 * Assign Libraries
	 *
	 * Creates local references to all currently instantiated objects
	 * so that any syntax that can be legally used in a controller
	 * can be used within Modules.
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
					// Needed to prevent reference errors with some configurations
					$this->$key = '';
					$this->$key =& $CI->$key;
				}
				else
				{
					$this->$key = $CI->$key;
				}
			}
		}
	}

	// Load models that layout required
	// if we load them in the Layout constructor method
	// we must create the layout instance first
	// thus, there is queue method to handle it
	function _model_load_queue()
	{
		$CI =& get_instance();
		if (isset($CI->_ci_module_model_queue))
		{
			foreach ($CI->_ci_module_model_queue as $key=>$value)
			{
				list($module_class_name, $model, $name) = explode(',', $value);
				$CI->$module_class_name->$model = new $name();
				$CI->$module_class_name->$model->_assign_libraries();
				unset($CI->_ci_module_model_queue[$key]);
			}
		}
	}
}
