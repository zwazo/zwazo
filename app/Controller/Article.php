<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;
use App\Data;

/** 
 * Article
 */
class Article extends ControllerAbstract {

	/**
	 *
	 */
	public function indexAction() {
		$id = $this->_app['request']->get('ref','');

		$sQuery = 'SELECT * FROM `'.Helper\Conf::DB_PREFIX.'memo` '
					.' WHERE id=:id';
		$stmt = Helper\Db::query($sQuery, array(':id' => $id));

		$all_green = true;
		if ( 0 == $stmt->rowCount() ) {
			throw new \Exception("woops, quelque chose s'est vraiemnt mal passé");
		}

		$this->vars('Article', $stmt->fetch());
	}

}