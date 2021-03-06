<?php
namespace App\Controller;

use App\Controller\ControllerAbstract;
use App\Helper;

class Account extends ControllerAbstract {
	
	public function indexAction() {
		/* fallback */
	}
	
	public function loginAction() {
		$next_uri = $this->_app['request']->getRequestUri();
// echo 'goto: '.$next_uri.'<br/>';
		$this->vars('next_uri', $next_uri);

		$data = array();
		$form = $this->_app['form.factory']->createBuilder('form', $data)
        ->add('login')
        ->add('password','password')
        ->getForm();

		$form->handleRequest( $this->_app['request'] );
		
		if ($form->isValid()) {
			$data = $form->getData();
			
			$success = false;
			$salt = 'Zwa:7c0n8L3u00jv';
			try {
				if ( preg_match('/[^a-z0-9\.\-_@]+/i', $data['login']) 
				  OR preg_match('/[^a-z0-9\.\-\[\]\*\(\)\{\}\+\|\!\/\$_@#&%=]+/i', $data['password'])
				) {
					$this->vars('error', 'Le login ou le mot de passe contiennent des caractères non autorisés');
				} else {
					$sQuery = 'SELECT COUNT(*) AS qte FROM `'.Helper\Conf::DB_PREFIX.'account` WHERE login=:login AND password=:psswd';
// echo 'pwd: '.md5($data['password'].$salt);
					$res = Helper\Db::query($sQuery, array(
						':login'  => $data['login']
						,':psswd' => md5($data['password'].$salt)
					));
					if (is_object($res)) {
						$aAccount = $res->fetch( \PDO::FETCH_ASSOC );
						$res = null;
						if (1 == $aAccount['qte']) {
							$success = true;
						}
					}
				}
			} catch (Exception $e) {
				$success = false;
			}
			
			if (true == $success) {
			// make session
				$this->_app['session']->set('user', $data['login']);
				if (Helper\Conf::SITE_ADMIN == $data['login']) { 
					$this->_app['session']->set('role', 'admin');
				} else {
					$this->_app['session']->set('role', 'friend');
				}
				return $this->_app->redirect( $next_uri );
			} else {
				
			}
		}

		$this->vars('form', $form->createView());
	}
	
	
}