<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;
use App\Data;

/** 
 * News Feed
 */
class News extends ControllerAbstract {

	/**
	 *
	 */
	public function indexAction() {
		$sQuery = "SELECT id,title,description FROM `".Helper\Conf::DB_PREFIX."news` "
			." ORDER BY id DESC "
			." LIMIT 20 "
		;
		$tmp = Helper\Db::query($sQuery);
		$this->vars('Results', Helper\Db::query($sQuery) );
	}

	/**
	 *
	 */
	public function editAction() {
		$id = $this->_app['request']->get('ref','');
		
		$data = array( 'id' => 'new' );
		if ( !empty($id) && is_numeric($id) ) {
			$q  = Helper\Db::query( "SELECT * FROM `".Helper\Conf::DB_PREFIX."news` WHERE id=".$id );
			if ( $q->rowCount() ) {
				$data = $q->fetch( \PDO::FETCH_ASSOC );
			}
		}

		$form = $this->_app['form.factory']->createBuilder('form', $data, array(
			'csrf_protection' => false // disable csrf ... fails here
		))
		->add('id', 'hidden')
		->add('title', 'text', array(
			'max_length' => 50,
			'attr'     => array( 'size' => 50 )
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
				':title' => $data['title'],
				':desc'  => $data['description'],
			);

			try {

				if ( !is_numeric($data['id']) ) {
					$sQuery = 'INSERT INTO `'.Helper\Conf::DB_PREFIX.'news`(`title`,`description`,`create_time`) VALUES (:title,:desc,NOW())';
					$stmt = Helper\Db::query($sQuery, $vars);
				} else {
					$sQuery = 'UPDATE `'.Helper\Conf::DB_PREFIX.'news` SET title=:title ,description=:desc WHERE id=:id';
					$vars[':id'] = $data['id'];
					$stmt = Helper\Db::query($sQuery, $vars);
				}
				
				if ( 0 == $stmt->rowCount() ) {
					$aInfos = $stmt->errorInfo();
					$this->vars('error', 'SQLSTATE['.$aInfos[0].']['.$aInfos[1].'] '.$aInfos[2]);
				} else {
				// redirect somewhere
					return $this->_app->redirect( 
						$this->_app['request']->getBaseUrl() . '/'.$this->_controller().'/list'
					);
				}
			} catch (Exception $e) {
				$this->vars('error', $e->getMessage());
			}
			
			
		}
		
		$this->vars('form', $form->createView());
	}

	public function listAction() {
		$res = Helper\Db::query("SELECT COUNT(*) AS tot FROM `".Helper\Conf::DB_PREFIX."news` ");
		$this->vars('iResults', 0);
		if (is_object($res)) {
			$_tmp = $res->fetch( \PDO::FETCH_ASSOC );
			$this->vars('iResults', $_tmp['tot']);
			$_tmp = null;
		}

		$sQuery = "SELECT id,title FROM `".Helper\Conf::DB_PREFIX."news` "
			." ORDER BY id DESC "
		;
		$tmp = Helper\Db::query($sQuery);
		$this->vars('Results', Helper\Db::query($sQuery) );
	}
	
	public function delAction() {
		
		$id = $this->_app['request']->get('ref','');
		
		if ( !empty($id) && is_numeric($id) ) {
			Helper\Db::query( "DELETE FROM `".Helper\Conf::DB_PREFIX."news` WHERE id=".$id );
		}
		
		return $this->_app->redirect( 
			$this->_app['request']->getBaseUrl() . '/'.$this->_controller().'/list'
		);
		
	}
}