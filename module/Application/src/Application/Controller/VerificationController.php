<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Practitioners;
use Application\Model\Common;
use Zend\Session\Container;
use Application\Model\FrontEndAuth;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Application\Model\Api;
use Application\Model\Verification,
    Application\Form\VerificationForm;

class VerificationController extends AbstractActionController
{

    // check user data exist in verify table or not
    public function checkuserexistence()
    {
        $api = new Api();
        $session = new Container('frontend');
        $verify_session = new Container('verify');

        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
        $url = $api_url . "/api/userverification/?user_id=" . $session->userid . "&verification_type_id=" . $verify_session->type;
        //$url = $api_url."/api/userverification/?user_id=41&verification_type_id=".$verify_session->type;
        //$url = $api_url."/api/userverification/?user_id=".$session->userid."";

        $res = $api->curl($url, array(), "GET");
        $content = json_decode($res->getBody(), true);

        $senddata = null;
        if (count($content) > 0) {
            // retrieving verification code
            foreach ($content as $userid) {
                $getdatetime = ((strtotime(date("Y-m-d H:i:s")) - strtotime($userid['created_date'])) < 86400) ? true : false;
                $sendcode = $userid['verification_code'];
                $verifyid = $userid['id'];
                $verifystatus = $userid['verification_status'];
            }
            $senddata = array('verifycode' => $sendcode, 'id' => $verifyid, 'timeverification' => $getdatetime, 'status' => $verifystatus);
        }

        return $senddata;
    }

