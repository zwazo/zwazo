<?php
namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

abstract class ControllerAbstract {

	protected $_name = null;
	protected $_app  = null;
	protected $_vars = array();

	abstract public function indexAction();

	/**
	 *
	 */
	public function init($app) {
		if (is_null($this->_name)) { $this->_name = basename(get_class($this)); }
		$this->_app = $app;

		$this->vars('controller', $app['request']->get('controller'));
		$this->vars('action', $app['request']->get('action'));
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
				return $this->_vars;
			} else if (isset($this->_vars[ $var ])) {
				return $this->_vars[ $var ];
			}
			return null;
		}
	}

}