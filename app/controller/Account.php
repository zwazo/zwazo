<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;

class Account extends ControllerAbstract {
	
	public function indexAction() {
		/* fallback */
	}
	
	public function loginAction() {
		$next_uri = $this->_app['request']->getRequestUri();
// echo 'goto: '.$next_uri.'<br/>';
		$this->vars('next_uri', $next_uri);

		$data = array(
			
		);
		
		$form = $this->_app['form.factory']->createBuilder('form', $data)
        ->add('login')
        ->add('password','password')
        ->getForm();
		  
		$form->handleRequest( $this->_app['request'] );
		
		if ($form->isValid()) {
			$data = $form->getData();

			if ('zwazo' == $data['login']) {
				$this->_app['session']->set('user', $data['login']);
				return $this->_app->redirect( $next_uri );
			} else {
				
			}
		}

		$this->vars('form', $form->createView());
	}
	
	public function logoutAction() {
		
	}
	
}