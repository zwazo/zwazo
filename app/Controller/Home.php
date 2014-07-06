<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;

/** 
 * Homepage
 */
class Home extends ControllerAbstract {

	public function indexAction() {
		$this->script('unslider.min.js');
		
		$this->reset('stylesheet');
		$this->stylesheet('unslider.css');
		$this->stylesheet('p_home.css');
	}

}