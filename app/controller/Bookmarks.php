<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;

class Bookmarks extends ControllerAbstract {

	public function indexAction() {
		
		$q = Helper\Db::query("SELECT url,label,id,description FROM bookmark ORDER BY id DESC LIMIT 10");
		if ( $q->rowCount() ) {
			$this->vars('Results', $q);
		}
		
		
	}

}