    public function indexAction()
    {
        $auth = new FrontEndAuth();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', array('action' => 'index'));
        }
        $form = new VerificationForm();
        $common = new Common;
        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];

        $session = new Container('frontend');
        if ($session->user_type_id == 3) {

            $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
            $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);

            if (!isset($subscriptionDetails['features']) || !is_array($subscriptionDetails['features']) || !in_array(4, $subscriptionDetails['features'])) {
                $this->flashMessenger()->addErrorMessage("Either you have not subscribed any subscription or your subscription don't have permission to access this section..!!");
                return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
            }
        }

        /*
          $getvalue = $this->checkuserexistence();
          $msg='';
          $class ='';
          if(count($getvalue)>0){
          foreach($getvalue as $data){
          // Email id condition
          if($data['verification_type_id']==1){
          // already verified or verification code recieve (verification pending)
          $flag  = ($data['verification_status']==1)?2:1;
          $msg   = ($flag ==2)?"Your email id already verified":"Verified code already send on your registered email id. Please check it to get verified code";
          $class = ($flag ==2)?"success-msg":"error-msg";
          }
          else{
          // For new user
          $flag = 3;
          }
          // Contact number condition
          if($data['verification_type_id']==2){
          // already verified
          $noflag = ($data['verification_status']==1)?2:1;
          $msg    =   ($noflag ==2)?"Your number already verified":"Verified code already send on your registered number. Please check it to get verified code";
          $class  = ($flag ==2)?"success-msg":"error-msg";
          }
          else{
          // For new user
          $noflag = 3;
          }
          }
          }else{
          // For new user
          $flag = 3;
          } */

        // getting banners
        $banners = $common->getBanner($api_url, 17);

        $view = new ViewModel(array('form' => $form, 'flag' => $flag, 'noflag' => $noflag, 'msg' => $msg, 'class' => $class, 'banners' => $banners/* ,'divtype'=>$divtype,'msg'=>$msg */));
        return($view);
    }

    // Get user email id or contact number
    public function getdetailAction()
    {
        $form = new VerificationForm();
        $getverificationtype = $this->getRequest()->getPost('type');
        // Create a session to store verification type
        $verify_session = new Container('verify');
        $verify_session->type = $getverificationtype;

        $getvalue = $this->checkuserexistence();
        $class = '';
        if (count($getvalue) > 0) {
            $dynamicmsg = ($verify_session->type == 1) ? 'email id' : 'phone number';
            $msg = '';
            // check status is verified or not
            if ($getvalue['status']) {
                $divtype = 1; // show only msg box;
                $msg = 'This ' . $dynamicmsg . ' is already verifed';
                $class = "success-msg";
            } else {
                //Time validation
                if ($getvalue['timeverification']) {
                    $divtype = 2; // show only verify div;
                    $msg = 'Verification code already send on your registered ' . $dynamicmsg . '.Please check it to get verify code';
                    $class = "success-msg";
                }
                // Open new form
                else {

                    $session = new Container('frontend');
                    $value = ($verify_session->type == 1) ? $session->email : '123';
                    $divtype = ($verify_session->type == 1) ? 3 : 4; // show new form;
                }
            }
        } else { // show new form;
            $session = new Container('frontend');
            $value = ($verify_session->type == 1) ? $session->email : '123';
            $divtype = ($verify_session->type == 1) ? 3 : 4;
        }
        echo json_encode(array('msg' => $msg, 'divtype' => $divtype, 'fieldvalue' => $value, 'class' => $class));
        die;
    }

    // Send verify code on mail
    public function sendmailAction()
    {
        $sendcode = rand();
        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $model = new Practitioners;
        $common = new Common;
        if ($session->email == $this->getRequest()->getPost('email')) {

            if ($template = $common->emailTemplate($api_url, 8)) {

                $mail = new Message();
                $transport = new \Zend\Mail\Transport\Sendmail();
                $html = new MimePart(preg_replace('/{{code}}/i', '<strong>' . $sendcode . '</strong>', $template['content']));
                $html->type = "text/html";

                $body = new MimeMessage();
                $body->setParts(array($html));

                $mail->setBody($body)
                        ->setFrom($template['fromEmail'], 'Ovessence')
                        ->addTo($session->email, '')
                        ->setSubject($template['subject']);


                try {
                    $verify_session = new Container('verify');

                    $transport->send($mail);
                    $api = new Api();
                    $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
                    $data = array('user_id' => $session->userid, 'verification_type_id' => $verify_session->type, 'verification_code' => $sendcode, 'created_date' => date('Y-m-d H:i:s'));

                    $getvalue = $this->checkuserexistence();
                    if (count($getvalue) > 0) {
                        // verification code  time out 
                        if ($getvalue['timeverification'] == false) {
                            $url = $api_url . "/api/userverification/" . $getvalue['id'] . "/";
                            $type = "PUT";
                        }
                    } else {
                        // new user
                        $url = $api_url . "/api/userverification/";
                        $type = "POST";
                    }

                    $res = $api->curl($url, $data, $type);

                    //$msg = ($res->getStatusCode()==201)? ('Your verified code was sent to your email'):('Error in create verified code');
                    //$error = ($res->getStatusCode()==201)? false: true;
                    $msg = 'Your verification code was sent to your email';
                    $error = false;
                } catch (Exception $e) {
                    $error = true;
                    $msg = 'Unable to send email';
                }
            } else {
                $error = true;
                $msg = 'Unable to find mail template..!!';
            }
        } else {
            $error = true;
            $msg = 'Sorry this is not registered email id';
        }

        echo json_encode(array('msg' => $msg, 'error' => $error));
        exit;
    }

    // Match get verify code to database record 
    public function verifycodeAction()
    {
        $getvalue = $this->checkuserexistence();
        if ($getvalue['verifycode'] == $this->getRequest()->getPost('code')) {
            $verify_session = new Container('verify');
            $session = new Container('frontend');
            $api = new Api();
            $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
            $data = array('user_id' => $session->userid, 'verification_type_id' => $verify_session->type, 'verification_status' => 1, 'verification_code' => $getvalue['verifycode']);
            $url = $api_url . "/api/userverification/" . $getvalue['id'] . "/";
            $res = $api->curl($url, $data, "PUT");

            //$msg = ($res->getStatusCode()!=201)? ('Successfully verified'):('Error in update data base');
            $msg = 'Successfully verified';
            //$error = ($res->getStatusCode()!=201)? false: true;
            $error = false;
        } else {
            $error = true;
            $msg = 'Please enter  right verify code';
        }
        echo json_encode(array('msg' => $msg, 'error' => $error));
        die;
    }

    //Send msg 
    public function sendmsgAction()
    {
        $request = $this->getRequest();
        $session = new Container('frontend'); // User detail session
        $verify_session = new Container('verify'); // verify type session
        $sendcode = rand();
        $common = new Common;
        $number = $request->getPost('number');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];

        if ($smstemplate = $common->smsTemplate($api_url, 5)) {

            // Create a msg
            $patterns = array('/{{code}}/i',);
            $replacements = array($sendcode);
            $newMessage = preg_replace($patterns, $replacements, $smstemplate['message']);

            $config = $this->getServiceLocator()->get('Config');
            $client = new \Services_Twilio($config['Twilio']['sid'], $config['Twilio']['token']);
            $msg = $client->account->messages->sendMessage($config['Twilio']['fromNumber'], $number, $newMessage, null, array("MessageStatus", "ErrorCode"));
            if ($msg->status == 'queued') {

                // Enter verify code in database
                // maintain sms history
                $url = $api_url . "/api/smshistory/";
                $data = array('subject' => $smstemplate['subject'], 'message' => $newMessage, 'to_user_id' => $session->userid, 'from_user_id' => $session->userid, 'status' => 1);
                $res = $api->curl($url, $data, "POST");
                // End maintain sms history

                $data = array('user_id' => $session->userid, 'verification_type_id' => $verify_session->type, 'verification_code' => $sendcode, 'created_date' => date('Y-m-d H:i:s'));

                $getvalue = $this->checkuserexistence();
                if (count($getvalue) > 0) {
                    // verification code  time out 
                    if ($getvalue['timeverification'] == false) {
                        $url = $api_url . "/api/userverification/" . $getvalue['id'] . "/";
                        $type = "PUT";
                    }
                } else {
                    // new user
                    $url = $api_url . "/api/userverification/";
                    $type = "POST";
                }
                $res = $api->curl($url, $data, $type);
                //$msg = ($res->getStatusCode()==201)? ('Your verified code was sent to your number'):('Error in create verified code');
                //$error = ($res->getStatusCode()==201)? false: true;
                $msg = 'Your verified code was sent to your number';
                $error = false;
                // End :- Enter verify code in database
            } else {
                $error = true;
                $msg = 'Invalid contact number for send verify code';
            }
        } else {
            $error = true;
            $msg = 'Message template not found..!!';
        }
        echo json_encode(array('msg' => $msg, 'error' => $error));
        die;
    }

}

?>
