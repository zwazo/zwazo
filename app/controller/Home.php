<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;

class Home extends ControllerAbstract {

	public function indexAction() {
		$this->vars('layout','home.twig');
		
	}

}