<?php
namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

abstract class ControllerAbstract {

	protected $_name = null; // use _classname()
	protected $_app  = null;
	protected $_vars = array();
	protected $_css  = array();
	protected $_js   = array();

	abstract public function indexAction();

	/**
	 *
	 */
	public function init($app) {
		if (is_null($this->_name)) { $this->_name = $this->_classname(); }
		$this->_app = $app;

		$controller = $app['request']->get('controller');
		if (empty($controller)) { $controller = strtolower($this->_classname()); }

		// css and js detection
		if (file_exists(ROOT_DIR."/web/css/p_{$controller}.css")) {
			$this->stylesheet("p_{$controller}.css");
		}
		if (file_exists(ROOT_DIR."/web/js/p_{$controller}.js")) {
			$this->script("p_{$controller}.js");
		}
	}

	/**
	 *
	 */
	final function _classname() {
		return basename( str_replace('\\','/',get_class($this)) );
	}
	
	/**
	 *
	 */
	final function _controller() {
		return strtolower($this->_classname());
	}
	
	/**
	 *
	 */
	public function reset($key) {
		if ('stylesheet' == $key) {
			$this->_css = array();
		}
		if ('script' == $key) {
			$this->_js = array();
		}
		if ('vars' == $key) {
			$this->_vars = array();
		}
	}
	
	/**
	 * @param  mixed $var   
	 * @param  mixed $val
	 * @return  
	 */
	public function vars($var=null, $val=null) {
		if (!is_null($val)) {
		// setter
			if (is_string($var)) {
				$this->_vars[ $var ] = $val;
			}
		} else {
		// getters
			if (is_null($var)) { 
				return array(
					'__src_js'  => $this->_js,
					'__src_css' => $this->_css,
				) + $this->_vars;
			} else if (isset($this->_vars[ $var ])) {
				return $this->_vars[ $var ];
			}
			return null;
		}
	}
	
	/**
	 *
	 */
	public function stylesheet($src, $media='screen') {
		$this->_css[ $src ] = array('src' => $src, 'media' => $media);
	}
	public function get_stylesheets() {
		return $this->_css;
	}

	/**
	 *
	 */
	public function script($src) {
		$this->_js[ $src ] = array('src' => $src);
	}
	public function get_scripts() {
		return $this->_js;
	}
}