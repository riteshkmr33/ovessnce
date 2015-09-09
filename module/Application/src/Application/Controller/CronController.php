<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Practitioners;
use Application\Model\Common;
use Application\Model\Bookings;
use Application\Model\Consumers;
use Application\Model\Transactions;
use Application\Model\Api;
use Admin\Model\UsersTable;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class CronController extends AbstractActionController
{

    function bookingAction()
    {	
        $common = $this->getServiceLocator()->get('Application\Model\Common');
        $bookingModel = new Bookings;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        // getting all bookings prior to 48 and 24 hours
        $bookings48hrs = $bookingModel->getBookings($api_url, '', '', 4, '', '', '', '', '', date('Y-m-d', strtotime('+2 days')));  // date('Y-m-d', strtotime('+2 days'))
        $bookings24hrs = $bookingModel->getBookings($api_url, '', '', 4, '', '', '', '', '', date('Y-m-d', strtotime('+1 days')));  // date('Y-m-d', strtotime('+1 days'))
        $bookings = array_merge($bookings48hrs['results'], $bookings24hrs['results']);
       
        foreach ($bookings as $booking) {
	
            if (is_numeric($booking['booking_status']['user_id'])) {
				
                $mailPattern = array('/{{user_name}}/i', '/{{user_type}}/i', '/{{service_name}}/i', '/{{date_time}}/i','/{{name}}/i');
                $mailReplace = array($booking['consumer_first_name'] . " " . $booking['consumer_last_name'], 'Practitioner', $booking['category_name'], date('l d/m/Y h:i A', strtotime($booking['booking_status']['booking_time'])),$booking['sp_first_name'] . " " . $booking['sp_last_name']);
                $common->sendMail($api_url, $booking['consumer_email'], '', 17, '', $mailPattern, $mailReplace);
                
                $subscriptionDetails = $common->getSubscriptiondetails($api_url, $booking['service_provider_id'], true);
                $practitionerFeatures = $common->getFeatures($api_url, $booking['service_provider_id']);
                $userFeatures = $common->getFeatures($api_url, $booking['user_id']);
                
                if(is_numeric($booking['consumer_number']) && isset($userFeatures['sms']) && $userFeatures['sms'] == 1) {
                    $common->sendMsg($booking['consumer_number'], 7, '', $mailPattern, $mailReplace);		
		}
				
                $mailReplace = array($booking['consumer_first_name'] . " " . $booking['consumer_last_name'], 'Consumer', $booking['category_name'], date('l d/m/Y h:i A', strtotime($booking['booking_status']['booking_time'])),$booking['sp_first_name'] . " " . $booking['sp_last_name']);
                $common->sendMail($api_url, $booking['sp_email'], '', 17, '', $mailPattern, $mailReplace);
                
                if (((isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features'])) || ($practitionerFeatures['sms'] == 1)) && is_numeric($booking['sp_number'])) {
		    $common->sendMsg($booking['sp_number'], 7, '', $mailPattern, $mailReplace);
		}
            }
        }
	
        $bookings48hrs = $bookingModel->getBookings($api_url, '', '', 5, '', '', '', '', '', date('Y-m-d', strtotime('+2 days')));
        $bookings24hrs = $bookingModel->getBookings($api_url, '', '', 5, '', '', '', '', '', date('Y-m-d', strtotime('+1 days')));
        $bookings = array_merge($bookings48hrs['results'], $bookings24hrs['results']);

        foreach ($bookings as $booking) {
            // Cancel all pending bookings prior 48 hours
            $bookingModel->changeBookingStatus($api_url, $booking['id'], 6, $common);
        }

        die;
    }

    // Send Practitioner newsletter
    function practitionerNewsletterAction()
    {
        set_time_limit(0);
        $api = new Api();
        $model = new Practitioners;
        $common = $this->getServiceLocator()->get('Application\Model\Common');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $result = $common->newsletterReciever($api_url);
       
        if (count($result) > 0) {
            //Send mail
            foreach ($result as $details) {
                $patterns = array('/{{reciever}}/i');
                $replace = array($details['name']);
                $user_details = $model->getSPDetails($api_url, $details['newsletter']['created_by']);

                // getting use email permissions
                $subscriptionDetails = $common->getSubscriptiondetails($api_url, $details['user_id'], true);  // getting service provider subscription details
                $userFeatures = $common->getFeatures($api_url, $details['user_id']);
				
                if (((isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features'])) || ($userFeatures['email'] == 1)) && ($userFeatures['newsletter'] == 2 || $userFeatures['newsletter'] == 4)) {
                    $common->sendMail($api_url, $details['email'], $user_details['email'], '', array('subject' => $details['newsletter']['subject'], 'message' => $details['newsletter']['message']), $patterns, $replace);
                    $api->curl($api_url . '/api/newsletter/send/' . $details['id'] . '/', array('status' => '1', 'sent_date' => date("Y-m-d H:i:s")), "PUT");
                    sleep(180);
                }
            }
        }
        die;
    }

    // Send admin newsletter
    public function adminNewsletterAction()
    {
        //set_time_limit(0);
        $api = new Api();
        $common = $this->getServiceLocator()->get('Application\Model\Common');
        $users = $this->getServiceLocator()->get('Admin\Model\UsersTable');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $data = array('status_id' => '1', 'created_by' => '1');
        $result = $common->adminNewsletters($api_url, $data);
        
        $consumer = new Consumers();
        // Start :- Get admin email id
        $res = $consumer->getConsumerdetails($api_url, 1);
        
        if (count($res) > 0) {
            $adminEmailId = $res['email'];
        }
        
        if (count($result) > 0) {
            foreach ($result as $details) {

                if ($details['created_by'] == "1") { // newsletter only created by admin
                    //if ((strtotime($details['send_date']) < strtotime('+1 days')) || $details['send_date'] == null) {
                    if ((strtotime($details['send_date']) === strtotime('today')) || $details['send_date'] == null) {

                        /* new code to get list of recievers for newsletter with usertype condition : starts here pi28jan */
                        if ($details['user_type_id'] == "1") {
                            // send to all users available + subscribers 						
                            $res = $users->fetchAll(false, array());
                            $newsletter_subs = $common->newsletterSubscription($api_url, array('status_id' => '1', 'send_status' => '0'));
                        } else if ($details['user_type_id'] == "3") {
                            // send to all service providers						
                            $res = $users->fetchAll(false, array('user_type' => '3'));
                        } else if ($details['user_type_id'] == "4") {
                            // send to all consumers						
                            $res = $users->fetchAll(false, array('user_type' => '4'));
                        } else if ($details['user_type_id'] == "8") {
                            // send to all newsletter subscribers						
                            $res = $users->fetchAll(false, array('user_type' => '8'));
                            $newsletter_subs = $common->newsletterSubscription($api_url, array('status_id' => '1', 'send_status' => '0'));
                        } else {
                            $res = '';
                        }
                        $recievers_list = array();

                        if (isset($res)) {
                            foreach ($res as $list) {
                                $userFeatures = $common->getFeatures($api_url, $list->id);
                                if ($userFeatures['newsletter'] == 1 || $userFeatures['newsletter'] == 4) {
                                    $recievers_list[] = $list->email;
                                }
                            }
                        }

                        if (isset($newsletter_subs) && count($newsletter_subs)) {
                            foreach ($newsletter_subs as $ns) {
                                array_push($recievers_list, $ns['email']);
                                $api->curl($api_url . '/api/newslettersubscription/' . $ns['id'] . '/', array('send_status' => '1'), "PUT");
                            }
                            unset($newsletter_subs);
                        }

                        /* code : ends here */
                        if (count($recievers_list) > 0) {
                            //Send mail
                            foreach ($recievers_list as $email) {
                                echo $email;
                                $common->sendMail($api_url, $email, $adminEmailId, '', array('subject' => $details['subject'], 'message' => $details['message']));
                                //sleep(180);
                            }
                        }
                    }
                }
            }
        }
        exit;
    }

    // Send Subscription renew remainder
    public function membershipAction()
    {
        $common = $this->getServiceLocator()->get('Application\Model\Common');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $data = array('status_id' => 1, 'subscription_end_date' => date('Y-m-d', strtotime('+2 days'))); // 2days before subscription end
        $results = $common->subscriptionData($api_url, $data);
        if (count($results) > 0) {
            foreach ($results as $detail) {
                // Start :- Auto renew subscription
                if ($detail['auto_renewal'] == '1') {
                    $this->autorenewcodeAction($api_url,$common,$detail);
                    // End :- Auto renew subscription
                } 
            }
        }
        die;
    }
    
    public function membershiptodayAction()
    {
        $common = $this->getServiceLocator()->get('Application\Model\Common');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        
        $data = array('status_id' => 1, 'subscription_end_date' => date('Y-m-d')); 
        $results = $common->subscriptionData($api_url, $data);
        if (count($results) > 0) {
            foreach ($results as $detail) {
                // Start :- Auto renew subscription
                if ($detail['auto_renewal'] == '1') {
                    $this->autorenewcodeAction($api_url,$common,$detail);
                    // End :- Auto renew subscription
                } 
            }
        }
        die;
    }
    
    public function membershipexpireAction()
    {
        $common = $this->getServiceLocator()->get('Application\Model\Common');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $data = array('status_id' => 1, 'subscription_end_date' => date('Y-m-d', strtotime('-2 days'))); // 2days after subscription end
        $results = $common->subscriptionData($api_url, $data);
        if (count($results) > 0) {
            foreach ($results as $detail) {
				$ongoing_subscription = $common->getSubscriptiondetails($api_url,$detail['user_id']);
				$common->addSubscription($api_url, array('user_id' => $detail['user_id'], 'id' => $ongoing_subscription[0]['id']));                
            }
        }
        die;
    }
    
    public function autorenewcodeAction($api_url,$common,$detail)
    {
        $data = array('user_id' => $detail['user_id'], 'use_for_renew' => 1);
        $getCardDetails = $common->getUserCardDetails($api_url, $data);
        $card = end($getCardDetails);
        // Start:-- Get card details
        if ($card['creditCardDetails_token'] != '' && $card['customerDetails_id'] != '') {
      
            $bookingModel = new Bookings();
            $trans = new Transactions();
            $model = new Practitioners();
            $transaction['amount'] = $detail['price']; //$details['price'];
            $transaction['creditCardDetails_token'] = $card['creditCardDetails_token']; //$getCardDetails['creditCardDetails_token'];
            $transaction['customerDetails_id'] = $card['customerDetails_id']; //$getCardDetails['customerDetails_id'];
            $result = $trans->processPayment($this->getServiceLocator()->get('Config'), $transaction, 1);
            $user_details = $model->getSPDetails($api_url, $detail['user_id']);

            // getting use email permissions
            $subscriptionDetails = $common->getSubscriptiondetails($api_url, $detail['user_id'], true);  // getting service provider subscription details
            $userFeatures = $common->getFeatures($api_url, $detail['user_id']);

            // If transaction done
            if ($result['status'] == 1) {

                $sendMail = 0; // Not send mail
                // Start :- Get subscription end date
                if (isset($detail['duration_in'])) {
                    if ($detail['duration_in'] == "1") {
                        // move subs date $selected['duration'] year ahed
                        $new_expireDate = date('Y-m-d', strtotime("+" . $detail['duration'] . " year", strtotime($detail['subscription_end_date'])));
                    } else if ($detail['duration_in'] == "2") {
                        // move subs date $selected['duration'] month ahed
                        $new_expireDate = date('Y-m-d', strtotime("+" . $detail['duration'] . " month", strtotime($detail['subscription_end_date'])));
                    } else if ($detail['duration_in'] == "3") {
                        // move subs date $selected['duration'] month ahed
                        $new_expireDate = date('Y-m-d', strtotime("+" . $detail['duration'] . " days", strtotime($detail['subscription_end_date'])));
                    } else {
                        // do nothing
                    }
                }
                // End :- Get subscription end date

                $subscriptionData = array();
                $subscriptionData['subscription_duration_id'] = $detail['subscription_duration_id'];
                $subscriptionData['payment_status_id'] = 7;
                $subscriptionData['site_commision'] = "0";
                $subscriptionData['status_id'] = 1;
                $subscriptionData['user_id'] = $detail['user_id'];
                $subscriptionData['invoice_total'] = $detail['price'];
                $subscriptionData['created_by'] = $detail['user_id'];
                $subscriptionData['invoice_status'] = 1;
                $subscriptionData['amount'] = $detail['price'];
                $subscriptionData['currency'] = $detail['currency'];
                $subscriptionData['payment_date'] = date('Y-m-d H:i:s');
                $subscriptionData['amount_paid'] = $detail['price'];
                $subscriptionData['transaction_id'] = $result['transaction_id'];
                $subscriptionData['payment_instrument_no'] = '5100';
                $subscriptionData['payment_method_id'] = '1';
                $subscriptionData['payment_status'] = 7;
                $subscriptionData['subscription_start_date'] = date('Y-m-d');
                $subscriptionData['sale_type'] = 1;
                $subscriptionData['subscription_end_date'] = $new_expireDate;
        
                $response = $bookingModel->addBooking($api_url, $subscriptionData);
        
                if ($response['status'] == 1 && isset($response['id'])) {
                
                    $user_details['address'] = json_decode($user_details['address'][0], true);
                    $user_details['contact'] = json_decode($user_details['contact'][0], true);
                    $data = $bookingModel->getBookings($api_url, '', '', '', '', '', '', $response['id'], 'subscription');

                    $view = new viewModel(array('booking_details' => $data['results'], 'user_details' => $user_details));
                    $view->setTemplate('application/membership/printinvoice.phtml');
                    $printData = $this->getServiceLocator()->get('viewrenderer')->render($view);

                    // Store in PDF format
                    $dompdf = new \DOMPDF();
                    $dompdf->load_html($printData);
                    $dompdf->render();
                    $output = $dompdf->output();


                    $attachment = new MimePart($output);
                    $attachment->type = 'application/pdf';
                    $attachment->filename = 'invoice.pdf';
                    $attachment->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
                    $attachment->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;
                    $patterns = array('/{{user_name}}/i');
                    $replaces = array('<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>');

                    if ((isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features'])) || ($userFeatures['email'] == 1)) {
                        $common->sendMail($api_url, $user_details['email'], '', 11, '', $patterns, $replaces, $attachment);
                    }
                }
            } else {

                /* Make entry in failed payment history (pi21jan) */
                $check_failedpayArr = array('user_id' => $detail['user_id'], 'date' => date('Y-m-d', strtotime('-3 days')));
                $res = $common->check_failed_payment_history($api_url, $check_failedpayArr);
                if ($res) {
                    // there is a failed payment in last three days then set the subscription to basic one 
                    $ongoing_subscription = $common->getSubscriptiondetails($api_url,$detail['user_id']);
                    $common->addSubscription($api_url, array('user_id' => $detail['user_id'], 'id' => $ongoing_subscription[0]['id']));

                    $delete_arr = array('user_id' => $detail['user_id']);
                    $common->deletefailedpayment($api_url, $delete_arr);
                } else {
                    $failed_payment_data = array('user_id' => $detail['user_id'], 'sale_type' => '1', 'instrument_no' => '', 'date' => date('Y-m-d H:i:s'));
                    $common->setfailedpayment($api_url, $failed_payment_data);
                }
                /* Failed payment code ends here */

                $consumer = new Consumers();
                // Start :- Get admin email id
                $res = $consumer->getConsumerdetails($api_url, 1);
                if (count($res) > 0) {
                    $adminEmailId = $res['email'];
                }

                // End :- Get admin email id
                $pattern = array('/{{user_name}}/i', '/{{subscription_name}}/i', '/{{error_message}}/i');
                $replace = array('<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>', $detail['subscription_name'], '<strong>Transaction failed with message :- </strong>' . $result['msg']);

                if ((isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features'])) || ($userFeatures['email'] == 1)) {

                    $common->sendMail($api_url, $user_details['email'], '', 19, '', $pattern, $replace, '', $adminEmailId);
                }
            }
        }else{
			/* Make entry in failed payment history (pi21jan) */
			$check_failedpayArr = array('user_id' => $detail['user_id'], 'date' => date('Y-m-d', strtotime('-3 days')));
			$res = $common->check_failed_payment_history($api_url, $check_failedpayArr);
			if ($res) {
				// there is a failed payment in last three days then set the subscription to basic one 
				$ongoing_subscription = $common->getSubscriptiondetails($api_url,$detail['user_id']);
				$common->addSubscription($api_url, array('user_id' => $detail['user_id'], 'id' => $ongoing_subscription[0]['id']));

				$delete_arr = array('user_id' => $detail['user_id']);
				$common->deletefailedpayment($api_url, $delete_arr);
			} else {
				$failed_payment_data = array('user_id' => $detail['user_id'], 'sale_type' => '1', 'instrument_no' => '', 'date' => date('Y-m-d H:i:s'));
				$common->setfailedpayment($api_url, $failed_payment_data);
			}
			/* Failed payment code ends here */
		} // End:-- Get card details
    }

    public function renewcardAction()
    {
        set_time_limit(0);
        $model = new Practitioners();
        $common = $this->getServiceLocator()->get('Application\Model\Common');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $filter = array('card_expiration_hash' => md5(date('n-Y')));
        $cardDetails = $common->getUserCardDetails($api_url, $filter);
        // Start :- Get admin email id
        $adminEmailId='';
        $consumer = new Consumers();

        $res = $consumer->getConsumerdetails($api_url, 1);
        if (count($res) > 0) {
            $adminEmailId = $res['email'];
        }
        
        $card = end($cardDetails);
        
        /* renew card code for all users pi20jan */
        if (count($cardDetails)>0) {
            foreach($cardDetails as  $card) {
                
                $user_details = $model->getSPDetails($api_url, $card['user_id']);
                
                // getting use email permissions
                $subscriptionDetails = $common->getSubscriptiondetails($api_url, $card['user_id'], true);  // getting service provider subscription details
                $userFeatures = $common->getFeatures($api_url, $card['user_id']);

                if ((isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features'])) || ($userFeatures['email'] == 1)) {

                    $pattern = array('/{{user_name}}/i');
                    $replace = array('<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>');
                    $common->sendMail($api_url, $user_details['email'], '', 20, '', $pattern, $replace, '', $adminEmailId);
                    $common->sendMail($api_url, $adminEmailId, '', 20, '', $pattern, $replace, '', '');
                }
        
            }
        }
        exit;
    }

}

?>
