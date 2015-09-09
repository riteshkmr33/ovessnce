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
use Zend\Session\Container;
use Application\Model\Api;
use Application\Model\FrontEndAuth;
use Application\Model\ForgetPassword,
    Application\Form\ForgetPasswordForm,
    Application\Form\RegisterForm;

class FacebookloginController extends AbstractActionController {

    public function indexAction() {
		
        $keys = $this->getServiceLocator()->get('config')['fb_keys'];
        $facebook = new \Facebook($keys);
        $user = $facebook->getUser();

        if ($user) { 
            try {
                  $user_profile = $facebook->api('/me');
                  //Check user exist or not 					
                  $api = new Api();

                  $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
                  $url = $api_url."/api/useractivity/";
                  $data = array('op'=>'check_exist_user','user_name'=>$user_profile['email'],'email'=>$user_profile['email']);
                  $res = $api->curl($url, $data, "POST"); 

                  //Set value in session
                  $fb_login = new Container('facebook');
                  $fb_login->first_name=$user_profile['first_name'];
                  $fb_login->last_name=$user_profile['last_name'];
                  $fb_login->user_name=$user_profile['email'];
                  $fb_login->email=$user_profile['email'];
                  $fb_login->social_id=$user_profile['id'];
                  $fb_login->token=$facebook->getAccessToken();
                  // END :- Set value in session

                  //$facebook1->destroySession(); die;

                  // For new user
                  if($res->getStatusCode()!=200){
                          $redirectUrl = 'register/index#register-a';
                          return $this->redirect()->toUrl($redirectUrl);
                  }
                  // For existing user
                  else{
                          $auth = new FrontEndAuth();
                          $session = new Container('frontend');
                          $content = json_decode($res->getBody(), true);
                          $session->status_id = $content['status_id'];
                          $session->userid = $content['id'];
                          if($content['status_id']!=3){
                            
                            $session->first_name = $content['first_name'];
                            $session->last_name = $content['last_name']; 
                            $session->email = $content['email'];
                            $session->user_name = $content['user_name'];
                            $session->user_type_id = $content['user_type_id'];
                            $session->user_data = $content;
                          
                            $session->last_login = $content['last_login_prev'];
                            $auth->wordpress_login($fbuname);
                            $redirectUrl = ($session->user_type_id == 4)?array('controller' => 'practitioner', 'action' => 'list'):array('controller' => 'practitioner', 'action' => 'dashboard');
                            //$redirectUrl = array('controller' => 'practitioner', 'action' => 'dashboard');
                            return $this->redirect()->toRoute(null, $redirectUrl);
                          }
                          else{
                               
                               $redirectUrl =array('controller' => 'login', 'action' => 'index');
                               return $this->redirect()->toRoute(null, $redirectUrl);
                          }
                  }
            } catch (FacebookApiException $e) {
                  error_log($e);
             $user = null;
            }
       }
       else { 
                  $loginUrl = $facebook->getLoginUrl(array('scope' => 'email,read_stream'));
                return ($this->redirect()->toUrl($loginUrl));
        }
       die;
		
    }
}
