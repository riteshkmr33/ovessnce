<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Application\Model\Api;
use Application\Model\FrontEndAuth;
use Application\Model\ForgetPassword,
    Application\Form\ForgetPasswordForm,
    Application\Form\RegisterForm;

class GoogleloginController extends AbstractActionController {

    public function indexAction() {
		//session_start();
		$google_login = new Container('google');
		$google_client_id 		= $this->getServiceLocator()->get('config')['gplus_keys']['google_client_id'];
		$google_client_secret 	= $this->getServiceLocator()->get('config')['gplus_keys']['google_client_secret'];
		$google_redirect_url 	= $this->getServiceLocator()->get('config')['gplus_keys']['google_redirect_url'];
		$google_developer_key 	= $this->getServiceLocator()->get('config')['gplus_keys']['google_developer_key'];
		
		$gClient = new \Google_Client();
		
		$gClient->setClientId($google_client_id);
		$gClient->setClientSecret($google_client_secret);
		$gClient->setRedirectUri($google_redirect_url);
		$gClient->setDeveloperKey($google_developer_key);
		$google_oauthV2 = new \Google_Oauth2Service($gClient);
		$id = (int) $this->params()->fromRoute('code', 0);
		//var_dump($id); 
		if ($_GET['code']) 
			{ 
				$gClient->authenticate($_GET['code']);
				//$_SESSION['token'] = $gClient->getAccessToken();
				$google_login->token = $gClient->getAccessToken();
				//header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
				//return;
			} 
		/*print"<pre>";
		print_r($google_oauthV2);*/ 
		//print_r($_SESSION);
		//die;
		if (isset($google_login->token))
			{ 
				$gClient->setAccessToken($google_login->token);
			}

			
			if ($gClient->getAccessToken()) 
			{
				  //For logged in user, get details from google using access token
				  $user 				= $google_oauthV2->userinfo->get();
				  $user_id 				= $user['id'];
				  $first_name 			= filter_var($user['given_name'], FILTER_SANITIZE_SPECIAL_CHARS);
				  $last_name 			= filter_var($user['family_name'], FILTER_SANITIZE_SPECIAL_CHARS);
				  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
				  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
				  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL); 
				  $google_login->token 	= $gClient->getAccessToken();
				  
				  
				  //Check user exist or not 					
					$api = new Api();
					$api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
					$url = $api_url."/api/useractivity/";
					$data = array('op'=>'check_exist_user','user_name'=>$email,'email'=>$email);
					$res = $api->curl($url, $data, "POST"); 
										
					//Set value in session
					
					$google_login->first_name=$first_name;
					$google_login->last_name=$last_name;
					$google_login->user_name=$email;
					$google_login->email=$email;
					$google_login->social_id=$user_id;
					// END :- Set value in session
					
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
                                                    $session->last_login = $content['last_login_prev'];
                                                    $session->user_data = $content;
                                                    
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
				  
				  
			}
			else 
			{
				//For Guest user, get google login url
				$authUrl = $gClient->createAuthUrl(); 
				return ($this->redirect()->toUrl($authUrl));
			}
			
			
			die();
		
		
		 
		
    }
}
