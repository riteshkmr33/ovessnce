<?php

/**
 * LoginController.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package Controller
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Application\Model\Api;
use Application\Model\FrontEndAuth;
use Application\Model\Login,
    Application\Form\LoginForm,
    Application\Form\RegisterForm;

class LoginController extends AbstractActionController
{

    public function indexAction()
    {
        $api = new Api();
        $auth = new FrontEndAuth();
        $session = new Container('frontend');
        $loginError = "";
        $seturl = $this->getRequest()->getQuery('last_url');

        //$redirectUrl = 'http://blog.ovessence.in/';

        if ($auth->hasIdentity()) { 
             if($session->status_id!=3){
                 //$redirectUrl = array('controller' => 'practitioner', 'action' => 'list'):array('controller' => 'practitioner', 'action' => 'dashboard');
                return ($session->user_type_id == 4) ? $this->redirect()->toRoute('consumer', array('action' => 'dashboard')) : $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
             }
             else {
                 $loginError="Sorry your are suspended to access this site ..!! ";
                 $auth->logout($redirectUrl);
             }
        }
       
        $form = new LoginForm();
        $register_form = new RegisterForm();

        //$forWishlist = new Container('last_url');


        $request = $this->getRequest();
        if ($request->isPost()) {
              
            $login = new Login();
            $form->setInputFilter($login->getInputFilter());

            $data = $request->getPost()->toArray();
			
            $form->setData($data);

            if ($form->isValid()) {
                //unset($data['rememberme'], $data['submit']);

                $bookingData = new Container('bookingData');

                $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];

                $url = $api_url . "/api/useractivity/";
                //$data = array("username" => "sazid1s", "password" => "123456", "op" => "login");

                $data['op'] = 'login';
                $res = $api->curl($url, $data, "POST");
				                   
                //var_dump($res); die;
                if ($res->getStatusCode() == 200) {

                    $content = json_decode($res->getBody(), true);
                    if($content['status_id']!=3){
			
                        //Get verifiy status
                        $url = $api_url . "/api/userverification/?user_id=" .$content['id'];
                        $res = $api->curl($url, array(), "GET");
                        $result = json_decode($res->getBody(), true);
                        $emailStatus =0;
                        $smsStatus =0;
                        
                        if (count($result) > 0) {
                            // retrieving verification code
                            foreach ($result as $userid) {
                                // email validation where 1:- email
                                if($userid['verification_type_id']==1){
                                   $emailStatus = $userid['verification_status'];                                     
                                }
                                // sms validation where 2:- for sms
                                if($userid['verification_type_id']==2){
                                   $smsStatus = $userid['verification_status']; 
                                }
                            }
                        }
                       
                       //End:- Get verifiy status
                        
                        $session->userid = $content['id'];
                        $session->first_name = $content['first_name'];
                        $session->last_name = $content['last_name'];
                        $session->email = $content['email'];
                        $session->user_name = $content['user_name'];
                        $session->user_type_id = $content['user_type_id'];
                        $session->user_data = $content;
                        $session->status_id = $content['status_id'];
                        $session->last_login = $content['last_login_prev'];
                        $session->email_verification_status = $emailStatus;
                        $session->sms_verification_status = $smsStatus;
                        
                        // SET Cookies
                        $time = ($data['rememberme'] == 'yes') ? (time() + 365 * 60 * 60 * 24) : (time() - 4);
                        $cookie = new SetCookie('username', $content['user_name'], $time); // now + 1 year
                        $cookie1 = new SetCookie('password', $data['Pass'], $time); // now + 1 year
                        $cookie2 = new SetCookie('rememberme', $data['rememberme'], $time); // now + 1 year
                        $response = $this->getResponse()->getHeaders();
                        $response->addHeader($cookie);
                        $response->addHeader($cookie1);
                        $response->addHeader($cookie2);
                        // End set cookies

                        if ($data['rememberme'] == 'yes') {
                                setcookie("user_name", $content['user_name'], time() + (60 * 60 * 1));
                                setcookie("password", $data['Pass'], time() + (60 * 60 * 1));  /* expire in 1 hour */
                        }

                        $auth->wordpress_login($session->user_name);

                        $redirectUrl = ($session->user_type_id == 4) ? array('controller' => 'consumer', 'action' => 'dashboard') : array('controller' => 'practitioner', 'action' => 'dashboard');

                        if (isset($bookingData->bookingData)) {
                                return $this->redirect()->toRoute('booking', array('action' => 'schedule', 'id' => $bookingData->sp));
                        } else if ($this->getRequest()->getQuery('lasturl') != '') {
                                return $this->redirect()->toUrl($this->getRequest()->getQuery('lasturl'));
                        } else {
                                return ($session->user_type_id == 4) ? $this->redirect()->toRoute('consumer', array('action' => 'dashboard')) : $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
                        }
                    }//Status check 
                    $loginError = "Sorry your are suspended to access this site ..!! ";
                    //return $this->redirect()->toUrl($redirectUrl);
                } else {
                    $loginError = "Username or Password is incorrect";
                }
            }
        } else {
            $username = ($this->getRequest()->getHeaders()->get('Cookie')->username) ? ($this->getRequest()->getHeaders()->get('Cookie')->username) : '';
            $password = ($this->getRequest()->getHeaders()->get('Cookie')->password) ? ($this->getRequest()->getHeaders()->get('Cookie')->password) : '';
            $rememberme = ($this->getRequest()->getHeaders()->get('Cookie')->password) ? ($this->getRequest()->getHeaders()->get('Cookie')->rememberme) : '';
            $form->get('Pass')->setValue($password);
            $form->get('user_name')->setValue($username);
            $form->get('rememberme')->setValue($rememberme);
        }
        
        return new ViewModel(array('form' => $form, 'register_form' => $register_form, 'loginError' => $loginError, 'setUrl' => $seturl));
    }

}
