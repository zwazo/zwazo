<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;
use App\Data;

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
		$x  = $this->_app['request']->get('x','');
		$this->_x = explode('/', $x);
		
		if (empty($this->_x[ self::CONTROLLER ])) {
			$this->_x[ self::CONTROLLER ] = 'editr';
			$this->_x[ self::ACTION ] = '404';
		} else if (empty($this->_x[ self::ACTION ])) {
			$this->_x[ self::ACTION ] = 'list';
		}
		
		$this->stylesheet( $this->_x[ self::CONTROLLER ].'.css' );
		$this->stylesheet('editor.css');
		
		$this->vars('layout', 'iframed.twig');
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
		
		$res = Helper\Db::query("SELECT COUNT(*) AS tot FROM `bookmark` ");
		$this->vars('iResults', 0);
		if (is_object($res)) {
			$_tmp = $res->fetch( \PDO::FETCH_ASSOC );
			$this->vars('iResults', $_tmp['tot']);
			$_tmp = null;
		}

		$sQuery = "SELECT id,label,url FROM `bookmark` "
			." ORDER BY id DESC "
		;
		$this->vars('Results', Helper\Db::query($sQuery) );
		
	}
	
	/**
	 *
	 */
	private function _bookmarks_edit() {

		$data = array( 'id' => 'new' );
		if ( !empty($this->_x[ self::ID ]) && is_numeric($this->_x[ self::ID ]) ) {
			$q  = Helper\Db::query( "SELECT * FROM `bookmark` WHERE id=".$this->_x[ self::ID ] );
			if ( $q->rowCount() ) {
				$data = $q->fetch( \PDO::FETCH_ASSOC );
			}
		}

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
					$stmt = Helper\Db::query($sQuery, $vars);
				} else {
					$sQuery = 'UPDATE bookmark SET url=:url, label=:label ,description=:desc WHERE id=:id';
					$vars[':id'] = $data['id'];
					$stmt = Helper\Db::query($sQuery, $vars);
				}
				
				if ( 0 == $stmt->rowCount() ) {
					$aInfos = $stmt->errorInfo();
					$this->vars('error', 'SQL ERROR: '.$aInfos[2].' (SQLState: '.$aInfos[0].', Error:'.$aInfos[1].')');
				} else {
				// redirect somewhere
					return $this->_app->redirect( 
						$this->_app['request']->getBaseUrl() . '/aa/editr?x='
						.$this->_x[ self::CONTROLLER ].'/list'
					);
				}
			} catch (Exception $e) {
				$this->vars('error', $e->getMessage());
			}
			
			
		}
		
		$this->vars('form', $form->createView());
	}
	
	/**
	 *
	 */
	private function _bookmarks_del() {
		
		if ( !empty($this->_x[ self::ID ]) && is_numeric($this->_x[ self::ID ]) ) {
			Helper\Db::query( "DELETE FROM `bookmark` WHERE id=".$this->_x[ self::ID ] );
		}
		
		return $this->_app->redirect( 
			$this->_app['request']->getBaseUrl() . '/aa/editr?x='
			.$this->_x[ self::CONTROLLER ].'/list'
		);
	}
	

}