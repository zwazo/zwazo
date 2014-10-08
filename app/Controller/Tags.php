<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;
use App\Data;

class Tags extends ControllerAbstract {

	public function indexAction() {
		
		
	}
	
	public function editAction() {
		
		$id = $this->_app['request']->get('ref','');
		
		$data = array( 'id' => 'new' );
		if ( !empty($id) && is_numeric($id) ) {
			$q  = Helper\Db::query( "SELECT * FROM `".Helper\Conf::DB_PREFIX."tag` WHERE id=".$id );
			if ( $q->rowCount() ) {
				$data = $q->fetch( \PDO::FETCH_ASSOC );
			}
		}

		$form = $this->_app['form.factory']->createBuilder('form', $data, array(
			'csrf_protection' => false // disable csrf ... fails here
		))
		->add('id', 'hidden')
		->add('label', 'text', array(
			'max_length' => 30,
			'attr'     => array( 'size' => 30 )
		))
		->getForm();
		
		$form->handleRequest( $this->_app['request'] );
		
		if ($form->isValid()) {
			$data = $form->getData();
			
			$vars = array(
				':label' => $data['label'],
			);

			try {

				if ( !is_numeric($data['id']) ) {
					$sQuery = 'INSERT INTO `'.Helper\Conf::DB_PREFIX.'tag`(`label`) VALUES (:label)';
					$stmt = Helper\Db::query($sQuery, $vars);
				} else {
					$sQuery = 'UPDATE `'.Helper\Conf::DB_PREFIX.'tag` SET label=:label WHERE id=:id';
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
		
		$this->stylesheet('admin.css');
	}

	public function listAction() {
	
		$res = Helper\Db::query("SELECT COUNT(*) AS tot FROM `".Helper\Conf::DB_PREFIX."tag` ");
		$this->vars('iResults', 0);
		if (is_object($res)) {
			$_tmp = $res->fetch( \PDO::FETCH_ASSOC );
			$this->vars('iResults', $_tmp['tot']);
			$_tmp = null;
		}

		$sQuery = "SELECT id,label FROM `".Helper\Conf::DB_PREFIX."tag` "
			." ORDER BY id DESC "
		;
		$this->vars('Results', Helper\Db::query($sQuery) );
		
		$this->stylesheet('admin.css');
	}
	
	public function delAction() {
		
		$id = $this->_app['request']->get('ref','');
		
		if ( !empty($id) && is_numeric($id) ) {
			Helper\Db::query( "DELETE FROM `".Helper\Conf::DB_PREFIX."tag` WHERE id=".$id );
		}
		
		return $this->_app->redirect( 
			$this->_app['request']->getBaseUrl() . '/'.$this->_controller().'/list'
		);
		
	}
}