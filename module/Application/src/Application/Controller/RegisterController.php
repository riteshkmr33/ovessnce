<?php

/**
 * LoginController.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package Controller
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Application\Model\Api;
use Application\Model\FrontEndAuth;
use Application\Model\Register,
    Application\Form\LoginForm,
    Application\Form\RegisterForm,
    Application\Model\Common;

class RegisterController extends AbstractActionController
{

    public function indexAction()
    {

        $fb_login = new Container('facebook');
        $google_login = new Container('google');
        $linkedin_login = new Container('linkedin');
        $social_media_id = '';
        if (isset($fb_login->social_id)) {
            $common_object = $fb_login;
            $social_media_id = $fb_login->social_id;
        }

        if (isset($google_login->social_id)) {
            $common_object = $google_login;
            $social_media_id = $google_login->social_id;
        }
        if (isset($linkedin_login->social_id)) {
            $common_object = $linkedin_login;
            $social_media_id = $linkedin_login->social_id;
        }

        $api = new Api();
        $auth = new FrontEndAuth();
        $session = new Container('frontend');
        $loginError = "";
        $redirectUrl = array('controller' => 'index');

        if ($auth->hasIdentity()) {
            //$redirectUrl = array('controller'=>'index');
            //$redirectUrl = array('controller'=>'login', 'action' => 'dashboard');
            return $this->redirect()->toRoute(null, $redirectUrl);
        }
        //var_dump($session->user_name); die;
        $common = new Common;
        $form = new LoginForm();
        $register_form = new RegisterForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            //die('hello');
            $register = new Register();
            $register_form->setInputFilter($register->getInputFilter());

            $data = $request->getPost()->toArray();
            
            $register_form->setData($data);
            //$validation_result = json_decode($this->passwordValidation($data['confirm_password']), true);


            if ($register_form->isValid()) {
                $loginError = $validation_result['msg'];
                //if(!$validation_result['error']){ 
                $loginError = '';
                unset($data['confirm_password'], $data['register_submit']);
                //die('hello');

                $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
                $url = $api_url . "/api/users/";
                $data['op'] = 'register';
                $data['social_media_id'] = $social_media_id;
                $data['status_id'] = ($data['user_type_id'] == 4)?9:5;
                
                $res = $api->curl($url, $data, "POST");

                if ($res->getStatusCode() == 201) {
                    
                    $content = json_decode($res->getBody(), true);
                    
                    $common->addChatAccount($this->getServiceLocator()->get('Config')['chatpath']['url'], $content, $data, $request);   // Creating account for live chat
                    $newsletter = ($content['user_type_id'] == 3)?1:4;
                    $common->addFeature($api_url, array('user_id' => $content['id'], 'email' => 1, 'sms' => 1, 'chat' => 0, 'newsletter' => $newsletter));   // Adding user feature setting
                    $common->sendMail($api_url, $content['email'], '', 21, '', array('/{{user_name}}/i'), array($content['first_name'] . ' ' . $content['last_name']));

                    $session->userid = $content['id'];
                    $session->first_name = $content['first_name'];
                    $session->last_name = $content['last_name'];
                    $session->email = $content['email'];
                    $session->user_name = $content['user_name'];
                    $session->user_type_id = $content['user_type_id'];
                    $session->user_data = $content;

                    // adding default subscription
                    if ($session->user_type_id == 3) {
                        $common->addSubscription($api_url, array('user_id' => $session->userid));
                    }

                    $auth->wordpress_create_user($session->user_name, $session->email, $data['confirm_password']);
                    $auth->wordpress_login($session->user_name);
                    $bookingData = new Container('bookingData');
                    //var_dump($res); die;
                    //return ($session->user_type_id == 4) ? $this->redirect()->toRoute('consumer', array('action' => 'dashboard')) : $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
                    if (isset($bookingData->bookingData)) {
                        return $this->redirect()->toRoute('booking', array('action' => 'schedule', 'id' => $bookingData->sp));
                    } else if ($this->getRequest()->getQuery('lasturl') != '') {
                        return $this->redirect()->toUrl($this->getRequest()->getQuery('lasturl'));
                    } else {
                        return ($session->user_type_id == 4) ? $this->redirect()->toRoute('consumer', array('action' => 'dashboard')) : $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
                    }
                    //return $this->redirect()->toRoute(null, $redirectUrl);
                } else {
                    $errors = json_decode($res->getBody(), true);
                    foreach ($errors as $key => $value) {
                        if (isset($value[0])) {
                            $register_form->setMessages(array(
                                $key => array(
                                    $value[0]
                                )
                            ));
                        }
                    }

                    $this->errors = $register_form->getMessages();  // added by Ritesh to get error messages
                }
                //var_dump($errors); die;
                //}// validation condition
            } else {
                $this->errors = $register_form->getMessages();
            }
        }

        $view = new ViewModel(array('form' => $form, 'register_form' => $register_form, 'loginError' => $loginError, 'fb_login' => $common_object, 'errors' => $this->errors));
        $view->setTemplate('application/login/index.phtml');
        return $view;
    }

}
