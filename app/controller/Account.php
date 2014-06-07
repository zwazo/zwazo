<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;

class Account extends ControllerAbstract {
	
	public function indexAction() {
		/* fallback */
	}
	
	public function loginAction() {
		$next_uri = $this->_app['request']->getRequestUri();
		echo $next_uri;
		$this->vars('next_uri', $next_uri);

		$form = $this->_app['form.factory']->createBuilder('form', $data)
			->add('login')
			->add('password')
			->getForm();

		$form->handleRequest( $this->_app['request'] );

		if ($form->isValid()) {
			$data = $form->getData();
			$this->_app['session']->set('user', $data['login']);
			
			
			return $this->_app->redirect( $next_uri );
		}
		
		$this->vars('form', $form->createView());
	}
	
	public function logoutAction() {
		
	}
	
}