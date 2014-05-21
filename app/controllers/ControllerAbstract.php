<?php
namespace Zwazo\Ctrl;

abstract class ControllerAbstract {

	protected $_vars = array();

	/**
	 * 
	 */
	public function __construct(&$app) {
		$this->app = &$app;
		$this->_controller = strtolower(basename(get_called_class()));
	}

	/**
	 * 
	 */
	public function assign($var, $val) {
		$this->_vars[ $var ] = $val;
	}

	/**
	 * 
	 */
	public function render() {
		return $this->app['twig']->render($this->_controller.'.html.twig', $this->_vars);
	}

}