<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;

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
		if (method_exists($this, $method)) {
			return call_user_func(array($this, $method));
		}
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
		$this->stylesheet('editr/bookmarks.css');
	
		$data = array( 'id' => 'new' );
		
		$form = $this->_app['form.factory']->createBuilder('form', $data, array(
			'csrf_protection' => false // disable csrf ... fails here
		))
		->add('id', 'hidden')
		->add('url', 'text', array(
			'max_length' => 255,
			'attr'     => array( 'size' => 70 )
		))
		->add('label', 'text', array(
			'max_length' => 100,
			'attr'     => array( 'size' => 70 )
		))
		->add('description', 'textarea', array(
			'required'   => false,
			'max_length' => 255,
			'attr'     => array( 'cols' => 85, 'rows' => 3 )
		))
		->getForm();
		
		$form->handleRequest( $this->_app['request'] );
		
		if ($form->isValid()) {
			$data = $form->getData();
			
			$vars = array(
				':url'   => $data['url'],
				':label' => $data['label'],
				':desc'  => $data['description'],
			);
			
			try {

				if ( !is_numeric($data['id']) ) {
					$sQuery = 'INSERT INTO bookmark(`url`,`label`,`description`,`create_time`) VALUES (:url,:label,:desc,NOW())';
					Helper\Db::query($sQuery, $vars);
					$data['id'] = Helper\Db::lastInsertId();
				} else {
					$sQuery = 'UPDATE bookmark SET url=:url, label=:label ,description=:desc WHERE id=:id';
					$vars[':id'] = $data['id'];
					Helper\Db::query($sQuery, $vars);
				}

				// redirect somewhere
				return $this->_app->redirect( 
					$this->_app['request']->getBaseUrl() . '/aa/editr?x='
					.$this->_x[ self::CONTROLLER ].'/list'
				);

			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		
		$this->vars('form', $form->createView());
	}
	
	
}