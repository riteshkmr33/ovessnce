<?php

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

class ForgetUsernameController extends AbstractActionController
{

    public function indexAction()
    {

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

                $url = $api_url . "/api/useractivity/";
                $data['email'] = $request->getPost('email');
                $res = $api->curl($url, $data, "GET");

                //echo '<pre>'; var_dump($res); die;
                if ($res->getStatusCode() == 200) {
                    $content = json_decode($res->getBody(), true);
                    //echo '<pre>'; var_dump($content[0]); die;
                    $model = new Common;
                    $model->sendMail($api_url, $content[0]['email'], '', 22, '', array('/{{user_name}}/i', '/{{username}}/i'), array($content[0]['first_name'].' '.$content[0]['last_name'], '<strong>'.$content[0]['user_name'].'</strong>'));
                    $error = false;
                    $msg = "A mail has been send to " . $content[0]['email'] . " ,Please check ";
                } else {
                    $error = true;
                    $msg = "User with given email does not exist";
                }
            }
        }

        $view = new ViewModel(array('form' => $form, 'error' => $error, 'msg' => $msg));
        $view->setTemplate('application/forgetPassword/index.phtml');
        return $view;
    }

}
