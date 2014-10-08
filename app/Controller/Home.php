<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;
use App\Data;

/** 
 * Homepage
 */
class Home extends ControllerAbstract {

	public function indexAction() {
		$this->vars('layout', 'home.html.twig');
		
		
		$filter_publicated = 'm.publicated=1';
		if ( 'admin' == $this->_app['session']->get('role') ) {
			$filter_publicated = "(m.publicated=1 OR m.publicated IS NULL OR m.publicated='')";
		}

		$sQuery = "SELECT m.id,m.title,m.description,m.publicated"
			.",(SELECT GROUP_CONCAT(tm.id_tag) FROM `".Helper\Conf::DB_PREFIX."tag_memo` tm WHERE tm.id_memo=m.id GROUP BY tm.id_memo) AS tags "
			." FROM `".Helper\Conf::DB_PREFIX."memo` m "
			." WHERE {$filter_publicated} " 
			." ORDER BY m.mdate DESC "
			." LIMIT 20 "
		;
		$oMemos = Helper\Db::query($sQuery);
		$aMemos = $aMemosId = array();
		$i = 0; 
		while ($aMemos[] = $oMemos->fetch()) {
			$aMemosId[] = $aMemos[$i]['id'];
			$i++;
		}

		$sQuery = "SELECT DISTINCT t.id,t.label "
			." FROM `".Helper\Conf::DB_PREFIX."tag_memo` tm "
			." INNER JOIN `".Helper\Conf::DB_PREFIX."tag` t ON (t.id=tm.id_tag)"
			." WHERE tm.id_memo IN (".implode(',', $aMemosId).")"
		;
		$aTags = Helper\Db::query($sQuery);

		$this->vars('Memos',  $aMemos);
		$this->vars('Tags',  $aTags);
	}

}