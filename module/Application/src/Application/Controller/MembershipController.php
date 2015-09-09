<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Application\Model\Practitioners;
use Application\Form\CheckoutForm;
use Application\Model\FrontEndAuth;
use Application\Model\Bookings;
use Application\Model\Transactions;
use Application\Model\Consumers;
use Application\Model\Common;
use Zend\Session\Container;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class MembershipController extends AbstractActionController
{

    public function indexAction()
    {
        $api = new Api();
        $common = new Common;
        $model = new Practitioners;
        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];

        $subscription_id = $this->getSubscriptionid($api_url);

        $url = $api_url . "/api/subscription/";
        $url_sf = $api_url . "/api/sitefeatures/";

        $data = array('');

        $res = $api->curl($url, $data, "GET");
        $res_sf = $api->curl($url_sf, $data, "GET");

        if ($res->getStatusCode() == 200) {

            $content = json_decode($res->getBody(), true);

            if (count($content) > 0) {

                foreach ($content as $key => $value) {

                    $membership[$key] = $value;
                }
            }
        } else {
            $membership = array();
        }

        if ($res_sf->getStatusCode() == 200) {

            $content = json_decode($res_sf->getBody(), true);

            if (count($content) > 0) {
                $i = 0;
                foreach ($content as $data) {

                    $site_features_list[$i]['id'] = $data['id'];
                    $site_features_list[$i]['feature_name'] = $data['feature_name'];
                    $site_features_list[$i]['description'] = $data['description'];
                    $i++;
                }
            }
        } else {
            $site_features_list = array();
        }

        $banners = $common->getBanner($api_url, 15);

        return new ViewModel(array(
            'membership' => $membership,
            'site_features_list' => $site_features_list,
            'subscription_id' => $subscription_id,
            'banners' => $banners,
            'currency' => $model->getcurrency($api_url, $this->getRequest()->getServer('REMOTE_ADDR'))
        ));
    }

    public function checkoutAction()
    {

        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toUrl('/login');
        }

        $id = $this->params()->fromRoute('id');

        if ($id == '') {
            $this->redirect()->toRoute('membership');
        }

        $subscriptionsession = new Container('subscriptionsession');
        $session = new Container('frontend');

        $api = new Api();
        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];

        $model_practitioner = new Practitioners;
        $common = new Common;
        $selected_subscription = $model_practitioner->getSubscription($api_url, $id);
        $ongoing_subscription = $common->getSubscriptiondetails($api_url);
        //echo "<pre>";print_r($ongoing_subscription);die;
        
        /* check if there is a saved card : start */
        $savedCard_details = $common->getUserCardDetails($api_url,array('user_id'=>$session->userid));
        /* check if there is a saved card : end */

        $subscription_id = $this->getSubscriptionid($api_url);

        if ($id == '1') {
            if ($common->addSubscription($api_url, array('user_id' => $session->userid, 'id' => $ongoing_subscription[0]['id']))) {
                $this->flashMessenger()->addSuccessMessage("Basic subscription activated on your account..!!");
                return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
            } else {
                $this->flashMessenger()->addErrorMessage("Failed to activate basic subscription..!!");
                return $this->redirect()->toRoute('membership');
            }
        }

        $ongoingSubs_endDate = 'NA';
        $ongoingsubs_name = 'NA';
        $total_amount = 'NA';
        $new_expireDate = 'NA';
        $selectedSubs_name = 'NA';
        $subscription_duration_id = 'NA';
        $selectedCurrency = $model_practitioner->getcurrency($api_url, $this->getRequest()->getServer('REMOTE_ADDR'));

        if ($id == $subscription_id) {
            /* same subscription now renew */
            $checkout_type = "renew";
            if (count($selected_subscription) > 0) {
                if (count($ongoing_subscription) > 0) {

                    (!empty($ongoing_subscription[0]['subscription_end_date'])) ? $ongoingSubs_endDate = $ongoing_subscription[0]['subscription_end_date'] : '';
                    $ongoing_duration = json_decode($ongoing_subscription[0]['subscription_duration'], true);
                    if (count($ongoing_duration) > 0) {
                        $ongoingsubs_name = $ongoing_duration['subscription_name'];
                    }
                } else {
                    $ongoingSubs_endDate = date('Y-m-d');
                }

                $selected = json_decode($selected_subscription['duration'][0], true);
                if (count($selected) > 0) {
                    $total_amount = $selected['price'];
                    $selectedSubs_name = $selected['subscription_name'];
                    $subscription_duration_id = $selected['id'];
                    if (isset($selected['duration_in'])) {
                        if ($selected['duration_in'] == "1") {
                            // move subs date $selected['duration'] year ahed
                            $new_expireDate = date('Y-m-d', strtotime("+" . $selected['duration'] . " year", strtotime($ongoingSubs_endDate)));
                        } else if ($selected['duration_in'] == "2") {
                            // move subs date $selected['duration'] month ahed
                            $new_expireDate = date('Y-m-d', strtotime("+" . $selected['duration'] . " month", strtotime($ongoingSubs_endDate)));
                        } else if ($selected['duration_in'] == "3") {
                            // move subs date $selected['duration'] month ahed
                            $new_expireDate = date('Y-m-d', strtotime("+" . $selected['duration'] . " days", strtotime($ongoingSubs_endDate)));
                        } else {
                            // do nothing
                        }
                    }
                }
            }
        } else {
            /* new subscription */
            $checkout_type = "new";
            if (count($selected_subscription) > 0) {
                if (count($ongoing_subscription) > 0) {
                    (!empty($ongoing_subscription[0]['subscription_end_date'])) ? $ongoingSubs_endDate = $ongoing_subscription[0]['subscription_end_date'] : '';
                    $ongoing_duration = json_decode($ongoing_subscription[0]['subscription_duration'], true);
                    if (count($ongoing_duration) > 0) {
                        $ongoingsubs_name = $ongoing_duration['subscription_name'];
                    }
                }

                $selected = json_decode($selected_subscription['duration'][0], true);
                if (count($selected) > 0) {
                    $total_amount = $selected['price'];
                    $selectedSubs_name = $selected['subscription_name'];
                    $subscription_duration_id = $selected['id'];
                    if (isset($selected['duration_in'])) {
                        if ($selected['duration_in'] == "1") {
                            // move subs date $selected['duration'] year ahed
                            $new_expireDate = date('Y-m-d', strtotime("+" . $selected['duration'] . " year", strtotime(date('Y-m-d'))));
                        } else if ($selected['duration_in'] == "2") {
                            // move subs date $selected['duration'] month ahed
                            $new_expireDate = date('Y-m-d', strtotime("+" . $selected['duration'] . " month", strtotime(date('Y-m-d'))));
                        } else if ($selected['duration_in'] == "3") {
                            // move subs date $selected['duration'] month ahed
                            $new_expireDate = date('Y-m-d', strtotime("+" . $selected['duration'] . " days", strtotime(date('Y-m-d'))));
                        } else {
                            // do nothing
                        }
                    }
                }
            }
        }
        //echo $new_expireDate;die;
        $subscriptionsession->serviceprice = $total_amount;
        $subscriptionsession->subscription_duration_id = $subscription_duration_id;
        $subscriptionsession->currency = $selectedCurrency;
        $subscriptionsession->subscription_end_date = $new_expireDate;

        $form = new CheckoutForm($this->getServiceLocator()->get('config')['payment_methods']);
        $session = new Container('frontend');
        $form->get('name_on_card')->setValue($session->first_name . ' ' . $session->last_name);
        $form->get('emailid')->setValue($session->email);

        $banners = $common->getBanner($api_url, 14);

        return new ViewModel(
                array(
                    'form' => $form,
                    'errorMsgs' => $this->flashMessenger()->getCurrentErrorMessages(),
                    'checkout_type' => $checkout_type,
                    'ongoingSubs_endDate' => $ongoingSubs_endDate,
                    'ongoingsubs_name' => $ongoingsubs_name,
                    'total_amount' => $total_amount,
                    'new_expireDate' => $new_expireDate,
                    'selectedSubs_name' => $selectedSubs_name,
                    'selectedCurrency' => $selectedCurrency,
                    'banners' => $banners,
                    'savedCard_details' => end($savedCard_details),
                    'merchant_id' => $this->getServiceLocator()->get('Config')['payment_gateway']['merchant_id']
                )
        );
    }

    public function paymentAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $subscriptionsession = new Container('subscriptionsession');
            $session = new Container('frontend');
            $bookingModel = new Bookings();
            $trans = new Transactions();
            $model = new Practitioners();
            $common = new Common;
            $consumer = new Consumers();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
           
            if ($request->getPost('usesavedcard')!=='1') {
				
                $data['name'] = $request->getPost('name_on_card');
                $data['email'] = $request->getPost('emailid');
                $card_type = $request->getPost('card_type');
                $data['card_no'] = $request->getPost('card_no');
                $data['month'] = $request->getPost('month');
                $data['year'] = $request->getPost('year');
                $data['cvv_no'] = $request->getPost('cvv_no');
                $data['amount'] = $subscriptionsession->serviceprice;
                $data['rememberme'] = $request->getPost('rememberme');
                $data['use_for_renew'] = $request->getPost('use_for_renew');
                $data['currency'] = $subscriptionsession->currency;
                
                $result = $trans->processPayment($this->getServiceLocator()->get('Config'), $data);
                
            }else{
				
                $savedCard_details = $common->getUserCardDetails($api_url,array('user_id'=>$session->userid));
                $cardDetails = end($savedCard_details);
				
				/* get saved card details : statr */
				$details = $trans->getcarddetails($this->getServiceLocator()->get('Config'),$cardDetails['creditCardDetails_token']);
				if (is_object($details) && isset($details->last4) && isset($details->cardType)) {
					
					$data['card_no'] = $details->last4;			
					
					if ($details->cardType=="Visa") {
						$card_type=1;
					}else if ($details->cardType=="MasterCard") {
						$card_type=2;
					}else if ($details->cardType=="American Express") {
						$card_type=3;
					}
				}	
				/* get saved card details : statr */
									
                $data['customerDetails_id'] = $cardDetails['customerDetails_id'];
                $data['paymentMethodToken'] = $cardDetails['creditCardDetails_token'];
                $data['amount'] = $subscriptionsession->serviceprice;
                $data['currency'] = $subscriptionsession->currency;
				
                $result = $trans->processPayment($this->getServiceLocator()->get('Config'), $data,'1');
            }

            if ($result['status'] == 1) {

                // save user card details
                if ($data['rememberme'] == 1 || $data['use_for_renew'] == "1") {
                    $usersCardDetails = array();
                    $usersCardDetails['user_id'] = $session->userid;
                    $usersCardDetails['creditCardDetails_token'] = $result['creditCardDetails_token'];
                    $usersCardDetails['customerDetails_id'] = $result['customerDetails_id'];
                    $usersCardDetails['use_for_renew'] = ($data['use_for_renew'] == 1 || $data['use_for_renew'] == "1") ? 1 : 0;
                    $usersCardDetails['card_expiration_hash'] = md5($data['month'] . '-' . $data['year']);

                    //$response = $bookingModel->addUsersCardDetails($api_url, $usersCardDetails);
                    $response = $trans->updateCard($this->getServiceLocator()->get('Config'), $usersCardDetails);
                }

                $subscriptionData = array();
                $subscriptionData['subscription_duration_id'] = $subscriptionsession->subscription_duration_id;
                $subscriptionData['payment_status_id'] = 7;
                $subscriptionData['site_commision'] = "0";
                $subscriptionData['status_id'] = 1;
                $subscriptionData['user_id'] = $session->userid;
                $subscriptionData['invoice_total'] = str_replace(array('USD$', 'CAD$', '$'), array('', '', ''), $subscriptionsession->serviceprice);
                $subscriptionData['created_by'] = $session->userid;
                $subscriptionData['invoice_status'] = 1;
                $subscriptionData['amount'] = str_replace(array('USD$', 'CAD$', '$'), array('', '', ''), $subscriptionsession->serviceprice);
                $subscriptionData['currency'] = $subscriptionsession->currency;
                $subscriptionData['payment_date'] = date('Y-m-d H:i:s');
                $subscriptionData['amount_paid'] = $subscriptionsession->serviceprice;
                $subscriptionData['transaction_id'] = $result['transaction_id'];
                $subscriptionData['payment_instrument_no'] = (strlen($data['card_no']) > 4) ? substr($data['card_no'], (strlen($data['card_no']) - 4), 4) : $data['card_no'] ;
                $subscriptionData['payment_method_id'] = $card_type;
                $subscriptionData['payment_status'] = 7;
                $subscriptionData['subscription_start_date'] = date('Y-m-d');
                $subscriptionData['sale_type'] = 1; /* 1 for subscription */
                $subscriptionData['subscription_end_date'] = $subscriptionsession->subscription_end_date;
                //$subscriptionData['user_card_id'] = $result['user_card_id'];


                $response = $bookingModel->addBooking($api_url, $subscriptionData);

                if ($response['status'] == 1 && isset($response['id'])) {

                    /* Send email code starts here */
                    $common = new Common;
                    if ($template = $common->emailTemplate($api_url, 11)) {

                        //$sp_details = $model->getSPDetails($api_url, $bookingsession->sp_id);
                        //$user_details = $consumer->getConsumerdetails($api_url, $session->userid);
                        $user_details = $model->getSPDetails($api_url, $session->userid);
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


                        $mail = new Message();
                        $transport = new \Zend\Mail\Transport\Sendmail();
                        $html = new MimePart(preg_replace('/{{user_name}}/i', '<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>', $template['content']));
                        $html->type = "text/html";

                        $body = new MimeMessage();
                        $body->setParts(array($html, $attachment));

                        $mail->setBody($body)
                                ->setFrom($template['fromEmail'], 'Ovessence')
                                ->addTo($user_details['email'], '')
                                ->setSubject($template['subject']);
                        $transport->send($mail);

                        /* Send email code ends here */
                    }

                    // unset all sessions
                    $subscriptionsession->offsetUnset('currency');
                    $subscriptionsession->offsetUnset('serviceprice');
                    $subscriptionsession->offsetUnset('subscription_duration_id');
                    $subscriptionsession->offsetUnset('subscription_end_date');

                    echo json_encode(array('status' => '1', 'msg' => 'Subscription updated successfully. <br /> Redirecting to invoice page..!!', 'subscription_id' => $response['id']));
                } else {
                    echo json_encode(array('status' => '0', 'msg' => 'Transaction completed successfully with Transaction Id <strong>' . $result['transaction_id'] . '</strong>. <br /> Failed to complete your request. Please contact to site admin..!!', 'errors' => $response['data']));
                }
            } else {
                echo json_encode($result);
            }
        }

        exit;
    }

    public function invoiceAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $auth = new FrontEndAuth;
        $bookingModel = new Bookings;
        $consumers = new Consumers;
        $common = new Common();
        $practitioners = new Practitioners;
        $session = new Container('frontend');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        if ($auth->hasIdentity(3)) {   // service provider invoice
            $data = $bookingModel->getBookings($api_url, '', '', '', '', '', '', $id, 'subscription');
            $userDetails = $practitioners->getSPDetails($api_url, $session->userid);
        } else {
            return $this->redirect()->toRoute('home');
        }

        $userDetails['address'] = json_decode($userDetails['address'][0], true);
        $userDetails['contact'] = json_decode($userDetails['contact'][0], true);
        //print_r($userDetails['address']); exit;
        if (isset($data['results']) && count($data['results']) == 1) {
            if ($this->getRequest()->getQuery('print') == 1) {
                $view = new viewModel(array('booking_details' => $data['results'], 'user_details' => $userDetails));
                $view->setTemplate('application/membership/printinvoice.phtml');
                $printData = $this->getServiceLocator()->get('viewrenderer')->render($view);
                // Store in PDF format
                $dompdf = new \DOMPDF();
                $dompdf->load_html($printData);
                $dompdf->render();
                $dompdf->stream('invoice.pdf', array('Attachment' => 0));
                exit;
            } else {
                $banners = $common->getBanner($api_url, 16);
                return new viewModel(array('booking_details' => $data['results'], 'user_details' => $userDetails, 'id' => $id, 'banners' => $banners));
            }
        } else {
            return $this->redirect()->toRoute('home');
        }

        return new viewModel($this->invoicedetails());
    }

    public function getSubscriptionid($api_url)
    {
        $model_practitioner = new Practitioners;
        $common = new Common;
        $membership_details = $common->getSubscriptiondetails($api_url);

        if (count($membership_details) > 0) {
            foreach ($membership_details as $details) {
                $duration = json_decode($details['subscription_duration'], true);
                //$subscription_id = $duration['subscription_id'];
                $subscription_id = $duration['id'];
            }
        } else {
            $subscription_id = '';
        }

        return $subscription_id;
    }

}
