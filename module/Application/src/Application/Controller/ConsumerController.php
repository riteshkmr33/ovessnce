<?php

/**
 * Description of ConsumerController
 *
 * 
 * @author adarshkumar
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Zend\Session\Container;
use Application\Model\FrontEndAuth;
use Application\Model\Practitioners;
use Application\Model\Consumers;
use Application\Model\Messages;
use Application\Model\Bookings;
use Application\Form\SPChangePasswordForm;
use Application\Form\SPcomposemessageFrom;
use Application\Form\SearchForm;
use Application\Model\Wishlists;
use Application\Model\Common;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\ImageS3;

class ConsumerController extends AbstractActionController
{

    public function indexAction()
    {

        return new ViewModel(array(
        ));
    }

    public function dashboardAction()
    {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        // 0:- both(sms & mail) are verified 1:- any one or both(sms & mail) are unverified
        $verifystatus = (($session->email_verification_status == 1) && ($session->sms_verification_status == 1)) ? 0 : 1;

        $api = new Api;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $loggedInUser = $session->userid;
        $userType = $session->user_type_id;


        $model_practitioner = new Practitioners;
        $model_consumer = new Consumers;
        $loggedInUserAddress = $model_practitioner->getLoggedInUserAddress($loggedInUser, $userType, $api_url);
        $common = new Common;
        $search_form = new SearchForm($common->getAllservices($api_url), $common->getstatesByCountry($api_url, $loggedInUserAddress->country_id));

        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);
        $practitioners_list = $model_practitioner->getSPlist($api_url);

        if (!$consumer_details) {
            $this->redirect()->toUrl('/consumer/list');
        }

        $bookings_count = $this->getConsumerbookingsCount($session->userid, $api, $api_url); //getting bookings count for Consumer
        $wishlist_count = $this->getConsumerWishlistCount($session->userid, $api, $api_url); //getting wishlist count for Consumer
        $contact_list_count = $model_consumer->getContactedListCount($session->userid, $api_url);
        $data = $this->getConsumerData($consumer_details);

        // getting banner for this page
        $banners = $common->getBanner($api_url, 5);
        
        // getting advertisments
        $ad = $common->getAdvertisement($api_url, 5);

        return new ViewModel(array(
            'consumer' => $data,
            //'states' => $common->getstatesByCountry($api_url, $data['address']['country_id']),
            'states' => $common->getstatesByCountry($api_url),
            'countries' => $common->getCountries($api_url),
            'languages' => $model_consumer->getLanguages($api_url),
            'booking_count' => $bookings_count,
            'wishlist_count' => $wishlist_count,
            'notifications' => $notifications,
            'practitioners_list' => $practitioners_list,
            'search_form' => $search_form,
            'last_login' => $session->last_login,
            'contact_list_count' => $contact_list_count,
            'verifystatus' => $verifystatus,
            'banners' => $banners,
            'addresses' => $common->getAddresses($api_url),
            'service_list' => $common->getServices($api_url),
            'advertisement' => $ad
        ));
    }

    public function viewAction()
    {

        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }

        $userid = (int) $this->params()->fromRoute('id', 0);

        if (!$userid) {
            return $this->redirect()->toUrl('/consumer/dashboard');
        }

        $api = new Api;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $session = new Container('frontend');
        $model_consumer = new Consumers;

        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);
        $data = $this->getConsumerData($consumer_details);

        return new ViewModel(array(
            'consumer' => $data
        ));
    }

    public function composeAction()
    {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $model = new Practitioners();
        $common = new Common;
        $contacted_list = $model->getCPlist($api_url);
        
        $form = new SPcomposemessageFrom($contacted_list);

        $model_practitioner = new Practitioners;
        $model_consumer = new Consumers;
        $model_common = $this->getServiceLocator()->get('Application\Model\Common');
        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $ids = $request->getPost('to');
            $subject = $request->getPost('subject');
            $message = $request->getPost('message');
            /* all these fields will be set to zero while inserting */
            $replyId = '0';
            $topLevel_id = '0';
            $readFlag = '0';
            $deleteFlag = '0';

            if (is_array($ids) && count($ids) > 0) {
                foreach ($ids as $id) {
                    $url = $api_url . "/api/messages/";
                    $data = array('subject' => $subject, 'message' => $message, 'to_user_id' => $id, 'from_user_id' => $session->userid, 'replyId' => $replyId,
                        'topLevel_id' => $topLevel_id, 'readFlag' => $readFlag, 'deleteFlag' => $deleteFlag, 'from_name' => $session->user_name);
                    $res = $api->curl($url, $data, "POST");

                    if ($res->getStatusCode() == 201) {

                        $result = json_decode($res->getBody(), true);
                        if ($result['id'] != '') {
                            /* update the message record 'reply id' and 'topL' after insert */
                            $last_insert_id = $result['id']; // get last insert id for user
                            $replyId = $last_insert_id;
                            $topLevel_id = $last_insert_id;

                            $url = $api_url . "/api/messages/" . $last_insert_id . "/";
                            $data = array('subject' => $subject, 'message' => $message, 'to_user_id' => $id, 'from_user_id' => $session->userid, 'replyId' => $replyId, 'topLevel_id' => $topLevel_id);

                            $res = $api->curl($url, $data, "PUT");

                            if ($res->getStatusCode() == 200) {

                                $practitioner_data = $model_practitioner->getSPDetails($api_url, $id);

                                if ($practitioner_data) {

                                    $subscriptionDetails = $model_common->getSubscriptiondetails($api_url, $practitioner_data['id'], true);
                                    $userFeatures = $model_common->getFeatures($api_url, $practitioner_data['id']);

                                    if (count($practitioner_data['contact']) > 0) {
                                        $contact_data = json_decode($practitioner_data['contact'][0], true);
                                    }

                                    $pattern = array('/{{reciever}}/i', '/{{sender}}/i');
                                    $replace = array('<strong>' . $practitioner_data['first_name'] . ' ' . $practitioner_data['last_name'] . '</strong>', '<strong>' . $session->first_name . ' ' . $session->last_name . '</strong>');

                                    if (isset($practitioner_data['email'])) {
                                        if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features']) && ($userFeatures['email'] == 1)) {
                                            $model_common->sendMail($api_url, $practitioner_data['email'], '', 16, '', $pattern, $replace, '');
                                        }
                                    }

                                    if (count($contact_data) > 0 && isset($contact_data['cellphone'])) {
                                        $replace = array($practitioner_data['first_name'] . ' ' . $practitioner_data['last_name'], $session->first_name . ' ' . $session->last_name);
                                        if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && ($userFeatures['sms'] == 1)) {
                                            $model_common->sendMsg($contact_data['cellphone'], 6, '', $pattern, $replace);
                                        }
                                    }
                                }

                                $msg = "Message sent sucessfully";
                            } else {
                                $error = true;
                                $msg = "Message sent with Errors";
                            }
                        } else {
                            $error = true;
                            $msg = "Message sent with errors";
                        }
                    } else {
                        $error = true;
                        $msg = "Error!! Message not sent";
                    }
                }
            } else {
                $error = true;
                $msg = "ERROR!! cannot send message";
            }

            echo json_encode(array('error' => $error, 'msg' => $msg));
            exit;
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 5);

        return new ViewModel(array(
            'form' => $form,
            'notifications' => $notifications,
            'avtar_url' => $consumer_details['avtar_url'],
            'first_name' => $consumer_details['first_name'],
            'last_name' => $consumer_details['last_name'],
            'banners' => $banners
        ));
    }

    public function inboxAction()
    {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }
        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        /* get details and notifiactions for consumer */
        $model_practitioner = new Practitioners;
        $model_consumer = new Consumers;
        $common = new Common;
        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $page = $request->getPost('page');

            $model = new Messages;
            $result = $model->getMessages($api_url, $session->userid, $session->user_type_id, $page, "inbox");

            echo json_encode($result);
            exit;
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 5);

        return new ViewModel(array(
            'notifications' => $notifications,
            'avtar_url' => $consumer_details['avtar_url'],
            'first_name' => $consumer_details['first_name'],
            'last_name' => $consumer_details['last_name'],
            'banners' => $banners
        ));
    }

    public function sentAction()
    {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }
        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        /* get details and notifiactions for consumer */
        $model_practitioner = new Practitioners;
        $model_consumer = new Consumers;
        $common = new Common;
        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $page = $request->getPost('page');

            $model = new Messages;

            $result = $model->getMessages($api_url, $session->userid, $session->user_type_id, $page, "sent");

            echo json_encode($result);
            exit;
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 5);

        return new ViewModel(array(
            'notifications' => $notifications,
            'avtar_url' => $consumer_details['avtar_url'],
            'first_name' => $consumer_details['first_name'],
            'last_name' => $consumer_details['last_name'],
            'banners' => $banners
        ));
    }

    public function trashAction()
    {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }
        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        /* get details and notifiactions for consumer */
        $model_practitioner = new Practitioners;
        $model_consumer = new Consumers;
        $common = new Common;
        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $page = $request->getPost('page');

            $model = new Messages;
            $result = $model->getMessages($api_url, $session->userid, $session->user_type_id, $page, "trash");

            echo json_encode($result);
            exit;
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 5);

        return new ViewModel(array(
            'notifications' => $notifications,
            'avtar_url' => $consumer_details['avtar_url'],
            'first_name' => $consumer_details['first_name'],
            'last_name' => $consumer_details['last_name'],
            'banners' => $banners
        ));
    }

    public function viewmessageAction()
    {
        $auth = new FrontEndAuth;

        $id = $this->params()->fromRoute('id');

        if ($id == '') {
            die('redirect to consumer inbox');
        }

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $url_master = $api_url . "/api/messages/" . $id . "/";
        $res_master = $api->curl($url_master, array(''), "GET");

        if ($res_master->getStatusCode() == 200) {
            $result_master = json_decode($res_master->getBody(), true);
            if ($result_master['readFlag_c'] == 0) {
                $res_master = $api->curl($url_master, array('readFlag_c' => 1), "PUT");
            }
        } else {
            $result_master = array();
        }

        if (isset($result_master['topLevel_id'])) {

            $result_replies = $this->fetchallreplies($api_url, $result_master);
        } else {
            $result_replies = array();
        }

        $model_practitioner = new Practitioners;
        $model_consumer = new Consumers;
        //$model_common = new Common;
        $model_common = $this->getServiceLocator()->get('Application/Model/Common');
        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        $request = $this->getRequest();
        if ($request->isPost()) {

            $reply_id = $request->getPost('replyId');
            $data = array('');
            $data['subject'] = $request->getPost("subject$reply_id");
            $data['message'] = $request->getPost("ReplyMessage$reply_id");
            $data['to_user_id'] = $request->getPost("toUserID$reply_id");
            $data['from_user_id'] = $session->userid;
            $data['replyId'] = $reply_id;
            $data['topLevel_id'] = $request->getPost("topLevel_id$reply_id");
            $data['from_name'] = $session->user_name;
            $data['readFlag'] = '0';
            $data['deleteFlag'] = '0';
            $data['created_date'] = date('Y-m-d H:i:s');

            if ($data['message'] != '') {
                // saving message in database;
                $url = $api_url . "/api/messages/";
                $res = $api->curl($url, $data, "POST");

                if ($res->getStatusCode() == 201) {
                    $replyMessage = "Reply Submitted Successfully";

                    //$practitioner_data = $model_practitioner->getSPDetails($api_url, $id);
                    $practitioner_data = $model_practitioner->getSPDetails($api_url, $data['to_user_id']);

                    if ($practitioner_data) {

                        $subscriptionDetails = $model_common->getSubscriptiondetails($api_url, $practitioner_data['id'], true);
                        $userFeatures = $model_common->getFeatures($api_url, $practitioner_data['id']);

                        if (count($practitioner_data['contact']) > 0) {
                            $contact_data = json_decode($practitioner_data['contact'][0], true);
                        }

                        $pattern = array('/{{reciever}}/i', '/{{sender}}/i');
                        $replace = array('<strong>' . $practitioner_data['first_name'] . ' ' . $practitioner_data['last_name'] . '</strong>', '<strong>' . $session->first_name . ' ' . $session->last_name . '</strong>');

                        if (isset($practitioner_data['email'])) {
                            if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features']) && ($userFeatures['email'] == 1)) {
                                $model_common->sendMail($api_url, $practitioner_data['email'], '', 16, '', $pattern, $replace, '');
                            }
                        }

                        if (count($contact_data) > 0 && isset($contact_data['cellphone'])) {
                            $replace = array($practitioner_data['first_name'] . ' ' . $practitioner_data['last_name'], $session->first_name . ' ' . $session->last_name);
                            if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && ($userFeatures['sms'] == 1)) {
                                $model_common->sendMsg($contact_data['cellphone'], 6, '', $pattern, $replace);
                            }
                        }
                    }

                    $this->fetchallreplies($api_url, $result_master);
                } else {
                    $replyMessage = "Error! Reply Could not be submitted";
                }
            } else {
                $msg_error = "Reply cannot be empty";
            }
        }

        // getting banner for this page
        $banners = $model_common->getBanner($api_url, 5);

        return new ViewModel(array(
            'master_message' => $result_master,
            'replies' => $result_replies,
            'current_user_id' => $session->userid,
            'notifications' => $notifications,
            'current_user_id' => $session->userid,
            'avtar_url' => $consumer_details['avtar_url'],
            'first_name' => $consumer_details['first_name'],
            'last_name' => $consumer_details['last_name'],
            'banners' => $banners
        ));
    }

    public function readmessageAction()
    {
        $auth = new FrontEndAuth;

        $id = $this->params()->fromRoute('id');

        if ($id == '') {
            die('redirect to consumer inbox');
        }

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $url_master = $api_url . "/api/messages/" . $id . "/";
        $res_master = $api->curl($url_master, array(''), "GET");

        if ($res_master->getStatusCode() == 200) {
            $result_master = json_decode($res_master->getBody(), true);
            if ($result_master['readFlag_c'] == 0) {
                $res_master = $api->curl($url_master, array('readFlag_c' => 1), "PUT");
            }
        } else {
            $result_master = array();
        }

        if (isset($result_master['topLevel_id'])) {

            $result_replies = $this->fetchallreplies($api_url, $result_master);
        } else {
            $result_replies = array();
        }

        $model_practitioner = new Practitioners;
        $model_consumer = new Consumers;
        $common = new Common;
        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');
        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        // getting banner for this page
        $banners = $common->getBanner($api_url, 5);

        return new ViewModel(array(
            'master_message' => $result_master,
            'replies' => $result_replies,
            'notifications' => $notifications,
            'avtar_url' => $consumer_details['avtar_url'],
            'first_name' => $consumer_details['first_name'],
            'last_name' => $consumer_details['last_name'],
            'current_user_id' => $session->userid,
            'banners' => $banners
        ));
    }

    public function fetchallreplies($api_url, $result_master)
    {
        $api = new Api();
        $url_replies = $api_url . "/api/messages/";
        $data = array('topLevel_id' => $result_master['topLevel_id']);

        $res_replies = $api->curl($url_replies, $data, "GET");

        if ($res_replies->getStatusCode() == 200) {
            $result_replies = json_decode($res_replies->getBody(), true);
        } else {
            $result_replies = array();
        }

        return $result_replies;
    }

    public function actionmsgsAction()
    {
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $error = false;
        $msg = '';

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $ids = $request->getPost('ids');
            $msg_action = $request->getPost('msg_action');
            $sucess_msg = '';
            $error_msg = '';

            if (count($ids) > 0 && $msg_action != '') {

                foreach ($ids as $id) {

                    $url = $api_url . "/api/messages/" . $id . "/";

                    if ($msg_action == "trash") {
                        $data = array('deleteFlag_c' => '1');
                        $sucess_msg = "Messages moved to trash successfully";
                        $error_msg = "Error!! could not moved to trash";
                    } else if ($msg_action == "untrash") {
                        $data = array('deleteFlag_c' => '0');
                        $sucess_msg = "Messages untrash successfully";
                        $error_msg = "Error!! could not untrash";
                    } else if ($msg_action == "markread") {
                        $data = array('readFlag_c' => '1');
                        $sucess_msg = "Message marked as read";
                        $error_msg = "Error!! cannot mark this message as read";
                    } else if ($msg_action == "markunread") {
                        $data = array('readFlag_c' => '0');
                        $sucess_msg = "Message marked as unread";
                        $error_msg = "Error!! cannot mark this message as unread";
                    } else if ($msg_action == "delete") {
                        $data = array('deleteFlag_c' => '2');
                        $sucess_msg = "Messages deleted successfully";
                        $error_msg = "Error!! could not delete";
                    } else {
                        $data = array();
                    }

                    $res = $api->curl($url, $data, "PUT");

                    if ($res->getStatusCode() == 200) {
                        $error = false;
                        $msg = $sucess_msg;
                    } else {
                        $error = true;
                        $msg = $error_msg;
                    }

                    /*
                      if ($msg_action == "delete") {

                      $res = $api->curl($url, $data, "DELETE");

                      if ($res->getStatusCode() == 204) {
                      $error = false;
                      $msg = "Message deleted permenantly";
                      } else {
                      $error = true;
                      $msg = "Error !! could not permanantly delete this message";
                      }
                      } else {

                      $res = $api->curl($url, $data, "PUT");

                      if ($res->getStatusCode() == 200) {
                      $error = false;
                      $msg = $sucess_msg;
                      } else {
                      $error = true;
                      $msg = $error_msg;
                      }
                      }
                     */
                }
            } else {
                $error = true;
                $msg = $error_msg;
            }

            //$model = new Practitioners;
            //$notifications = $model->getNotifications($api_url);

            echo json_encode(array('error' => $error, 'msg' => $msg, /* 'notifications' => $notifications */));
            exit;
        }

        exit;
    }

    public function settingsAction()
    {

        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');

        $api = new Api;
        $common = new Common;
        $booking_model = new Bookings;

        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $featureData = $common->getFeatures($api_url, $session->userid);

        $result_newsletter = $common->chkNewsletter($api_url);

        $unsubscribe_reasons = $common->getUnsubscribereason($api_url);

        $model_practitioner = new Practitioners;
        $model_consumer = new Consumers;

        $notifications = $model_practitioner->getNotifications($api_url, 'consumer');

        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        if (!$consumer_details) {
            $this->redirect()->toUrl('/consumer/index');
        }

        $form = new SPChangePasswordForm();
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $error = false;

            ($request->getPost('action') != '') ? $action = $request->getPost('action') : $action = '';

            if ($action == "change_password") {
                if (preg_match('/^.*(?=.{6,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).*$/', $request->getPost('Pass'))) {
                    ($request->getPost('old_pass') != '') ? $old_pass = $request->getPost('old_pass') : $old_pass = '';
                    ($request->getPost('Pass') != '') ? $Pass = $request->getPost('Pass') : $Pass = '';
                    ($request->getPost('confirm_password') != '') ? $confirm_password = $request->getPost('confirm_password') : $confirm_password = '';

                    if ($old_pass != '' && $Pass != '' && $confirm_password != '') {

                        if ($Pass == $confirm_password) {

                            $data = array('op' => 'changepassword', 'old_password' => md5($old_pass), 'new_password' => md5($Pass), 'user_id' => $session->userid);
                            $url = $api_url . "/api/useractivity/";

                            $res = $api->curl($url, $data, "POST");

                            if ($res->getStatusCode() == 200) {
                                $msg = "Success!! Password changed sucessfully";
                            } else if ($res->getStatusCode() == 404) {
                                $error = true;
                                $msg = "Error!! Old password is not correct";
                            } else {
                                $error = true;
                                $msg = "Error!! Password could not be updated";
                            }
                        } else {
                            // Password and confirm password do not match
                            $error = true;
                            $msg = "Error!! Confirm password do not match";
                        }
                    } else {
                        $error = true;
                        $msg = "Error!! Invalid form data";
                    }
                } else {
                    $error = true;
                    $msg = "Password must be at least 6 characters and must contain at least one lower case letter, one upper case letter, one digit and one special character.";
                }

                echo json_encode(array('error' => $error, 'msg' => $msg));
                exit;
            } else if ($action == "change_features") {

                ($request->getPost('feature_email') != '') ? $feature_email = $request->getPost('feature_email') : $feature_email = '';
                ($request->getPost('feature_sms') != '') ? $feature_sms = $request->getPost('feature_sms') : $feature_sms = '';
                ($request->getPost('feature_chat') != '') ? $feature_chat = $request->getPost('feature_chat') : $feature_chat = '';
                ($request->getPost('feature_table_id') != '') ? $feature_table_id = $request->getPost('feature_table_id') : $feature_table_id = '';

                $data = array('email' => $feature_email, 'sms' => $feature_sms, 'chat' => $feature_chat, 'user_id' => $session->userid);
                $data['id'] = $featureData['id'];
                
                if ($common->addFeature($api_url, $data)) {
                    $msg = "Feature setting updated successfully";
                } else {
                    $error = true;
                    $msg = "Feature setting updated successfully";
                }

                echo json_encode(array('error' => $error, 'msg' => $msg));
                exit;
            } else if ($action == "newletter-chk") {

                ($request->getPost('newletter_chk') != '') ? $newletter_chk = $request->getPost('newletter_chk') : $newletter_chk = '';
                $data = array('user_id' => $session->userid, 'newsletter' => $newletter_chk);

                if (isset($featureData['id'])) {
                    // update 
                    $url = $api_url . '/api/userfeaturesetting/' . $featureData['id'] . '/';
                    $res = $api->curlUpdate($url, $data, "PUT");
                } else {

                    $url = $api_url . '/api/userfeaturesetting/';
                    $res = $api->curlUpdate($url, $data, "POST");
                }

                if ($res) {
                    $msg = "Feature setting updated successfully";
                } else {
                    $error = true;
                    $msg = "Feature setting updated successfully";
                }

                echo json_encode(array('error' => $error, 'msg' => $msg));
                exit;
            } else if ($action == "close-acc") {

                $reason_id = ($request->getPost('reason_id') != '') ? $request->getPost('reason_id') : '';
                $other_reason = ($request->getPost('other_reason') != '') ? $request->getPost('other_reason') : '';

                if ($reason_id !== '') {

                    if ($reason_id == 5 && $other_reason == '') {
                        $error = true;
                        $msg = "Please provide other reason in the text area";
                    } else {
                        $result = $common->closeAccount($api_url, $reason_id, $other_reason);
                        if ($result) {
                            // close acc here
                            // remove all related bookings
                            $bookings_data = $booking_model->getBookings($api_url, "", $session->userid, '', '', '', '', '', '');

                            if (isset($bookings_data['booking_ids']) && !empty($bookings_data['booking_ids'])) {
                                $booking_ids = explode(',', $bookings_data['booking_ids']);
                                $twilloconf = $this->getServiceLocator()->get('config')['Twilio'];
                                if (count($booking_ids) > 0) {
                                    foreach ($booking_ids as $ids) {
                                        if (isset($ids['id']) && !empty($ids['id'])) {
                                            $booking_result = $booking_model->changeBookingStatus($api_url, $ids['id'], "6", $twilloconf);
                                        }
                                    }
                                }
                            }


                            $msg = "You account has been deactivated successfully..you will be logged out in 5 seconds";
                        } else {
                            // error acc could not be closed
                            $error = true;
                            $msg = "Some Error occured , Could not close your account , please try after some time";
                        }
                    }
                } else {
                    $error = true;
                    $msg = "Please provide us a reason to close your account";
                }

                echo json_encode(array('error' => $error, 'msg' => $msg));
                exit;
            } else {

                $error = true;
                $msg = "Invalid request";
                echo json_encode(array('error' => $error, 'msg' => $msg));
                exit;
            }
        }

        $consumer_details = $model_consumer->getConsumerdetails($api_url, $session->userid);

        $data = $this->getConsumerData($consumer_details);

        // getting banner for this page
        $banners = $common->getBanner($api_url, 5);

        return new ViewModel(array(
            'consumer' => $data,
            'form' => $form,
            'featureData' => $featureData,
            'notifications' => $notifications,
            'avtar_url' => $consumer_details['avtar_url'],
            'first_name' => $consumer_details['first_name'],
            'last_name' => $consumer_details['last_name'],
            'newsletter_chk' => $result_newsletter,
            'reasonsList' => $unsubscribe_reasons,
            'banners' => $banners
        ));
    }

    public function getConsumerData($res)
    {
        //$content = json_decode($res->getBody(), true);
        $content = $res;
        $address = array();
        $contact = array();
        $certification = array();
        $language = array();

        if (count($content['address']) > 0) {
            foreach ($content['address'] as $add) {
                $address[] = json_decode($add, true);
            }
        }

        if (count($content['contact']) > 0) {
            foreach ($content['contact'] as $cont) {
                $contact[] = json_decode($cont, true);
            }
        }

        if (count($content['certification']) > 0) {
            foreach ($content['certification'] as $edu) {
                $certification[] = json_decode($edu, true);
            }
        }

        if (count($content['language']) > 0) {
            foreach ($content['language'] as $data) {

                $langArr = json_decode($data, true);

                if (!empty($langArr)) {
                    $lang[] = $langArr['service_language'];
                }
            }
        }

        if (count($lang) > 0) {
            $language = implode(', ', $lang);
        } else {
            $language = '';
        }
        //echo "<pre>"; print_r($contact); die();
        return array(
            'id' => $content['id'],
            'first_name' => $content['first_name'],
            'last_name' => $content['last_name'],
            'user_name' => $content['user_name'],
            'email' => $content['email'],
            'avtar_url' => $content['avtar_url'],
            'age' => $content['age'],
            'gender' => $content['gender'],
            'user_type_id' => $content['user_type_id'],
            'address' => $address[0],
            'contact' => $contact[0],
            'language' => $language,
            'educations' => $certification,
        );
    }

    public function getConsumerbookingsCount($id, $api, $api_url)
    {

        $url = $api_url . "/api/booking/";
        $data = array('user_id' => $id);
        $res = $api->curl($url, $data, "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            (count($content['results']) > 0) ? $count = count($content['results']) : $count = '0';
        } else {
            $count = '0';
        }
        return $count;
    }

    public function getConsumerWishlistCount($id, $api, $api_url)
    {

        $url = $api_url . "/api/wishlist/";
        $data = array('created_by' => $id);
        $res = $api->curl($url, $data, "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            ($content['count'] > 0) ? $count = $content['count'] : $count = '0';
        } else {
            $count = '0';
        }
        return $count;
    }

    public function getwishlistAction()
    {
        $model = new Wishlists;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $page = $request->getPost('page');
            $user_id = $request->getPost('user_id');
            $recordsPerPage = $request->getPost('items');
            if ($page != '' && $user_id != '' && $recordsPerPage != '') {
                $data = array('page' => $page, 'no_of_records' => $recordsPerPage, 'created_by' => $user_id);
                $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
                $wishlists = $model->getwishlists($data, $api_url);
            }
            echo json_encode($wishlists['results']);
        }
        exit;
    }

    public function deleteWishlistAction()
    {
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $error = false;
        $msg = '';

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $ids = $request->getPost('id');

            if (is_array($ids) && count($ids) > 0) {
                foreach ($ids as $id) {
                    $url = $api_url . "/api/wishlist/" . $id . "/";
                    $res = $api->curl($url, array(''), "DELETE");
                    if ($res->getStatusCode() == 204) {
                        $error = false;
                        $msg = "Service deleted successfully";
                    } else {
                        $error = true;
                        $msg = "Error!! could not delete this service";
                    }
                }
            } else {
                $error = true;
                $msg = "ERROR!! cannot delete this service";
            }

            echo json_encode(array('error' => $error, 'msg' => $msg));
            exit;
        }
        exit;
    }

    public function sendInvitationAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();

            if ($request->getPost('user') != "" && $request->getPost('email') != "") {

                $model = new Wishlists;
                $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
                if ($template = $model->emailTemplate($api_url, 5)) {

                    $user_details = $model->getCustomerDetails($api_url, $request->getPost('user'));

                    $mail = new Message();
                    $transport = new \Zend\Mail\Transport\Sendmail();
                    $html = new MimePart(preg_replace('/{{user_name}}/i', '<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>', $template['content']));
                    $html->type = "text/html";

                    $body = new MimeMessage();
                    $body->setParts(array($html));

                    $mail->setBody($body)
                            ->setFrom($template['fromEmail'], 'Ovessence')
                            ->addTo($request->getPost('email'), '')
                            ->setSubject($template['subject']);
                    $transport->send($mail);
                    echo json_encode(array('status' => 1, 'msg' => 'Invitation sent to the email address..!!'));
                } else {
                    echo json_encode(array('status' => 0, 'msg' => 'Unable to find mail template..!!'));
                }
            } else {
                echo json_encode(array('status' => 0, 'msg' => 'Unable to send invitation..!!'));
            }
        }
        exit;
    }

    public function referspAction()
    {
        $session = new Container('frontend');
        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            if ($request->getPost('user') != "" && $request->getPost('email') != "") {
                $model = new Wishlists;
                $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

                if ($template = $model->emailTemplate($api_url, 7)) {

                    $user_details = $model->getCustomerDetails($api_url, $session->userid);

                    $profile_url = $this->getServiceLocator()->get('config')['basepath']['url'] . 'practitioner/view/' . $request->getPost('user');
                    $patterns = array('/{{user_name}}/i', '/{{profile_link}}/i');
                    $replacements = array('<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>', '<strong>' . $profile_url . '</strong>');
                    $mail = new Message();
                    $transport = new \Zend\Mail\Transport\Sendmail();
                    $html = new MimePart(preg_replace($patterns, $replacements, $template['content']));

                    $html->type = "text/html";

                    $body = new MimeMessage();
                    $body->setParts(array($html));

                    $mail->setBody($body)
                            ->setFrom($template['fromEmail'], 'Ovessence')
                            ->addTo($request->getPost('email'), '')
                            ->setSubject($template['subject']);
                    $transport->send($mail);

                    echo json_encode(array('status' => 1, 'msg' => 'Selected practitioner successfully referred to the given email address..!!'));
                } else {
                    echo json_encode(array('status' => 0, 'msg' => 'Unable to find mail template..!!'));
                }
            } else {
                echo json_encode(array('status' => 0, 'msg' => 'Unable to Refer..!!'));
            }
        }
        exit;
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $action = $request->getPost('action', 'profile');
            $api = new Api();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $session = new Container('frontend');
            $consumer = new Consumers;

            switch ($action) {

                case 'profile' :
                    $errors = array();

                    $user_id = $details_data['user_id'] = $request->getQuery('user_id');

                    $contact_data['first_name'] = (trim($request->getQuery('first_name')) != "") ? trim($request->getQuery('first_name')) : '';
                    $contact_data['last_name'] = (trim($request->getQuery('last_name')) != "") ? trim($request->getQuery('last_name')) : '';
                    $contact_data['age'] = (trim($request->getQuery('age')) != "") ? trim($request->getQuery('age')) : '';
                    $contact_data['gender'] = (trim($request->getQuery('gender')) != "") ? trim($request->getQuery('gender')) : '';

                    /* contact validation starts here */

                    foreach ($contact_data as $key => $value) {

                        if ($value == '' && $key != 'language') {

                            $errors[$key] = "This field is required.";
                        } elseif ($key == "age" && $value > 99) {

                            $errors[$key] = "Please provide valid age.";
                        }
                    }

                    /* contact validation starts here */

                    $languages = $request->getQuery('language');
                    $del_res = $api->curlUpdate($api_url . "/api/consumerrelateddata/", array('op' => "language", 'users_id' => $user_id), "DELETE");

                    if (is_array($languages) && count($languages) > 0) {

                        foreach ($languages as $language) {
                            $user_languages[] = array('user_id' => $user_id, 'language_id' => $language);
                        }
                        $result = $api->curl($api_url . "/api/consumerrelateddata/", array('language' => json_encode($user_languages, true)), "POST");
                    }

                    if (count($errors) <= 0) {
                        $user_res = $api->curl($api_url . "/api/users/" . $user_id . "/", $contact_data, "PUT");

                        if ($user_res->getStatusCode() == 200) {
                            echo json_encode(array('status' => 1, 'msg' => 'Profile successfully updated..!!'));
                        } else {
                            $errors = ($user_res->getStatusCode() != 200 && is_array(json_decode($user_res->getBody(), true))) ? array_merge($errors, json_decode($user_res->getBody(), true)) : $errors;
                            echo json_encode(array('status' => 0, 'errors' => $errors));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'errors' => $errors));
                    }

                    break;

                case 'contact':
                    $contact_data = array();
                    $errors = array();

                    $contact_data['home_phone'] = ($request->getQuery('home_phone') != "") ? trim($request->getQuery('home_phone')) : '';
                    $contact_data['cell_phone'] = ($request->getQuery('cell_phone') != "") ? trim($request->getQuery('cell_phone')) : '';
                    $contact_data['work_phone'] = ($request->getQuery('work_phone') != "") ? trim($request->getQuery('work_phone')) : '';
                    $contact_data['fax'] = ($request->getQuery('fax') != "") ? trim($request->getQuery('fax')) : '';
                    $contact_data['user_id'] = ($session->userid != "") ? $session->userid : '';

                    /* contact validation starts here */
                    foreach ($contact_data as $key => $value) {

                        /*if ($value != '' && !in_array($key, array('')) && !is_numeric($value)) {
                            $errors[$key] = "Must be a numberic value.";
                        } else*/
                        
                        if ($key == "home_phone" && $value == '') {
                            $errors[$key] = "Field is requried.";
                        } elseif ($key == "cell_phone" && $value == '') {
                            $errors[$key] = "Field is requried.";
                        } elseif ($key == "fax" && $value != '' && !is_numeric($value)) {
                            $errors[$key] = "Valid Fax No. must be a numberic value.";
                        } elseif (($key == "home_phone" && !preg_match('/^\d{11}$/', $value))) {
                        //} elseif (($key == "home_phone" && !preg_match('/^(\(\+[0-9]\))\s*([0-9]{3})-([0-9]{3})-([0-9]{4})$/', $value))) {
                            $errors[$key] = "Valid Phone number must be 11 digit number (1 as a prefix and 10 digit phone number).";
                        } elseif (($key == "work_phone" && $value != '' && !preg_match('/^\d{11}$/', $value))) {
                        //} elseif (($key == "home_phone" && !preg_match('/^(\(\+[0-9]\))\s*([0-9]{3})-([0-9]{3})-([0-9]{4})$/', $value))) {
                            $errors[$key] = "Valid Phone number must be 11 digit number (1 as a prefix and 10 digit phone number).";
                        } elseif (($key == "cell_phone" && !preg_match('/^\d{11}$/', $value))) {
                        //} elseif (($key == "cell_phone" && !preg_match('/^(\(\+[0-9]\))\s*([0-9]{3})-([0-9]{3})-([0-9]{4})$/', $value))) {
                            $errors[$key] = "Valid Phone number must be 11 digit number (1 as a prefix and 10 digit phone number).";
                        } elseif (($key == "fax" && $value != '' && !preg_match('/^\d{11}$/', $value))) {
                        //} elseif (($key == "home_phone" && !preg_match('/^(\(\+[0-9]\))\s*([0-9]{3})-([0-9]{3})-([0-9]{4})$/', $value))) {
                            $errors[$key] = "Valid Fax number must be 11 digit number (1 as a prefix and 10 digit phone number).";
                        }
                    }

                    /* contact validation ends here */

                    if (count($errors) <= 0) {
                        $contact_id = $request->getQuery('contact_id');

                        /* check if contact exists */
                        $contact_check = $api->curl($api_url . "/api/users/contact/" . $contact_id . "/", array(), "GET");

                        //$contact_data_check = json_decode($contact_check->getBody(), true);

                        if ($contact_check->getStatusCode() == 404 && $contact_id == "") {

                            $contact_res = $api->curl($api_url . "/api/users/contact/", $contact_data, "POST");

                            if ($contact_res->getStatusCode() == 201) {

                                echo json_encode(array('status' => 1, 'msg' => 'User contact successfully added..!!'));
                            } else {
                                $errors = ($contact_res->getStatusCode() != 200 && is_array(json_decode($contact_res->getBody(), true))) ? array_merge($errors, json_decode($contact_res->getBody(), true)) : $errors;
                                echo json_encode(array('status' => 0, 'errors' => $errors));
                            }
                        } elseif ($contact_check->getStatusCode() == 200 && $contact_id != "") {
                            $contact_check_res = json_decode($contact_check->getBody(), true);

                            if ($contact_id == $contact_check_res['id'] && $contact_check_res['user_id'] == $session->userid) {
                                $contact_res = $api->curl($api_url . "/api/users/contact/" . $contact_id . "/", $contact_data, "PUT");

                                if ($contact_res->getStatusCode() == 200) {

                                    echo json_encode(array('status' => 1, 'msg' => 'User contact successfully updated..!!'));
                                } else {
                                    $errors = ($contact_res->getStatusCode() != 200 && is_array(json_decode($contact_res->getBody(), true))) ? array_merge($errors, json_decode($contact_res->getBody(), true)) : $errors;
                                    echo json_encode(array('status' => 0, 'errors' => $errors));
                                }
                            }
                        } else {
                            $errors['Contact'] = "Unable to update contact please try again..!!.";
                            echo json_encode(array('status' => 0, 'errors' => $errors));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'errors' => $errors));
                    }
                    break;

                case 'address':
                    $address_data = array();
                    $errors = array();
                    $address_data['user_type'] = '';
                    $address_data['location_type_id'] = 1;
                    $address_data['user_id'] = $session->userid;

                    $address_data['street1_address'] = (trim($request->getQuery('street1_address')) != "") ? trim($request->getQuery('street1_address')) : '';
                    $address_data['city'] = (trim($request->getQuery('city')) != "") ? trim($request->getQuery('city')) : '';
                    $address_data['state_id'] = (trim($request->getQuery('state_id')) != "") ? trim($request->getQuery('state_id')) : '';
                    $address_data['country_id'] = (trim($request->getQuery('country_id')) != "") ? trim($request->getQuery('country_id')) : '';
                    $address_data['zip_code'] = (trim($request->getQuery('zip_code')) != "") ? trim($request->getQuery('zip_code')) : '';

                    /* contact validation starts here */
                    foreach ($address_data as $key => $value) {

                        if ($key == "zip_code" && $value == '') {

                            $errors[$key] = "Please enter a valid zip code.";
                        }
                    }

                    /* contact validation ends here */
                    if (count($errors) <= 0) {
                        $address_id = $request->getQuery('address_id');

                        /* check if contact exists */
                        $address_check = $api->curl($api_url . "/api/address/" . $address_id . "/", array(), "GET");
                        //$address_check = $api->curl($api_url . "/api/address/505/", array(), "GET");

                        if ($address_check->getStatusCode() == 404 && $address_id == "") {

                            $address_res = $api->curl($api_url . "/api/address/", $address_data, "POST");

                            if ($address_res->getStatusCode() == 200) {

                                echo json_encode(array('status' => 1, 'msg' => 'User contact successfully added..!!'));
                            } else {
                                $errors = ($address_res->getStatusCode() != 200 && is_array(json_decode($address_res->getBody(), true))) ? array_merge($errors, json_decode($address_res->getBody(), true)) : $errors;
                                echo json_encode(array('status' => 0, 'errors' => $errors));
                            }
                        } elseif ($address_check->getStatusCode() == 200 && $address_id != "") {

                            $address_res = $api->curl($api_url . "/api/address/" . $address_id . "/", $address_data, "PUT");

                            if ($address_res->getStatusCode() == 201) {
                                echo json_encode(array('status' => 1, 'msg' => 'User address successfully updated..!!'));
                            } else {
                                $errors = ($address_res->getStatusCode() != 201 && is_array(json_decode($address_res->getBody(), true))) ? array_merge($errors, json_decode($address_res->getBody(), true)) : $errors;
                                echo json_encode(array('status' => 0, 'errors' => $errors));
                            }
                        } else {

                            $errors['Address'] = "Unable to update address please try again..!!.";
                            echo json_encode(array('status' => 0, 'errors' => $errors));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'errors' => $errors));
                    }
                    break;

                case 'consumer-avatar' :
                    $File = $this->params()->fromFiles('consumer-avatar');
                    $valid_file_ext = array("image/jpg", "image/jpeg", "image/bmp", "image/gif", "image/png");
                    if ($File['error'] == 0 && $File['size'] > 0) {

                        if (in_array($File['type'], $valid_file_ext)) {
                            $session = new Container('frontend');
                            $user_id = $session->userid;
                            $api = new Api;
                            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
                            $res = $api->curl($api_url . "/api/users/" . $user_id . "/", array(), "GET");

                            if ($res->getStatusCode() == 200) {
                                $data = json_decode($res->getBody());

                                $old_avtar_url = $data->avtar_url;


                                // uploading consumer avtar
                                $S3 = new ImageS3;
                                $avatar_data = $S3->uploadFiles($_FILES['consumer-avatar'], '', array(), array('Avtars' => '378x378\>\!'));

                                //deleting existing avatar from Amazone
                                ($old_avtar_url != '') ? $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $old_avtar_url)) : '';

                                //updating avtar for user
                                $res = $api->curl($api_url . "/api/users/" . $user_id . "/", array('avtar_url' => $avatar_data['Avtars']), "PUT");

                                if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
                                    echo json_encode(array('status' => 1, 'msg' => 'Avtar image changed successfully..!!', 'image_url' => $avatar_data['Avtars']));
                                } else {
                                    echo json_encode(array('status' => 0, 'msg' => 'Failed to update avtar image..!!'));
                                }
                            } else {
                                echo json_encode(array('status' => 0, 'msg' => 'Failed to update avtar image..!!'));
                            }
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Please upload a valid image..!!'));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'No file selected for upload..!!'));
                    }

                    break;
                
                case 'delete_avtar' :
                    
                    $details = $consumer->getConsumerdetails($api_url, $session->userid);
                    
                    if (isset($details['avtar_url']) && $details['avtar_url'] != '') {
                        $S3 = new ImageS3;
                        $res = $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $details['avtar_url']));
                        
                        //updating avtar for user
                        $res = $api->curl($api_url . "/api/users/" . $session->userid . "/", array('avtar_url' => ''), "PUT");

                        if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
                            echo json_encode(array('status' => 1, 'msg' => 'Avtar image deleted successfully..!!', 'code' => $res));
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Failed to delete avtar image..!!'));
                        } 
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'No image found..!!'));
                    }
                    break;
               
            }
        }
        exit;
    }

    public function bookingsCalenderAction()
    {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(4)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        $id = $session->userid;

        if ($id != '') {

            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            $model = new Bookings;

            if ($content = $model->getBookings($api_url, "", $id)) {

                if (count($content['results']) > 0) {

                    foreach ($content['results'] as $booking) {
                        if ($booking['booking_status']['status_id'] != '6') {
                            switch ($booking['booking_status']['status_id']) {
                                case '4' :
                                    $color = '#96E34B';
                                    $title = 'Confirmed';
                                    break;

                                case '5' :
                                    $title = 'Pending Approval';
                                    $color = '#3399FF';
                                    break;

                                case '6' :
                                    $title = 'Canceled';
                                    $color = '#ED4E2A';
                                    break;

                                default :
                                    $title = 'Pending Approval';
                                    $color = '#FCB322';
                                    break;
                            }
                            $events[] = array('title' => '', 'start' => date('D F d Y H:i:s', strtotime($booking['booking_status']['booking_time'])), 'end' => date('D F d Y H:i:s', (strtotime($booking['booking_status']['booking_time']) + ($booking['duration'] * 60) + ($delay_time['delay_time'] * 60))), 'allDay' => false, 'backgroundColor' => $color, 'url' => '', 'id' => $booking['id'], 'user_id' => $booking['user_id'], 'duration' => $booking['duration']);
                        }
                    }
                    //print_r($events); exit;
                    echo json_encode($events);
                    exit;
                }
            }
        }
        exit;
    }

    public function ratingsAction()
    {

        $id = $this->params()->fromRoute('id');
        $getparams = $this->getRequest()->getQuery();

        if (!empty($id) && $id != null && $id != "") {

            $auth = new FrontEndAuth;

            if (!$auth->hasIdentity(4)) {
                return $this->redirect()->toUrl('/login');
            }

            $ratingType = array();
            $ratingData = array();
            $session = new Container('frontend');
            $model = new Practitioners();
            $bookingModel = new Bookings();
            $common = new Common;
            $api = new Api();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $reviewFlag = false;
            $user_id = $session->userid;

            if (isset($getparams['review']) && $getparams['review'] == 1 && isset($getparams['s_id'])) {
                // check service 
                $serivce_id = $getparams['s_id'];
                $sp_id = $id;

                $result = $bookingModel->getBookings($api_url, $sp_id, $user_id, '4', "", "", $serivce_id);
                $reviewFlag = $model->setreviewFlg($result);

                //get service provider details

                $sp_res = $api->curl($api_url . "/api/spusers/" . $sp_id . "/", array(), "GET");

                if ($sp_res->getStatusCode() == 200) {
                    $spData = json_decode($sp_res->getBody(), true);
                }
            }

            if ($reviewFlag == true) {

                //get previous ratings
                $ratings_data_res = $api->curl($api_url . "/api/rating/", array("users_id" => $id, "service_id" => $serivce_id, "created_by" => $user_id), "GET");

                if ($ratings_data_res->getStatusCode() == 200) {
                    $ratingData = json_decode($ratings_data_res->getBody(), true);
                }
                //Check if rating data exits otherwise send $ratingType for fresh reviews.
                if (empty($ratingData)) {
                    if (empty($ratingData)) {

                        $rating_res = $api->curl($api_url . "/api/ratingtype/", array("status_id" => 1), "GET");

                        if ($rating_res->getStatusCode() == 200) {

                            $ratingType = json_decode($rating_res->getBody(), true);
                        }
                    }
                }
            } else {
                return $this->redirect()->toUrl('/consumer/dashboard');
            }
        } else {
            return $this->redirect()->toUrl('/consumer/dashboard');
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 5);

        return new viewModel(array(
            "ratingType" => $ratingType,
            "ratingData" => $ratingData,
            "spData" => $spData,
            "service_id" => $serivce_id,
            'banners' => $banners
        ));
    }

    public function saveRatingsAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {
            $request = $this->getRequest();
            $params = $request->getQuery();
            $ratings = $_POST;
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $api = new Api();
            $session = new Container('frontend');
            $errors = array();
            $ratingType = array();
            $newRatings = array();
            $user_id = $session->userid;

            $rating_res = $api->curl($api_url . "/api/ratingtype/", array("status_id" => 1), "GET");

            if ($rating_res->getStatusCode() == 200) {

                $ratingType = json_decode($rating_res->getBody(), true);
            }

            if (!empty($ratingType) && is_array($ratingType) && count($ratingType) > 0) {
                foreach ($ratingType as $value) {
                    $key = str_replace(" ", "_", $value['rating_type']);
                    if (array_key_exists($key, $ratings)) {
                        $newRatings[] = array("users_id" => $params['serviceProvider'], "service_id" => $params['service_id'], "rating_type_id" => $value["id"], "rate" => $ratings[$key], "rating_type" => $value['rating_type'], "created_by" => $user_id);
                    } else {
                        $newRatings[] = array("users_id" => $params['serviceProvider'], "service_id" => $params['service_id'], "rating_type_id" => $value["id"], "rate" => 0, "rating_type" => $value['rating_type'], "created_by" => $user_id);
                    }
                }
            }
            //echo "<pre>"; echo json_encode(array("rating"=>$newRatings)); die();
            if (!empty($newRatings) && is_array($newRatings) && count($newRatings) > 0) {
                $data = json_encode($newRatings);
                $ratingsave_res = $api->curl($api_url . "/api/ratinginsert/", array("rating" => $data), "POST");

                if ($ratingsave_res->getStatusCode() == 201) {
                    echo json_encode(array('status' => 1, 'msg' => 'Your ratings for practitioner successfully saved..!!', 'data' => json_decode($ratingsave_res->getBody(), true)));
                } else {
                    $errors = ($ratingsave_res->getStatusCode() != 200 && is_array(json_decode($ratingsave_res->getBody(), true))) ? array_merge($errors, json_decode($ratingsave_res->getBody(), true)) : $errors;
                    echo json_encode(array('status' => 0, 'errors' => $errors));
                }
            } else {
                $errors['ratings'] = "Please try again. Unable to save ratings.";
                echo json_encode(array('status' => 0, 'errors' => $errors));
            }
        }
        exit;
    }

    public function getcontactlistAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $api = new Api();
            $page = $request->getPost('page');
            $user_id = $request->getPost('user_id');
            $recordsPerPage = $request->getPost('items');

            if ($page != '' && $user_id != '' && $recordsPerPage != '') {
                $data = array('page' => $page, 'no_of_records' => $recordsPerPage, 'from_user_id' => $user_id);
                //print_r($data); exit;
                $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
                $contact_res = $api->curl($api_url . "/api/messages/", $data, "GET");
                //echo '<pre>'; print_r($contact_res); exit;
                if ($contact_res->getStatusCode() == 200) {
                    $content = json_decode($contact_res->getBody(), true);
                    //echo '<pre>'; print_r($content['results']); exit;
                    echo json_encode($content['results']);
                    die;
                }
            }
        }
        exit;
    }

}
