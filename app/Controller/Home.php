<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;

/** 
 * Homepage
 */
class Home extends ControllerAbstract {

	public function indexAction() {
		
		$this->vars('layout', 'home.html.twig');
		
	}

}