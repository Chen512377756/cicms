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
 * Output Class
 *
 * Responsible for sending final output to browser
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Output
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/output.html
 */
class CI_Output {

	var $final_output;
	var $cache_expiration	= 0;
	var $headers 			= array();
	var $enable_profiler 	= FALSE;


	function CI_Output()
	{
		log_message('debug', "Output Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Output
	 *
	 * Returns the current output string
	 *
	 * @access	public
	 * @return	string
	 */	
	function get_output()
	{
		return $this->final_output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set Output
	 *
	 * Sets the output string
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function set_output($output)
	{
		$this->final_output = $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Append Output
	 *
	 * Appends data onto the output string
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function append_output($output)
	{
		if ($this->final_output == '')
		{
			$this->final_output = $output;
		}
		else
		{
			$this->final_output .= $output;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Header
	 *
	 * Lets you set a server header which will be outputted with the final display.
	 *
	 * Note:  If a file is cached, headers will not be sent.  We need to figure out
	 * how to permit header data to be saved with the cache data...
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function set_header($header, $replace = TRUE)
	{
		$this->headers[] = array($header, $replace);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set HTTP Status Header
	 * moved to Common procedural functions in 1.7.2
	 * 
	 * @access	public
	 * @param	int 	the status code
	 * @param	string	
	 * @return	void
	 */	
	function set_status_header($code = '200', $text = '')
	{
		set_status_header($code, $text);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Enable/disable Profiler
	 *
	 * @access	public
	 * @param	bool
	 * @return	void
	 */	
	function enable_profiler($val = TRUE)
	{
		$this->enable_profiler = (is_bool($val)) ? $val : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set Cache
	 *
	 * @access	public
	 * @param	integer
	 * @return	void
	 */	
	function cache($time,$good_segment=null)
	{
		if(!is_null($good_segment)){
			if(!is_array($good_segment)){
				$good_segment=array($good_segment);
			}
			$CI =& get_instance();
			foreach($good_segment as $v){
				$CI->uri->goodseg[$v]=1;
			}
		}
		$this->cache_expiration = ( ! is_numeric($time)) ? 0 : $time;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Output
	 *
	 * All "view" data is automatically put into this variable by the controller class:
	 *
	 * $this->final_output
	 *
	 * This function sends the finalized output data to the browser along
	 * with any server headers and profile data.  It also stops the
	 * benchmark timer so the page rendering speed and memory usage can be shown.
	 *
	 * @access	public
	 * @return	mixed
	 */		
	function _display($output = '')
	{	
		// Note:  We use globals because we can't use $CI =& get_instance()
		// since this function is sometimes called by the caching mechanism,
		// which happens before the CI super object is available.
		global $BM, $CFG;
		
		// --------------------------------------------------------------------
		
		// Set the output data
		if ($output == '')
		{
			$output =& $this->final_output;
		}
		
		// Grab the super object if we can.
		if (function_exists('get_instance'))
		{
			$CI =& get_instance();
		}
		
		
		// --------------------------------------------------------------------
		
		// Do we need to write a cache file?
		if ($this->cache_expiration > 0)
		{
			if(!$CFG->item("htmlize")){
				$this->_write_cache($output);
			}else{
				$t=$this->_write_html($output);

				// Xtend: in js prefetch mode, no need to send output
				if(defined('JS_PREFETCH')){
					exit("<script></script>");
				}

				if(!empty($t)){
					header("Location: ".$t,TRUE,302);
					exit;
				}				
			}
		}
		
		// Xtend: in js prefetch mode, no need to send output
		if(defined('JS_PREFETCH')){
			exit("<script></script>");
		}
		
		// --------------------------------------------------------------------

		// Parse out the elapsed time and memory usage,
		// then swap the pseudo-variables with the data

		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');		
		$output = str_replace('{elapsed_time}', $elapsed, $output);
		
		$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
		$output = str_replace('{memory_usage}', $memory, $output);		

		// --------------------------------------------------------------------
		
		// Is compression requested?
		if ($CFG->item('compress_output') === TRUE)
		{
			if (extension_loaded('zlib'))
			{
				if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
				{
					ob_start('ob_gzhandler');
				}
			}
		}

		// --------------------------------------------------------------------
		
		// Are there any server headers to send?
		if (count($this->headers) > 0)
		{
			foreach ($this->headers as $header)
			{
				@header($header[0], $header[1]);
			}
		}		

		// --------------------------------------------------------------------
		
		// Does the $CI object exist?
		// If not we know we are dealing with a cache file so we'll
		// simply echo out the data and exit.
		if ( ! isset($CI))
		{
			echo $output;
			log_message('debug', "Final output sent to browser");
			log_message('debug', "Total execution time: ".$elapsed);
			return TRUE;
		}
	
		// --------------------------------------------------------------------

		// Do we need to generate profile data?
		// If so, load the Profile class and run it.
		if ($this->enable_profiler == TRUE)
		{
			$CI->load->library('profiler');				
										
			// If the output data contains closing </body> and </html> tags
			// we will remove them and add them back after we insert the profile data
			if (preg_match("|</body>.*?</html>|is", $output))
			{
				$output  = preg_replace("|</body>.*?</html>|is", '', $output);
				$output .= $CI->profiler->run();
				$output .= '</body></html>';
			}
			else
			{
				$output .= $CI->profiler->run();
			}
		}
		
		// --------------------------------------------------------------------

		// Does the controller contain a function named _output()?
		// If so send the output there.  Otherwise, echo it.
		if (method_exists($CI, '_output'))
		{
			$CI->_output($output);
		}
		else
		{
			echo $output;  // Send it to the browser!
		}
		
		log_message('debug', "Final output sent to browser");
		log_message('debug', "Total execution time: ".$elapsed);		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write a Cache File
	 *
	 * @access	public
	 * @return	void
	 */	
	function _write_cache($output)
	{
		$CI =& get_instance();	
		$path = $CI->config->item('cache_path');
	
		$cache_path = ($path == '') ? BASEPATH.'cache/' : $path;
		
		if ( ! is_dir($cache_path) OR ! is_really_writable($cache_path))
		{
			return;
		}
		
		$uri =	$CI->config->item('base_url').
				$CI->config->item('index_page').
				$CI->uri->uri_string();
		
		$cache_path .= md5($uri);

		if ( ! $fp = @fopen($cache_path, FOPEN_WRITE_CREATE_DESTRUCTIVE))
		{
			log_message('error', "Unable to write cache file: ".$cache_path);
			return;
		}
		
		$expire = time() + ($this->cache_expiration * 60);
		
		if (flock($fp, LOCK_EX))
		{
			fwrite($fp, $expire.'TS--->'.$output);
			flock($fp, LOCK_UN);
		}
		else
		{
			log_message('error', "Unable to secure a file lock for file at: ".$cache_path);
			return;
		}
		fclose($fp);
		@chmod($cache_path, DIR_WRITE_MODE);

		log_message('debug', "Cache file written: ".$cache_path);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Update/serve a cached file
	 *
	 * @access	public
	 * @return	void
	 */	
	function _display_cache(&$CFG, &$URI)
	{
		if($CFG->item("htmlize")){
			return $this->_display_html($CFG,$URI);
		}
		
		$cache_path = ($CFG->item('cache_path') == '') ? BASEPATH.'cache/' : $CFG->item('cache_path');
			
		if ( ! is_dir($cache_path) OR ! is_really_writable($cache_path))
		{
			return FALSE;
		}
		
		// Build the file path.  The file name is an MD5 hash of the full URI
		$uri =	$CFG->item('base_url').
				$CFG->item('index_page').
				$URI->uri_string;
				
		$filepath = $cache_path.md5($uri);
		
		if ( ! @file_exists($filepath))
		{
			return FALSE;
		}
	
		if ( ! $fp = @fopen($filepath, FOPEN_READ))
		{
			return FALSE;
		}
			
		flock($fp, LOCK_SH);
		
		$cache = '';
		if (filesize($filepath) > 0)
		{
			$cache = fread($fp, filesize($filepath));
		}
	
		flock($fp, LOCK_UN);
		fclose($fp);
					
		// Strip out the embedded timestamp		
		if ( ! preg_match("/(\d+TS--->)/", $cache, $match))
		{
			return FALSE;
		}
		
		// Has the file expired? If so we'll delete it.
		if (time() >= trim(str_replace('TS--->', '', $match['1'])))
		{ 		
			@unlink($filepath);
			log_message('debug', "Cache file has expired. File deleted");
			return FALSE;
		}

		// Display the cache
		$this->_display(str_replace($match['0'], '', $cache));
		log_message('debug', "Cache file is current. Sending it to browser.");		
		return TRUE;
	}

	function _write_html($output){
		$CI =& get_instance();

		//build uri string file	
		$uri=$CI->router->fetch_class().'/'.$CI->router->fetch_method();
		foreach($CI->uri->segments as $k=>$v){
			if($k<3)continue;
			if(isset($CI->uri->goodseg[$k])){
				$uri.='/'.$v;
			}
		}
		$uri=ltrim($uri,'/\\');
		$uri.='.html';
		
		$file=FCPATH.$uri;
		if(!dirmake($file)){
			log_message('error', "Unable to create prefetch dir: ".dirname($file));
			return '';
		}
		
		if ( ! $fp = @fopen($file, FOPEN_WRITE_CREATE_DESTRUCTIVE)){
			log_message('error', "Unable to write prefetch: ".$file);
			return '';
		}
		
		if (flock($fp, LOCK_EX)){
			$expire=time()+($this->cache_expiration*60);
			$date=date('Y-m-d H:i:s',$expire);
			$t=$CI->config->item('index_page');
			$t=empty($t)?LONG_URL:LONG_URL.$t.'?';
			$jump_url=substr($uri,0,-5);
			$t.=$jump_url.'.js';
			$jump_url.='.html';
			$t="<script>function __cixs(x){var h=document.body,s=document.createElement('script');s.src=x;s.type='text/javascript';s.defer=true;s.id='_cixs'+parseInt((Math.random()*10000000));void(h.appendChild(s))}if(new Date()>new Date('{$date}'.replace(/\-/g,'\/')))__cixs('{$t}');</script><noscript><iframe src='{$t}' width='0' height='0' frameborder='0' scrolling='no'></iframe></noscript><!--ts{$expire}-->";
			fwrite($fp,$output.$t);
			flock($fp,LOCK_UN);
		}else{
			log_message('error', "Unable to secure a file lock for file at: ".$file);
			return '';
		}
		fclose($fp);
		@chmod(dirname($file), DIR_WRITE_MODE);

		log_message('debug', "Prefetch written: ".$file);
		return $jump_url;
	}

	function _display_html(&$CFG, &$URI){
		global $RTR;
		
		//build uri string file	
		$uri=$RTR->fetch_class().'/'.$RTR->fetch_method();
		foreach($URI->segments as $k=>$v){
			if($k<3)continue;
			$uri.='/'.$v;//can't compute goodseg here, so use them all
		}
		$uri=ltrim($uri,'/\\');
		$uri.='.html';
		
		$file=FCPATH.$uri;
		if(!file_exists($file)){
			return false;
		}
		
		$k=substr(file_get_contents($file),-22);
		if(!($t=strrpos($k,'<!--ts'))){
			return false;
		}
		$n=substr($k,$t+6,10);
		if(is_numeric($n)&&intval($n)>time()){
			log_message('debug', "Prefetch is current. Redirecting it to browser.");
			header('location:'.LONG_URL.$uri);
			return true;
		}
		
		if(!@unlink($file)){
			log_message('debug', "Prefetch delete failure: $file");
		}
		
		return false;
	}

}
// END Output Class

/* End of file Output.php */
/* Location: ./system/libraries/Output.php */