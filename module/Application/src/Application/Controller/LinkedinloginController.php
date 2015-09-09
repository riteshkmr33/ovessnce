<?php

/**
 * ForgetpasswordController.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package Controller
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
//use \social_auth\facebook\Facebook;
use Zend\View\Model\ViewModel;
use HappyR\LinkedIn\LinkedIn;
use Zend\Session\Container;
use Application\Model\Api;
use Application\Model\FrontEndAuth;
use Application\Model\ForgetPassword,
    Application\Form\ForgetPasswordForm,
    Application\Form\RegisterForm;

class LinkedinloginController extends AbstractActionController
{

    public function indexAction()
    {

        $linkedIn = new LinkedIn($this->getServiceLocator()->get('config')['linkedin_keys']['aapId'], $this->getServiceLocator()->get('config')['linkedin_keys']['app_secret']);

        if ($linkedIn->isAuthenticated()) {
            //we know that the user is authenticated now. Start query the API
            $user = $linkedIn->api('v1/people/~:(id,firstName,lastName,emailAddress)');

            //Check user exist or not 					
            $api = new Api();
            $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
            $url = $api_url . "/api/useractivity/";
            $data = array('op' => 'check_exist_user', 'user_name' => $user['emailAddress'], 'email' => $user['emailAddress']);
            $res = $api->curl($url, $data, "POST");

            //Set value in session
            $lk_login = new Container('linkedin');
            $lk_login->first_name = $user['firstName'];
            $lk_login->last_name = $user['lastName'];
            $lk_login->user_name = $user['emailAddress'];
            $lk_login->email = $user['emailAddress'];
            $lk_login->social_id = $user['id'];
            // END :- Set value in session
            //$facebook1->destroySession(); die;
            // For new user
            if ($res->getStatusCode() != 200) {
                $redirectUrl = 'register/index#register-a';
                return $this->redirect()->toUrl($redirectUrl);
            }
            // For existing user
            else {
                $auth = new FrontEndAuth();
                $session = new Container('frontend');
                $content = json_decode($res->getBody(), true);
                $session->status_id = $content['status_id'];
                $session->userid = $content['id'];
                if ($content['status_id'] != 3) {

                    $session->first_name = $content['first_name'];
                    $session->last_name = $content['last_name'];
                    $session->email = $content['email'];
                    $session->user_name = $content['user_name'];
                    $session->user_type_id = $content['user_type_id'];
                    $session->user_data = $content;
                    $session->last_login = $content['last_login_prev'];
                    $auth->wordpress_login($fbuname);
                    //$redirectUrl = ($session->user_type_id == 4)?array('controller' => 'practitioner', 'action' => 'list'):array('controller' => 'practitioner', 'action' => 'dashboard');

                    $redirectUrl = ($session->user_type_id == 4) ? 'list' : 'dashboard';
                    $url = "practitioner/" . $redirectUrl;
                    return $this->redirect()->toUrl($url);
                } else {


                    return $this->redirect()->toRoute('login', array('action' => 'index'));
                }
            }
        } elseif ($linkedIn->hasError()) {
            echo "User canceled the login.";
            exit();
        }

        //if not authenticated
        $url = $linkedIn->getLoginUrl();
        return ($this->redirect()->toUrl($url));

        die;
    }

}
