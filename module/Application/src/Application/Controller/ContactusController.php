<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Application\Model\Contact;
use Application\Model\Consumers;
use Application\Form\ContactForm;
use Zend\Mail;
use Application\Model\Common;

class ContactusController extends AbstractActionController
{

    public function indexAction()
    {
        $contact_form = new ContactForm();
        $request = $this->getRequest();
        $common = new Common;
        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];

        if ($request->isXmlHttpRequest()) {

            $email = ($request->getPost('email') != '') ? $request->getPost('email') : '';
            $first_name = ($request->getPost('first_name') != '') ? $request->getPost('first_name') : '';
            $last_name = ($request->getPost('last_name') != '') ? $request->getPost('last_name') : '';
            $message = ($request->getPost('message') != '') ? $request->getPost('message') : '';
            $phone = ($request->getPost('phone') != '') ? $request->getPost('phone') : '';

            /* $api = new Api();
              $url = $api_url."/api/contactus/";
              $data = array('email' => $email, 'first_name'=> $first_name, 'last_name'=> $last_name, 'phone'=> $phone, 'message'=> $message );

              $res = $api->curl($url, $data, "POST");
              if($res->getStatusCode() == 200){
              $msgbody = json_decode($res->getBody(), true);
              }else {
              $msgbody = '';
              } */

            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $consumer = new Consumers();
            // Start :- Get admin email id
            $res = $consumer->getConsumerdetails($api_url, 1);
            if (count($res) > 0) {
                $adminEmailId = $res['email'];
            }

            if ($common->sendMail($api_url, $adminEmailId, $email, 23, '', array('/{{first_name}}/i', '/{{user_name}}/i', '/{{email}}/i', '/{{phone}}/i', '/{{message}}/i'), array($first_name, $first_name . " " . $last_name, $email, $phone, $message))) {
                $error = false;
                $msg = "Success!! Mail send sucessfully";
            } else {
                $error = true;
                $msg = "Failed to send mail. Please try again later..!!";
            }
            /* $mail = new Mail\Message();
              $mail->setBody($messagebody);
              $mail->addTo(/*$adminEmailId'rtshkmr302@gmail.com', 'Name of recipient');
              $mail->setSubject('An ovessence visitor whants to contact you.');

              $transport = new Mail\Transport\Sendmail();
              if($transport->send($mail)){
              $msg = "Success!! Mail send sucessfully";
              }else{
              $error = true;
              $msg = "Error!! Unable to contact.";
              } */
            
            echo json_encode(array('error' => $error, 'msg' => $msg));
            exit;
        }
        $api = new Api();

        $response = $api->curl($api_url . '/api/pages/3/', array(), "GET");

        $content = '{{form}}';
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            $content = $data['content'];
        }
        
        $banners = $common->getBanner($api_url, 7);
        
        return new ViewModel(array(
            'form' => $contact_form,
            'content' => $content,
            'banners' => $banners
        ));
    }

    /* send subscription invitation code starts here */

    public function subscribeNewsletterAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $request = $this->getRequest();
            $errors = array();
            $data = array('email' => $request->getPost('email'));

            if ($data['email'] != "") {
                $api = new Api();
                $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
                $newsletter_res = $api->curl($api_url . "/api/newslettersubscription/", $data, "POST");

                if ($newsletter_res->getStatusCode() == 201) {

                    /* Send email code starts here */
                    $common = $this->getServiceLocator()->get('Application\Model\Common');
                    $pattern = array('/{{email}}/i');
                    $userreplace = array($data['email']);

                    $common->sendMail($api_url, $data['email'], '', 15, '', $pattern, $userreplace);
                    /* Send email code ends here */

                    echo json_encode(array('status' => 1, 'msg' => 'Email for newsletter subscription successfully added..!!'));
                } else {
                    $errors = ($newsletter_res->getStatusCode() != 200 && is_array(json_decode($newsletter_res->getBody(), true))) ? array_merge($errors, json_decode($newsletter_res->getBody(), true)) : $errors;
                    echo json_encode(array('status' => 0, 'errors' => $errors));
                }
            } else {
                $errors['email'] = "Please provide a valid email address.";
                echo json_encode(array('status' => 0, 'errors' => $errors));
            }
        }
        exit;
    }

    /* send subscription invitation code ends here */

    /* unsubscribe for news */

    public function unsubscribeAction()
    {
        $status = array();
        $error = array();
        $token = $this->params()->fromRoute('id');
        if ($token != "" && strlen($token) == 44) {

            $model = new Common();
            $id = $model->getDecode($token);

            if ($id != "" && is_numeric($id)) {

                $api = new Api();
                $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
                $newsletter_res = $api->curl($api_url . "/api/newslettersubscription/" . $id . "/", array("status_id" => 0), "PUT");

                if ($newsletter_res->getStatusCode() == 200) {
                    $status = array('status' => 1, 'msg' => 'Your Email is sucessfully unsubscribe for newsletter..!!');
                } else {
                    $status = array('status' => 0, 'err' => "Sorry!!..Unsubscribe link is broken. Unable to process your request please try again.");
                }
            } else {
                $status = array('status' => 0, 'err' => "Sorry!!..Unsubscribe link is broken. Unable to process your request please try again.");
            }
        } else {
            return $this->redirect()->toUrl('/contact');
        }

        return new ViewModel(array(
            'status' => $status
        ));
    }

}
