<?php
/**
 * ForgetpasswordController.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package Controller
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Application\Model\Api;
use Application\Model\Common;
use Application\Model\FrontEndAuth;
use Application\Model\ForgetPassword,
    Application\Form\ForgetPasswordForm;
    use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class ForgetpasswordController extends AbstractActionController {

    public function indexAction() {
		
		$api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
		
        $api = new Api();
        $auth = new FrontEndAuth();
        $error = "";
        $redirectUrl = array('controller' => 'index');

        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute(null, $redirectUrl);
        }

        $form = new ForgetPasswordForm();
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $model = new ForgetPassword();
            $form->setInputFilter($model->getInputFilter());

            $data = $request->getPost()->toArray();

            $form->setData($data);

            if ($form->isValid()) {
                unset($data['submit']);
                $random_password = $model->generateRandomPassword();
                $data['password'] = $random_password;
                $session = new Container('frontend');
                
                $url = $api_url."/api/useractivity/";
                $data['op'] = 'forgotpassword';
                $res = $api->curl($url, $data, "POST");
                
                //var_dump($res); die;
                if ($res->getStatusCode() == 200) {
					$model = new Common;
					if ($template = $model->emailTemplate($api_url, 3)) {
                        $content = json_decode($res->getBody(), true);
                       // '{{user_first_name}}', '{{username}}', '{{password}}'  
                       //$content['first_name'], $content['user_name'], $random_password), $template['content']);                     
                        $patterns= array('/{{user_first_name}}/i','/{{username}}/i','/{{password}}/i');
                        $replacements = array($content['first_name'],'<strong>'.$content['user_name'].'</strong>','<strong>'.$random_password.'</strong>','<strong>'.$getservices.'</strong>');
                        $mail = new Message();
                        $transport = new \Zend\Mail\Transport\Sendmail();
                        $html = new MimePart(preg_replace($patterns,$replacements, $template['content']));

                        $html->type = "text/html";

                        $body = new MimeMessage();
                        $body->setParts(array($html));
						$url = $api_url."/api/useractivity/";
						$data = array('email'=>$content['email'],'password'=>$random_password);
						$data['op'] = 'resetpassword';
						$res = $api->curl($url, $data, "POST");
						if($res->getStatusCode() == 200){
							$mail->setBody($body)
									 ->setFrom($template['fromEmail'], 'Ovessence')
									 ->addTo($content['email'], '')
									 ->setSubject($template['subject']);
							$transport->send($mail);
							$error = false;
							$msg = "A mail has been send to ". $content['email'] ." ,Please check ";
						}else{
							$error = true;
							$msg = "Unable to set password..!! ";
						}
                       // echo json_encode(array('status' => 1, 'msg' => 'Business card sent to the email address..!!'));
					} else {
						$error = true;
						$msg = "Unable to find mail template..!!";
                        //echo json_encode(array('status' => 0, 'msg' => 'Unable to find mail template..!!'));
					  }
					
					/*
                    $content = json_decode($res->getBody(), true);
                                        
                    $forget_pass_url = $api_url."/api/emailtemplate/3/";
                    
                    $forget_pass_res = $api->curl($forget_pass_url, array(), "GET");
                    //print_r($forget_pass_res); die;
                    if($forget_pass_res->getStatusCode() == 200) {
                        $template = json_decode($forget_pass_res->getBody(), true);
                        
                        $template_data = str_replace(array('{{user_first_name}}', '{{username}}', '{{password}}' ), array($content['first_name'], $content['user_name'], $random_password), $template['content']);
                        
                        $wp_user_detail = $auth->wordpress_user_detail($content['user_name']);
                        $auth->wordpress_set_password($random_password, $wp_user_detail->ID);
                        
                        $reset_data['op'] = 'resetpassword';
                        $reset_data['email'] = $content['email'];
                        $reset_data['password'] = $random_password;
						$reset_res = $api->curl($url, $reset_data, "POST");
                        
                        $mail = new \Zend\Mail\Message();                 
                    
                        $html = new \Zend\Mime\Part($template_data);
                        $html->type = "text/html";

                        $body = new \Zend\Mime\Message();
                        $body->setParts(array($html));
						//$content['email']
                        $mail->setBody($body)
                             ->setFrom($template['fromEmail'], 'Ovessence')
                             ->addTo('shivania@clavax.us', $content['first_name'].' '.$content['last_name'])
                             ->setSubject($template['subject']);
                        $transport = new \Zend\Mail\Transport\Sendmail($template['fromEmail']);
                        $transport->send($mail);                    
                        $error = "A mail has been send to ". $content['email'] ." ,Please check ";
                    
                    }*/
                    
                } else {
    
                    if($res->getStatusCode() == "404"){
						$error = true;
						$msg = "User with given email does not exist";
					}else{
						$error = true;
						$msg = $res->getReasonPhrase();
					}
                }
                
            }
        }

        $view = new ViewModel(array('form' => $form, 'error' => $error,'msg'=>$msg));
        $view->setTemplate('application/forgetPassword/index.phtml');
        return $view;
    }
}
