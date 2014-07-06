<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;

/** 
 * Homepage
 */
class Home extends ControllerAbstract {

	public function indexAction() {
		
		
		$this->reset('stylesheet');
		$this->stylesheet('unslider.css');
		$this->stylesheet('p_home.css');
		
		$this->reset('script');
		$this->script('unslider.min.js');
		$this->script('p_home.js');
	}

}