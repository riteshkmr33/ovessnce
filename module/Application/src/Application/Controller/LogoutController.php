<?php
/**
 * LogoutController.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package Controller
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Model\FrontEndAuth;

class LogoutController extends AbstractActionController
{
    
    public function indexAction($redirectUrl = array('controller'=>'login')) {
        //var_dump($redirectUrl); die;
        $auth = new FrontEndAuth();
        $auth->logout($redirectUrl);
        $auth->wordpress_logout();
        
        return $this->redirect()->toRoute(null,$redirectUrl);
        /*
            return $this->redirect()->toRoute(null,array('controller'=>'Login', 'action' => 'dashboard','params' =>$params));
        * 
        */
    }
}
