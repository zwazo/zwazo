<?php
namespace Zwazo\Ctrl;

require_once __DIR__ . '/ControllerAbstract.php';

class Home extends ControllerAbstract {

	public function indexAction($app) {
		
		return $this->render();
	}

}