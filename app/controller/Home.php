<?php
namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Home {

	//public function indexAction(Request $request, Application $app) {
	public function indexAction() {
		return 'App\Home::index';
	}

}