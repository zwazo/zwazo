<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;

class Aa extends ControllerAbstract {

	const CONTROLLER = 0;
	const ACTION     = 1;
	const ID         = 2;

	private $_x  = array();
	
	/**
	 *
	 */
	public function indexAction() {
		
	}
	
	/**
	 *
	 */
	public function editrAction() {
		$this->vars('layout', 'iframed.twig');
		$this->stylesheet('editor.css');
		
		$x  = $this->_app['request']->get('x','');
		$this->_x = explode('/', $x);
		
		if (empty($this->_x[ self::CONTROLLER ])) {
			$this->_x[ self::CONTROLLER ] = 'editr';
			$this->_x[ self::ACTION ] = '404';
		} else if (empty($this->_x[ self::ACTION ])) {
			$this->_x[ self::ACTION ] = 'list';
		}

		$this->vars('editr_ctrl', $this->_x[ self::CONTROLLER ]);
		$this->vars('editr_act', $this->_x[ self::ACTION ]);
		$method = '_'.$this->_x[ self::CONTROLLER ].'_'.$this->_x[ self::ACTION ];
		if (!method_exists($this, $method)) {
			$content = $this->_editr_404();
		} else {
			$content = call_user_func(array($this, $method));
		}
		
	}
	
	/**
	 *
	 */
	private function _editr_404() {
		return 'HTTP/1.1 404 Page Not Found';
	}
	
	/**
	 *
	 */
	private function _bookmarks_list() {
		
	}
	
	/**
	 *
	 */
	private function _bookmarks_edit() {
		$this->vars('edit_case', 'create');
	}
	
	
}