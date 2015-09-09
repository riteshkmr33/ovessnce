<?php

/**
 *
 *
 * @author adarshkumar
 */

namespace Application\Model;

use Application\Model\Api;
use Application\Model\Bookings;
use Application\Model\Practitioners;
use Zend\Session\Container;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Common implements ServiceLocatorAwareInterface {

    private $api;
    private $serviceLocator;

    public function __construct() {
        $this->api = new Api();
    }

    public function setServiceLocator(ServiceLocatorInterface $sl) {
        $this->serviceLocator = $sl;
        return $this;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    /**
     * 	Function for generating string hash for newsletter subscription.
     * 	
     * 	Accept $id as parameter.
     *
     * 	Returns encoded hash of provided parameter.
     *
     */
    public function getEncode($id) {
        $hash_string = array("5cfaabafb8a481d", "93a4de0ac02eb66c8");
        if ($id != "") {
            $before_encode_hash = $hash_string[0] . "$id" . $hash_string[1];
            $encode_hash = base64_encode($before_encode_hash);
        }
        return $encode_hash;
    }

    /**
     * 	Function for converting string hash into newsletter subscription id.
     * 	
     * 	Accept $hash as parameter.
     *
     * 	Converts and return encoded hash into id.
     *
     */
    /* NWNmYWFiYWZiOGE0ODFkMjkzYTRkZTBhYzAyZWI2NmM4 */

    public function getDecode($hash) {
        $decode_string = array("/5cfaabafb8a481d/", "/93a4de0ac02eb66c8/");
        if ($hash != "") {
            $before_decode_hash = base64_decode($hash);
            $decode_hash = preg_replace($decode_string, "", $before_decode_hash);
        }
        return $decode_hash;
    }

    /* Function to get email template */

    public function emailTemplate($api_url, $id) {
        $res = $this->api->curl($api_url . '/api/emailtemplate/' . $id . '/', array(), "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            return $content;
        } else {
            return false;
        }
    }

    /* Function to get sms template */

    public function smsTemplate($api_url, $id) {
        $res = $this->api->curl($api_url . '/api/sms/' . $id . '/', array(), "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            return $content;
        } else {
            return false;
        }
    }

    /* Function to get all services */

    public function getAllservices($api_url) {

        $url = $api_url . "/api/servicecategory/";
        $data = array('');
        $res = $this->api->curl($url, $data, "GET");

        $list = array();

        if ($res->getStatusCode() == 200) {

            $content = json_decode($res->getBody(), true);
            if (count($content) > 0) {
                foreach ($content as $data) {
                    $list[$data['id']] = $data['category_name'];
                }
            }
        } else {
            $list = array();
        }

        return $list;
    }

    /* Function to get all states by country or without country */

    public function getstatesByCountry($api_url, $country_id = '') {
        $list = array();
        $url = $api_url . "/api/state/";
        $data = ($country_id != '') ? array('country_id' => $country_id, 'ordering' => 'state_name') : array();
        $res = $this->api->curl($url, $data, "GET");

        if ($res->getStatusCode() == 200) {

            $content = json_decode($res->getBody(), true);
            if (count($content) > 0) {
                foreach ($content as $data) {
                    $list[] = array('id' => $data['id'], 'value' => $data['state_name']);
                }
            }
        }

        return $list;
    }

    /* Function to get all countries */

    public function getCountries($api_url) {
        $res = $this->api->curl($api_url . "/api/country/", array(), "GET");

        $list = array();

        if ($res->getStatusCode() == 200) {

            $content = json_decode($res->getBody(), true);
            if (count($content) > 0) {
                foreach ($content as $data) {
                    $list[$data['id']] = $data['country_name'];
                }
            }
        } else {
            $list = array();
        }

        return $list;
    }

    /* Function to send in site message */

    public function sendMessage($api_url, $to_user_id, $from_user_id, $message_id = '', $from_name = '', $subject = '', $message = '', $replyId = '0', $topLevel_id = '0', $readFlag = '0', $deleteFlag = '0') {
        
        $data = array('subject' => $subject, 'message' => $message, 'to_user_id' => $to_user_id, 'from_user_id' => $from_user_id, 'replyId' => $replyId,
            'topLevel_id' => $topLevel_id, 'readFlag' => $readFlag, 'deleteFlag' => $deleteFlag, 'from_name' => $from_name);
        $response = ($message_id != "")?$this->api->curl($api_url . "/api/messages/" . $message_id . "/", $data, "PUT"):$this->api->curl($api_url . "/api/messages/", $data, "POST");
        //echo '<pre>'; print_r($response); exit;
        return $response;
    }

    /* Function to send mail */

    public function sendMail($api_url, $to, $from = '', $template = '', $content = array('subject' => '', 'message' => ''), $pattern = array(), $replace = array(), $attachment = false, $Bcc = '') {
        $mail = new Message();
        $body = new MimeMessage();
        $transport = new \Zend\Mail\Transport\Sendmail();

        if ($template != '') {
            if ($template = $this->emailTemplate($api_url, $template)) {
                $html = new MimePart(preg_replace($pattern, $replace, $template['content']));
                $subject = $template['subject'];
            }
        } else if ($content['message'] != '') {
            $html = new MimePart(preg_replace($pattern, $replace, $content['message']));
            $subject = $content['subject'];
        }

        if ($html) {
            $html->type = "text/html";
            if ($attachment != false) {
                $body->setParts(array($html, $attachment));
            } else {
                $body->setParts(array($html));
            }

            $from = ($from != '') ? $from : $template['fromEmail'];

            try {
                $mail->setBody($body)
                        ->setFrom($from, 'Ovessence')
                        //->addTo($user_email, '')
                        ->addTo($to, '')
                        ->setSubject(preg_replace($pattern, $replace, $subject));
                if ($Bcc != '') {
                    $mail->addBcc($Bcc, '');
                }
                $transport->send($mail);
                return true;
            } catch (\Exception $ex) {
                //echo $ex->getMessage(); exit;
                return false;
            }
        } else {
            return false;
        }
    }

    /* Function to send msg */

    public function sendMsg($to, $template = '', $msg = '', $pattern = array(), $replace = array()) {
        $config = $this->serviceLocator->get('config')['Twilio'];
        $client = new \Services_Twilio($config['sid'], $config['token']);

        if ($template != '') {
            $content = $this->smsTemplate($this->serviceLocator->get('config')['api_url']['value'], $template);
            $message = preg_replace($pattern, $replace, $content['message']);
        } else {
            $message = $msg;
        }

        try {
            $msg = $client->account->messages->sendMessage($config['fromNumber'], '+' . $to, $message, null, array("MessageStatus", "ErrorCode"));
            if ($msg->status == 'queued') {
                $session = new Container('frontend');
                $data = array('subject' => preg_replace($pattern, $replace, $content['subject']), 'message' => $message, 'to_user_id' => $session->userid, 'from_user_id' => $session->userid, 'status' => 1);
                $this->api->curl($this->serviceLocator->get('config')['api_url']['value'] . '/api/smshistory/', $data, "POST");
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /* function to check whether user logged in user has subscribed to site newsletter - 
     * return false in case - no subscription
     *  return details incase - subscription
     * */

    public function chkNewsletter($api_url) {
        $session = new Container('frontend');
        $chk = array();

        if (isset($session->email)) {

            $data = array('email' => $session->email);
            $url = $api_url . "/api/newslettersubscription/";
            $res = $this->api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {

                $result = json_decode($res->getBody(), true);

                if (count($result) > 0) {
                    foreach ($result as $data) {

                        isset($data['id']) ? $chk['id'] = $data['id'] : $chk['id'] = '';
                        isset($data['email']) ? $chk['email'] = $data['email'] : $chk['email'] = '';
                        isset($data['status_id']) ? $chk['status_id'] = $data['status_id'] : $chk['status_id'] = '';
                    }
                }
            }
        }

        return $chk;
    }

    /* function to insert / update newsletter */

    function updateNewsletter($api_url, $status_id, $table_id = '') {
        $url = $api_url . '/api/newslettersubscription/';
        $session = new Container('frontend');
        $error = '';
        $msg = '';

        if ($table_id != '') {
            // update
            $data = array('status_id' => $status_id);
            $url = $url . $table_id . "/";
            $res = $this->api->curl($url, $data, "PUT");
            if ($res->getStatusCode() == 200) {
                // success new record inserted
                $msg = "Newsletter subscription updated successfully";
            } else {
                // fail newsletter subs not updated
                $error = true;
                $msg = "Error !! cannot updated newsletter subscription";
            }
        } else {
            // insert
            $data = array('email' => $session->email, 'status_id' => $status_id);
            $res = $this->api->curl($url, $data, "POST");
            if ($res->getStatusCode() == 201) {
                // success new record inserted
                $msg = "Newsletter subscription updated successfully";
            } else {
                // fail newsletter subs not updated
                $error = true;
                $msg = "Error !! cannot updated newsletter subscription";
            }
        }

        return array('error' => $error, 'msg' => $msg);
    }

    /* this function is for updating auto renewal option of user subscription */

    public function autorenew($api_url, $subscription_id, $autorenewal) {
        $url = $api_url . '/api/usersubscription/' . $subscription_id . '/';
        $session = new Container('frontend');
        $error = '';
        $msg = '';

        $data = array('auto_renewal' => $autorenewal);
        $res = $this->api->curl($url, $data, "PUT");

        if ($res->getStatusCode() == 200) {
            $msg = "auto renewal updated successfully";
        } else {
            $error = true;
            $msg = "Error, could not update to auto renewal";
        }

        return array('error' => $error, 'msg' => $msg);
    }

    /* this function returs the reasons user must opt while deactivation his/her account */

    public function getUnsubscribereason($api_url) {
        $reasonList = array();

        $url = $api_url . '/api/account-deactivate-reason/';
        $res = $this->api->curl($url, array(''), "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            if (count($content) > 0) {
                $i = 0;
                foreach ($content as $data) {
                    $reasonList[$i]['id'] = $data['id'];
                    $reasonList[$i]['reason'] = $data['reason'];
                    $i++;
                }
            }
        }

        return $reasonList;
    }

    /* this funciton creates a entry in close-account-list return true or false as response */

    public function closeAccount($api_url, $reason_id, $comment) {

        $session = new Container('frontend');
        $model_practitioner = new Practitioners;
        $subscription_id = '';

        $url = $api_url . '/api/deactivated-account-list/';
        $data = array('user_id' => $session->userid, 'reason_id' => $reason_id, 'comment' => $comment);

        $res = $this->api->curl($url, $data, "POST");

        if ($res->getStatusCode() == 201) {

            if ($session->user_type_id == "3") {
                // practitioner
                $user_url = $api_url . '/api/spusers/' . $session->userid . '/';
            } else {
                // consumer
                $user_url = $api_url . '/api/users/' . $session->userid . '/';
            }

            $user_data = array('status_id' => '3');
            $res = $this->api->curl($user_url, $user_data, "PUT");

            if ($res->getStatusCode() == 200) {
                // unsubscribe the current subscription if any

                if ($session->user_type_id == "3") {

                    $result = $this->getSubscriptiondetails($api_url, $session->userid);

                    if (count($result) > 0) {
                        foreach ($result as $res) {
                            if (isset($res['id'])) {
                                // subscription active
                                $subscription_id = $res['id'];
                            }
                        }
                    }

                    if ($subscription_id != '') {
                        $unsubs_membership = $this->unsubscribeMembership($api_url, $subscription_id);
                        if ($unsubs_membership) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * This funciton is user to unsubscribe user subscription
     */

    public function unsubscribeMembership($api_url, $id) {
        if ($id != '') {

            $url = $api_url . '/api/usersubscription/' . $id . '/';
            $data = array('status_id' => '2');
            $res = $this->api->curl($url, $data, "PUT");

            if ($res->getStatusCode() == 200) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /* This function checks if current user account is removable or not i.e
     * If it has pending bookings must setttle those first
     */

    public function isaccountRemovable($api_url, $type = 'practitioner') {
        $session = new Container('frontend');

        if ($session->user_type_id === 3) {
            // practitioner - check for any active bookings

            $booking_model = new Bookings;

            $bookingArr = $booking_model->getBookings($api_url, $session->userid, "", "", "", "", "", "");

            $date1 = new \DateTime("now");

            if (isset($bookingArr['results']) && count($bookingArr['results']) > 0) {

                foreach ($bookingArr['results'] as $arr) {

                    $date2 = new \DateTime($arr['booked_date']);

                    if (($date1 < $date2) && $arr['status_id'] != "6") {
                        // future booking cannot deactivate account
                        return false;
                        exit;
                    } else {
                        return true;
                    }
                }
            } else {
                // no future booking at all , can deactivate his account
                return true;
                exit;
            }
        } else {
            return false;
        }
    }

    /* this function returns user features */

    public function getFeatures($api_url, $id) {
        if ($id != '') {

            $url = $api_url . "/api/userfeaturesetting/";
            $data = array('user_id' => $id);
            $res = $this->api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {

                $content = json_decode($res->getBody(), true);

                $feature_list = array(
                    'id' => $content[0]['id'],
                    'chat' => $content[0]['chat'],
                    'sms' => $content[0]['sms'],
                    'email' => $content[0]['email'],
                    'newsletter' => $content[0]['newsletter'],
                );
            } else {
                $feature_list = array('chat' => '', 'sms' => '', 'email' => '', 'newsletter' => '');
            }
        } else {

            $feature_list = array('chat' => '', 'sms' => '', 'email' => '', 'newsletter' => '');
        }

        return $feature_list;
    }

    /* Function to add user features */

    public function addFeature($api_url, $data) {
        if (isset($data['id'])) {
            $res = $this->api->curl($api_url . "/api/userfeaturesetting/" . $data['id'] . "/", $data, "PUT");
        } else {
            $res = $this->api->curl($api_url . "/api/userfeaturesetting/", $data, "POST");
        }

        return ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) ? true : false;
    }

    /* function to get subscription features */

    public function subscriptionFeatures($api_url, $subscription_id) {
        if ($subscription_id != '') {

            $url = $api_url . "/api/subscription/" . $subscription_id . '/';
            $res = $this->api->curl($url, array(''), "GET");

            if ($res->getStatusCode() == 200) {

                $content = json_decode($res->getBody(), true);
                if (count($content) > 0) {
                    foreach ($content as $data) {
                        echo "<pre>";
                        print_r($data);
                        die;
                    }
                }
            }
        }
    }

    /*
     * get subscription details of current logged in user
     * */

    public function getSubscriptiondetails($api_url, $user_id = '', $withFeatures = false) {
        $session = new Container('frontend');
        $user_id = !empty($user_id) ? $user_id : $session->userid;
        if ($user_id != '') {

            $url = $api_url . "/api/usersubscription/";
            $data = array('user_id' => $user_id, 'status_id' => '1');
            $res = $this->api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {

                $content = json_decode($res->getBody(), true);

                if (count($content) > 0) {
                    if ($withFeatures == true) {
                        $subs_details = json_decode($content[0]['subscription_duration']);
                        $feat_res = $this->api->curl($api_url . '/api/subscription/' . $subs_details->subscription_id . '/', array(), "GET");

                        if ($feat_res->getStatusCode() == 200) {
                            $feat_content = json_decode($feat_res->getBody(), true);

                            foreach ($feat_content['site_feature'] as $feature) {
                                $features[] = json_decode($feature, true)['id'];
                            }
                            $video_limit = isset($feat_content['subscription_video_limit'][0]) ? json_decode($feat_content['subscription_video_limit'][0], true) : array('limit' => 1);
                            return array('details' => $content, 'features' => $features, 'video_limit' => $video_limit);
                        } else {
                            return array('details' => $content, 'features' => array(), 'video_limit' => array('limit' => 1));
                        }
                    } else {
                        return $content;
                    }
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    // Get newsletter reciever details
    function newsletterReciever($api_url) {
        $url = $api_url . "/api/newsletter/send/";
        $data = array('status' => 0);
        $res = $this->api->curl($url, $data, "GET");
        $details = array();

        if ($res->getStatusCode() == 200) {
            $i = 0;
            $content = json_decode($res->getBody(), true);
            if ($content['count'] > 0) {
                foreach ($content['results'] as $recievers) {
                    //echo '<pre>'; print_r($recievers); exit;
                    $details[$i]['newsletter_id'] = $recievers['newsletter_id'];
                    $details[$i]['id'] = $recievers['id'];
                    $details[$i]['newsletter'] = json_decode($recievers['newsletter'], true);

                    if (count($recievers['user']) > 0) {
                        $user = json_decode($recievers['user'], true);
                        $details[$i]['user_id'] = $user['user_id'];
                        $details[$i]['email'] = $user['email'];
                        $details[$i]['name'] = $user['first_name'] . ' ' . $user['last_name'];
                    }
                    $i++;
                }
            }
        }
        return $details;
    }

    // Get admin newsletter details
    function adminNewsletters($api_url, $data = array()) {
        $url = $api_url . "/api/newsletter/";

        $res = $this->api->curl($url, $data, "GET");
        $details = array();

        if ($res->getStatusCode() == 200) {
            $i = 0;
            $content = json_decode($res->getBody(), true);
            if ($content['count'] > 0) {
                foreach ($content['results'] as $recievers) {
                    $details[$i]['id'] = $recievers['id'];
                    $details[$i]['subject'] = $recievers['subject'];
                    $details[$i]['message'] = $recievers['message'];
                    $details[$i]['send_date'] = $recievers['send_date'];
                    $i++;
                }
            }
        }
        return $details;
    }

    // Get newsletter subscription 
    function newsletterSubscription($api_url, $data = array()) {

        $url = $api_url . "/api/newslettersubscription/";
        $res = $this->api->curl($url, $data, "GET");
        $details = array();

        if ($res->getStatusCode() == 200) {
            $i = 0;
            $content = json_decode($res->getBody(), true);
            if (count($content) > 0) {
                foreach ($content as $recievers) {
                    $details[$i]['id'] = $recievers['id'];
                    $details[$i]['email'] = $recievers['email'];
                    $i++;
                }
            }
        }
        return $details;
    }

    // Get user subscription details
    function subscriptionData($api_url, $data = array()) {

        $url = $api_url . "/api/usersubscription/";
        $res = $this->api->curl($url, $data, "GET");
        $details = array();

        if ($res->getStatusCode() == 200) {
            $i = 0;
            $content = json_decode($res->getBody(), true);
            if (count($content) > 0) {
                foreach ($content as $recievers) {
                    $details[$i]['id'] = $recievers['id'];
                    $details[$i]['auto_renewal'] = $recievers['auto_renewal'];
                    $details[$i]['user_id'] = $recievers['user_id'];
                    $details[$i]['subscription_duration_id'] = $recievers['subscription_duration_id'];
                    $details[$i]['invoice_id'] = $recievers['invoice_id'];
                    $details[$i]['status_id'] = $recievers['status_id'];
                    $details[$i]['subscription_start_date'] = $recievers['subscription_start_date'];
                    $details[$i]['subscription_end_date'] = $recievers['subscription_end_date'];
                    if (count($recievers['subscription_duration']) > 0) {
                        $subsc = json_decode($recievers['subscription_duration'], true);
                        $details[$i]['subscription_name'] = $subsc['subscription_name'];
                        $details[$i]['duration'] = $subsc['duration'];
                        $details[$i]['duration_in'] = $subsc['duration_in'];
                        $details[$i]['price'] = $subsc['price'];
                        $details[$i]['currency'] = $subsc['currency'];
                        $details[$i]['subscription_id'] = $subsc['subscription_id'];
                    }

                    $i++;
                }
            }
        }
        return $details;
    }

    // Get user card details
    function getUserCardDetails($api_url, $data = array()) {

        $url = $api_url . "/api/card_details/";
        $res = $this->api->curl($url, $data, "GET");
        $details = array();

        if ($res->getStatusCode() == 200) {
            $i = 0;
            $content = json_decode($res->getBody(), true);

            if (count($content['results']) > 0) {
                foreach ($content['results'] as $recievers) {
                    $details[$i]['id'] = $recievers['id'];
                    $details[$i]['user_id'] = $recievers['user_id'];
                    $details[$i]['use_for_renew'] = $recievers['use_for_renew'];
                    $details[$i]['creditCardDetails_token'] = $recievers['creditCardDetails_token'];
                    $details[$i]['customerDetails_id'] = $recievers['customerDetails_id'];
                    if (count($recievers['subscription_duration']) > 0) {
                        $subsc = json_decode($recievers['subscription_duration'], true);
                        $details[$i]['subscription_name'] = $subsc['subscription_name'];
                    }

                    $i++;
                }
            }
        }
        return $details;
    }

    /* Fucntion to get address details */

    public function address($api_url, $id) {
        $res = $this->api->curl($api_url . "/api/address/" . $id . "/", array(), "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            $country = json_decode($content['country'], true);
            $state = json_decode($content['state'], true);
            $content['country_name'] = $country['country_name'];
            $content['state_name'] = $state['state_name'];
            return $content;
        } else {
            return false;
        }
        exit;
    }

    /* Function to get Banners */

    public function getBanner($api_url, $page_id) {
        $banners = array();
        $res = $this->api->curl($api_url . "/api/banners/", array('page_location_id' => $page_id, 'status_id' => 1), "GET");

        if ($res->getStatusCode() == '200') {
            $content = json_decode($res->getBody(), true);
            $banners = isset($content['results']) ? $content['results'] : $banners;
        }
        return $banners;
    }

    /* Function to add subscription  */

    public function addSubscription($api_url, $data = array()) {
        $data['subscription_duration_id'] = 1;
        $data['subscription_start_date'] = date('Y-m-d');
        $data['subscription_end_date'] = null;
        $data['invoice_id'] = null;
        $data['status_id'] = 1;
        $res = isset($data['id']) ? $this->api->curl($api_url . "/api/usersubscription/" . $data['id'] . "/", $data, "PUT") : $this->api->curl($api_url . "/api/usersubscription/", $data, "POST");
        //echo '<pre>'; print_r($res); exit;
        if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
            return true;
        } else {
            return false;
        }
    }

    /* Function to get all addresses */

    public function getAddresses($api_url) {
        $addresses = array();
        $res = $this->api->curl($api_url . "/api/address/", array(), "GET");

        if ($res->getStatusCode() == 200) {
            $addresses = json_decode($res->getBody(), true);
        }

        return $addresses;
    }

    /* Function to get all services */

    public function getServices($api_url) {
        $service_list = array();
        $res = $this->api->curl($api_url . "/api/servicecategory/", array(), "GET");

        if ($res->getStatusCode() == 200) {
            $service_list = json_decode($res->getBody(), true);
            //echo '<pre>'; print_r($service_list); exit;
        }

        return $service_list;
    }

    /* Function to get faqs */

    public function getFaqs($api_url, $user_type = 3, $id = '', $resultType = 'all') {
        $faqs = $filter = array();
        $filter['user_type'] = $user_type;
        ($id != '') ? $filter['id'] = $id : '';
        $res = $this->api->curl($api_url . "/api/faqs/", $filter, "GET");
        //echo '<pre>'; print_r($res); exit;
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);

            if ($resultType == 'all') {
                if (is_array($content) && count($content) > 0) {
                    foreach ($content as $faq) {
                        $index = json_decode($faq['index'], true);
                        //echo '<pre>'; print_r($index); exit;

                        if (array_key_exists($index['id'], $faqs) == true) {
                            $faqs[$index['id']]['faqs'][] = $faq;
                        } else {
                            $faqs[$index['id']]['title'] = $index['index_name'];
                            $faqs[$index['id']]['faqs'][] = $faq;
                        }
                    }
                }
            } else {
                $faqs = $content[0];
            }
        }

        return $faqs;
    }

    /* Function to get page content */

    public function getPage($api_url, $id) {
        $res = $this->api->curl($api_url . "/api/pages/" . $id . "/", array(), "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);

            return $content['content'];
        } else {
            return '';
        }
    }

    /* Function to get media */

    public function getMedia($api_url, $media_type = 2, $page = 1, $status_id = 9) {
        $filter = array('status_id' => $status_id, 'media_type' => $media_type, 'page' => $page);
        $res = $this->api->curl($api_url . "/api/media/", $filter, "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);

            return $content['results'];
        } else {
            return '';
        }
    }

    /* Function to get Advertisement */

    public function getAdvertisement($api_url, $page_id) {
        $advertisements = array(array('image' => 'https://s3-us-west-2.amazonaws.com/ovessence/img/your-add-here.jpg', 'url' => '//ovessence.com/contact'));

        $filter = array('page_id' => $page_id);
        $res = $this->api->curl($api_url . "/api/advertisements/", $filter, "GET");

        if ($res->getStatusCode() == 200) {
            $ads = json_decode($res->getBody(), true);

            if (is_array($ads) && count($ads) > 0) {
                foreach ($ads as $ad) {
                    $advertisements[] = array('image' => $ad['banner_content'], 'url' => $ad['target_url']);
                }

                return $advertisements;
            } else {
                return $advertisements;
            }
        } else {
            return $advertisements;
        }
    }

    /* Function to create account for live chat */

    public function addChatAccount($url, $content, $data, $request) {
        $liveData = array();
        $liveData['ls_id'] = $content['id'];
        $liveData['ls_name'] = $data['first_name'] . " " . $data['last_name'];
        $liveData['ls_email'] = $data['email'];
        $liveData['ls_username'] = $data['user_name'];
        $liveData['ls_access'] = 1;
        $liveData['ls_password'] = $data['Pass'];
        $liveData['ls_confirm_password'] = $request->getPost('confirm_password');
        $liveData['ls_responses'] = 1;
        $liveData['jak_timefrom'] = '00';
        $liveData['jak_timefromm'] = '00';
        $liveData['jak_timeto'] = '00';
        $liveData['jak_timetom'] = '00';
        $liveData['ls_phone'] = null;
        $liveData['ls_dnotify'] = 1;
        $liveData['ls_files'] = 1;
        $liveData['ls_chat'] = 1;
        $liveData['ls_chatlist'] = 0;
        $liveData['ls_sound'] = 1;
        $liveData['ls_ringing'] = 3;
        $liveData['ls_emailnot'] = 0;
        $liveData['ls_lang'] = null;
        $liveData['ls_inv'] = null;
        $liveData['save'] = '';

        $liveres = $this->api->curl($url . 'operator/index.php?p=users&sp=new&from=register', $liveData, "POST");
        //echo '<pre>'; print_r($liveres); exit;
    }

    // function to get browser details

    function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

}

?>