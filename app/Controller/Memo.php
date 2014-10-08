<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;
use App\Data;

/** 
 * Memo Feed
 */
class Memo extends ControllerAbstract {

	/**
	 *
	 */
	public function indexAction() {
		
	}

	/**
	 *
	 */
	public function editAction() {
		$id = $this->_app['request']->get('ref','');
		
		$data = array( 'id' => 'new' );
		if ( !empty($id) && is_numeric($id) ) {
			$q  = Helper\Db::query( "SELECT * FROM `".Helper\Conf::DB_PREFIX."memo` WHERE id=".$id );
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
			'max_length' => 255,
			'attr'     => array( 'cols' => 100, 'rows' => 5 )
		))
		->add('publicated', 'choice', array(
			'choices' => array( 0 => 'Non',  1 => 'Oui' ),
			'label' => 'publier',
			'required' => false
		))
		->add('article', 'textarea', array(
			'attr'     => array( 'cols' => 100, 'rows' => 20 ),
			'required' => false
		))
		->getForm();
		
		$form->handleRequest( $this->_app['request'] );
		
		if ($form->isValid()) {
			$data = $form->getData();
			$vars = array(
				':title'       => $data['title'],
				':description' => $data['description'],
				':article'     => $data['article'],
				':publicated'  => $data['publicated'],
			);

			try {
				if ( !is_numeric($data['id']) ) {
					$sQuery = 'INSERT INTO `'.Helper\Conf::DB_PREFIX.'memo`(`cdate`,`mdate`,`title`,`description`,`article`,`publicated`) '
						.' VALUES (NOW(),NOW(),:title,:description,:article,:publicated)';
					$stmt = Helper\Db::query($sQuery, $vars);
				} else {
					$sQuery = 'UPDATE `'.Helper\Conf::DB_PREFIX.'memo` '
						.' SET `title`=:title,`description`=:description,`article`=:article,`mdate`=NOW(),`publicated`=:publicated '
						.' WHERE id=:id';
					$vars[':id'] = $data['id'];
					$stmt = Helper\Db::query($sQuery, $vars);
				}

				$all_green = true;
				if ( 0 == $stmt->rowCount() ) {
					$all_green = false;
					$aInfos = $stmt->errorInfo();
					$this->vars('error', 'SQLSTATE['.$aInfos[0].']['.$aInfos[1].'] '.$aInfos[2]);
				} else if ( !is_numeric($data['id']) ) {					
					$vars[':id'] = $data['id'] = Helper\Db::lastInsertId();
				}

				// tags update
				if ( !empty($data['id']) && is_numeric($data['id']) ) {
					$tags = $this->_app['request']->get('tags',null);
					if (is_array($tags)) {
						Helper\Db::query('DELETE FROM `'.Helper\Conf::DB_PREFIX.'tag_memo` WHERE id_memo='.$data['id']);
						Helper\Db::query(
							"INSERT INTO `".Helper\Conf::DB_PREFIX."tag_memo` (`id_tag`,`id_memo`) "
							." VALUES (".implode(','.$data['id'].'),(', $tags).",".$data['id']."); "
						);
					}
				}

				if (true == $all_green) {
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

	// tags
		$id_memo = ( is_numeric($data['id']) ) ? $data['id'] : '0';
		$sQuery = "SELECT t.id,t.label,tm.id_memo FROM `".Helper\Conf::DB_PREFIX."tag` t "
			." LEFT JOIN `".Helper\Conf::DB_PREFIX."tag_memo` tm ON (tm.id_tag=t.id AND tm.id_memo=".$id_memo." )"
			." ORDER BY t.label ASC "
		;
		$this->vars('Tags', Helper\Db::query($sQuery) );

		$this->stylesheet('admin.css');
	}

	public function listAction() {
		$res = Helper\Db::query("SELECT COUNT(*) AS tot FROM `".Helper\Conf::DB_PREFIX."memo` ");
		$this->vars('iResults', 0);
		if (is_object($res)) {
			$_tmp = $res->fetch( \PDO::FETCH_ASSOC );
			$this->vars('iResults', $_tmp['tot']);
			$_tmp = null;
		}

		$sQuery = "SELECT id,title FROM `".Helper\Conf::DB_PREFIX."memo` "
			." ORDER BY id DESC "
		;
		$this->vars('Results', Helper\Db::query($sQuery) );
		
		$this->stylesheet('admin.css');
	}
	
	public function delAction() {
		
		$id = $this->_app['request']->get('ref','');
		
		if ( !empty($id) && is_numeric($id) ) {
			Helper\Db::query( "DELETE FROM `".Helper\Conf::DB_PREFIX."memo` WHERE id=".$id );
		}
		
		return $this->_app->redirect( 
			$this->_app['request']->getBaseUrl() . '/'.$this->_controller().'/list'
		);
		
	}
}