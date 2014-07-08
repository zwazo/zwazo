<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;
use App\Data;

class Bookmarks extends ControllerAbstract {

	public function filterAction() {
		$id_tag = $this->_app['request']->get('ref','');
		$this->vars('id_tag', $id_tag);
		$this->indexAction();
	}

	public function indexAction() {
		
		$q = Helper\Db::query("SELECT id,label FROM `".Helper\Conf::DB_PREFIX."tag` ORDER BY label");
		if ( $q->rowCount() ) {
			$this->vars('Tags', $q);
		}
		
		$q = Helper\Db::query("SELECT url,label,id,description FROM `".Helper\Conf::DB_PREFIX."bookmark` ORDER BY id DESC LIMIT 10");
		if ( $q->rowCount() ) {
			$this->vars('Results', $q);
		}
		
	}

	public function editAction() {
		$this->script('jquery-ui.min-core-autocomplete.js');
		$this->stylesheet('jquery-ui.min.css');
		$this->stylesheet('jquery-ui.structure.min.css');
		
		
		$id = $this->_app['request']->get('ref','');
		
		$data = array( 'id' => 'new' );
		if ( !empty($id) && is_numeric($id) ) {
			$q  = Helper\Db::query( "SELECT * FROM `".Helper\Conf::DB_PREFIX."bookmark` WHERE id=".$id );
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
				':url'   => str_replace('http://', '', $data['url']),
				':label' => $data['label'],
				':desc'  => $data['description'],
			);

			try {

				if ( !is_numeric($data['id']) ) {
					$sQuery = 'INSERT INTO `'.Helper\Conf::DB_PREFIX.'bookmark`(`url`,`label`,`description`,`create_time`) VALUES (:url,:label,:desc,NOW())';
					$stmt = Helper\Db::query($sQuery, $vars);
				} else {
					$sQuery = 'UPDATE `'.Helper\Conf::DB_PREFIX.'bookmark` SET url=:url, label=:label ,description=:desc WHERE id=:id';
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
		$res = Helper\Db::query("SELECT COUNT(*) AS tot FROM `".Helper\Conf::DB_PREFIX."bookmark` ");
		$this->vars('iResults', 0);
		if (is_object($res)) {
			$_tmp = $res->fetch( \PDO::FETCH_ASSOC );
			$this->vars('iResults', $_tmp['tot']);
			$_tmp = null;
		}

		$sQuery = "SELECT id,label,url FROM `".Helper\Conf::DB_PREFIX."bookmark` "
			." ORDER BY id DESC "
		;
		$this->vars('Results', Helper\Db::query($sQuery) );
	}
	
	public function delAction() {
		
		$id = $this->_app['request']->get('ref','');
		
		if ( !empty($id) && is_numeric($id) ) {
			Helper\Db::query( "DELETE FROM `".Helper\Conf::DB_PREFIX."bookmark` WHERE id=".$id );
		}
		
		return $this->_app->redirect( 
			$this->_app['request']->getBaseUrl() . '/'.$this->_controller().'/list'
		);
		
	}
}