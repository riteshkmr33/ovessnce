<?php
/**
 * ResetpasswordController.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package Controller
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Application\Model\Api;
use Application\Model\FrontEndAuth;
use Application\Model\ResetPassword,
    Application\Form\ResetPasswordForm;

class ResetpasswordController extends AbstractActionController {

    public function indexAction() {
        $api = new Api();
        $auth = new FrontEndAuth();
        $loginError = "";
        $error = '';
        $redirectUrl = array('controller' => 'index');

        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute(null, $redirectUrl);
        }

        $resettoken = $this->params()->fromRoute('resettoken');
        
        
        if($resettoken === null) {
            return $this->redirect()->toRoute(null, $redirectUrl);
        }
               // var_dump($resettoken); die;
        $resettoken = base64_encode($resettoken);
        $form = new ResetPasswordForm();
        $request = $this->getRequest();
        
        
        
        if ($request->isPost()) {
            
            $model = new ResetPassword();
            $form->setInputFilter($model->getInputFilter());

            $data = $request->getPost()->toArray();
//            var_dump($data['resettoken']);
//            var_dump(base64_decode($data['resettoken'])); die;
            
            $form->setData($data);

            if ($form->isValid()) {
                
                $data['email'] = $data['resettoken'];
                unset($data['submit'], $data['resettoken']);
                
                //$session = new Container('frontend');
                $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
                $url = $api_url."/api/useractivity/";
                $data['op'] = 'resetpassword';
                $res = $api->curl($url, $data, "POST");
                

                if ($res->getStatusCode() == 200) {

//                    $content = json_decode($res->getBody(), true);
//                    
//                    //print_r($content); 
//                    //print_r($content[0]['fields']); 
//                    //die;
//                    $content = json_decode($res->getBody(), true);
//                    $session->userid = $content['id'];
//                    $session->first_name = $content['first_name'];
//                    $session->last_name = $content['last_name']; 
//                    $session->email = $content['email'];
//                    $session->user_name = $content['user_name'];
//                    $session->user_type_id = $content['user_type_id'];
//                    $session->user_data = $content;
//                    
//                    return $this->redirect()->toRoute(null, $redirectUrl);
                } else {
//                    $errors = json_decode($res->getBody(), true);
//                    foreach ($errors as $key=>$value) {
//                        if(isset($value[0])){
//                            $form->setMessages(array(
//                                $key => array(
//                                     $value[0]
//                                )
//                            ));
//                        }
//                    }
                }
                //var_dump($res); die;
            }
        } else {
           // die('else');
            $form->get('resettoken')->setValue($resettoken);
        }
        
        $view = new ViewModel(array('form' => $form, 'error' => $error, 'res'=>$res));
        $view->setTemplate('application/resetPassword/index.phtml');
        return $view;
    }
}
