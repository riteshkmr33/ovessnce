<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Application\Model\Practitioners;
use Application\Model\Consumers;
use Application\Model\Bookings;
use Application\Model\Transactions;
use Application\Model\FrontEndAuth;
use Zend\Session\Container;
use Application\Form\BookingForm;
use Application\Form\WishlistForm;
use Application\Form\CheckoutForm;
use Zend\View\Renderer\PhpRenderer;
use Application\Model\Common;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\InputFilter\InputFilter;

class BookingController extends AbstractActionController
{

    public function indexAction()
    {
        return $this->redirect()->toRoute('home');
        //return new ViewModel();
    }

    public function scheduleAction()
    {
        $session = new Container('bookingData');
        $request = $this->getRequest();
        if ($request->isPost() || isset($session->bookingData)) {
            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                return $this->redirect()->toRoute('practitioner', array('action' => 'list'));
            }

            
            $bookingModel = new Bookings;
            $model = new Practitioners();
            $slots = $errors = array();
            $address = array();
            $work_address = array();

            /* Validation for booking form start here */
            $filter = new InputFilter;
            $filter->add(array('name' => 'service_location', 'required' => true));
            $filter->add(array('name' => 'service_id', 'required' => true));
            $filter->add(array('name' => 'duration', 'required' => true));
            $filter->add(array('name' => 'service_date', 'required' => true));
            /* Validation for booking form end here */

            $api = new Api();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $res = $api->curl($api_url . "/api/spusers/" . $id . "/", array(), "GET");

            if ($res->getStatusCode() != 200) {
                return $this->redirect()->toRoute('practitioner', array('action' => 'list'));
            }

            $content = json_decode($res->getBody(), true);

            // retrieving work_address
            foreach ($content['work_address'] as $wadd) {
                $work_address[] = json_decode($wadd, true);
            }

            // retrieving services
            foreach ($content['service'] as $service) {
                $service_list[] = $temp = json_decode($service, true);
                $services[$temp['id']] = $temp['duration'];
            }

            $form = new BookingForm($work_address, $service_list);

            $auth = new FrontEndAuth;

            if (!$auth->hasIdentity(4)) {
                if ($request->isPost()) {

                    $form->setInputFilter($filter);
                    $form->setData($request->getPost());

                    if ($form->isValid()) {

                        $session->bookingData = $request->getPost();
                    } else {
                        $errors = $form->getMessages();
                        isset($errors['service_location']['isEmpty']) ? $this->flashMessenger()->addErrorMessage('Please select service location') : '';
                        isset($errors['service_id']['isEmpty']) ? $this->flashMessenger()->addErrorMessage('Please select service.') : '';
                        isset($errors['duration']['isEmpty']) ? $this->flashMessenger()->addErrorMessage('Please select service duration.') : '';
                        isset($errors['service_date']['isEmpty']) ? $this->flashMessenger()->addErrorMessage('Please select service date.') : '';
                        return $this->redirect()->toRoute('practitioner', array('action' => 'view', 'id' => $id));
                    }
                }
                $session->sp = $id;

                return $this->redirect()->toRoute('login', array('action' => 'index'));
            }

            if (isset($session->bookingData)) {
                $bookingData = $session->bookingData;
                $form->bind($session->bookingData);
                //$session->offsetUnset('bookingData');
            }

            if ($request->isPost() || isset($session->bookingData)) {

                $form->setInputFilter($filter);
                ($request->isPost()) ? $form->setData($request->getPost()) : $form->setData($session->bookingData);

                if ($form->isValid()) {

                    // Create a session
                    $bookingsession = new Container('bookingsession');
                    $bookingsession->locationid = ($request->isPost()) ? $request->getPost('service_location') : $session->bookingData->service_location;
                    $bookingsession->serviceid = ($request->isPost()) ? $request->getPost('service_id') : $session->bookingData->service_id;
                    $bookingsession->durationid = ($request->isPost()) ? $request->getPost('duration') : $session->bookingData->duration;
                    $bookingsession->servicedate = ($request->isPost()) ? $request->getPost('service_date') : $session->bookingData->service_date;
                    $bookingsession->serviceprice = $model->getServicePrice($api_url, ($request->isPost()) ? $request->getPost('duration') : $session->bookingData->duration);
                    $bookingsession->sp_id = $id;

                    return $this->redirect()->toRoute('booking', array('action' => 'checkout'));
                } else {
                    $errors = $form->getMessages();
                    isset($errors['service_location']['isEmpty']) ? $this->flashMessenger()->addErrorMessage('Please select service location') : '';
                    isset($errors['service_id']['isEmpty']) ? $this->flashMessenger()->addErrorMessage('Please select service.') : '';
                    isset($errors['duration']['isEmpty']) ? $this->flashMessenger()->addErrorMessage('Please select service duration.') : '';
                    isset($errors['service_date']['isEmpty']) ? $this->flashMessenger()->addErrorMessage('Please select service date.') : '';
                    return $this->redirect()->toRoute('practitioner', array('action' => 'view', 'id' => $id));
                }
            }
        } else {
            return $this->redirect()->toRoute('practitioner', array('action' => 'list'));
        }

        exit;
    }

    public function checkoutAction()
    {
        $bookingsession = new Container('bookingsession');

        if ((isset($bookingsession->locationid) && !empty($bookingsession->locationid)) && (isset($bookingsession->serviceid) && !empty($bookingsession->serviceid)) && (isset($bookingsession->durationid) && !empty($bookingsession->durationid)) && (isset($bookingsession->servicedate) && !empty($bookingsession->servicedate))) {
            
            // getting banner for this page
            $common = new Common;
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $banners = $common->getBanner($api_url, 14);
            
            $form = new CheckoutForm($this->getServiceLocator()->get('config')['payment_methods']);
            $session = new Container('frontend');
            $form->get('name_on_card')->setValue($session->first_name . ' ' . $session->last_name);
            $form->get('emailid')->setValue($session->email);
            $bookingsession = new Container('bookingsession');
            $bookingamount = $bookingsession->price;
            return new ViewModel(array('form' => $form, 'errorMsgs' => $this->flashMessenger()->getCurrentErrorMessages(), 'banners' => $banners, 'bookingamount' => $bookingamount, 'merchant_id' => $this->getServiceLocator()->get('Config')['payment_gateway']['merchant_id']));
        } else {
            return $this->redirect()->toRoute('practitioner', array('action' => 'list'));
        }
    }

    // Brain tree payment gateway integration //
    public function paymentAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $bookingsession = new Container('bookingsession');
            $session = new Container('frontend');
            $bookingModel = new Bookings();
            $trans = new Transactions();
            $model = new Practitioners();
            $consumer = new Consumers();
	    $common = new Common;
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $data['name'] = $request->getPost('name_on_card');
            $data['email'] = $request->getPost('emailid');
            $card_type = $request->getPost('card_type');
            $data['card_no'] = $request->getPost('card_no');
            $data['month'] = $request->getPost('month');
            $data['year'] = $request->getPost('year');
            $data['cvv_no'] = $request->getPost('cvv_no');
            $data['amount'] = $bookingsession->price;
            $data['currency'] = $bookingsession->currency;

            $result = $trans->processPayment($this->getServiceLocator()->get('Config'), $data);

            if ($result['status'] == 1) {
                //if (1 == 1) {

                $bookingData = array();
                $bookingData['transaction_id'] = $result['transaction_id'];
                $bookingData['service_provider_id'] = $bookingsession->sp_id;
                $bookingData['user_id'] = $session->userid;
                $bookingData['service_provider_service_id'] = $bookingsession->durationid;
                //$bookingData['booked_date'] = $bookingsession->servicedate;
                $bookingData['booked_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $bookingsession->servicedate)));
                $bookingData['booking_status'] = 5;
                $bookingData['payment_status'] = 7;
                $bookingData['status_id'] = 1;
                $bookingData['sale_type'] = 3;
                $bookingData['currency'] = $bookingsession->currency;
                $bookingData['amount'] = str_replace(array('USD$', 'CAD$', 'USD ', 'CAD ', 'USD', 'CAD', '$'), array('', '','', '','', '', ''), $bookingsession->serviceprice);   // as suggested by kanhaiya sir on 1-8-2014
                $bookingData['site_commision'] = $data['amount'];
                $bookingData['invoice_total'] = str_replace(array('USD$', 'CAD$', 'USD ', 'CAD ', 'USD', 'CAD', '$'), array('', '','', '','', '', ''), $bookingsession->serviceprice);
                ;    // as suggested by kanhaiya sir on 1-8-2014
                $bookingData['invoice_status'] = 1;
                $bookingData['payment_status_id'] = 7;
                $bookingData['amount_paid'] = $data['amount'];
                $bookingData['payment_date'] = date('Y-m-d H:i:s');
                $bookingData['payment_instrument_no'] = substr($data['card_no'], (strlen($data['card_no']) - 4), 4);
                $bookingData['payment_method_id'] = $card_type;
                $bookingData['created_by'] = $session->userid;
                $bookingData['service_address_id'] = $bookingsession->locationid;
                //print_r($bookingData); exit;
                $response = $bookingModel->addBooking($api_url, $bookingData);
                if ($response['status'] == 1 && isset($response['id'])) {
                    //if (1==1) {
                    
                    /* Generate attachment code starts here*/
                    $sp_details = $model->getSPDetails($api_url, $bookingsession->sp_id);
                    $sp_details['contact'] = json_decode($sp_details['contact'][0], true);
                    $user_details = $consumer->getConsumerdetails($api_url, $session->userid);
                    $user_details['address'] = json_decode($user_details['address'][0], true);
                    $user_details['contact'] = json_decode($user_details['contact'][0], true);
                    $data = $bookingModel->getBookings($api_url, '', $session->userid, '', '', '', '', $response['id']);
		    $service_rendering_address = ($data['results'][0]['service_address_id'] != '' && $data['results'][0]['service_address_id'] != 'None')?$common->address($api_url, $data['results'][0]['service_address_id']):'Not Available';
	
                    $view = new viewModel(array('booking_details' => $data['results'], 'user_details' => $user_details, 'service_rendering_address' => $service_rendering_address, 'service_rendering_details' =>$sp_details));
                    $view->setTemplate('application/booking/printinvoice.phtml');
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
                    /* Generate attachment code ends here*/

                    /* Send email code starts here */
                    $common = $this->getServiceLocator()->get('Application\Model\Common');
                    $pattern = array('/{{user_name}}/i');
                    $userreplace = array('<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>');
                    $spreplace = array('<strong>' . $sp_details['first_name'] . ' ' . $sp_details['last_name'] . '</strong>');
                    
                    $common->sendMail($api_url, $user_details['email'], '', 9, '', $pattern, $userreplace, $attachment);
                    
                    $subscriptionDetails = $common->getSubscriptiondetails($api_url, $bookingData['service_provider_id'], true);  // getting service provider subscription details
                    $userFeatures = $common->getFeatures($api_url, $bookingData['service_provider_id']);
                    
                    if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features']) && ($userFeatures['email'] == 1)) {
                        $common->sendMail($api_url, $sp_details['email'], '', 10, '', $pattern, $spreplace);
                    }

                    if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && ($userFeatures['sms'] == 1)) {
                        $common->sendMsg( $sp_details['contact']['phone_number'], 1, '', $pattern, array($sp_details['first_name'] . ' ' . $sp_details['last_name']));
                    }
                    /* Send email code ends here */

                    // unset all sessions
                    $bookingsession->offsetUnset('sp_id');
                    $bookingsession->offsetUnset('user_id');
                    $bookingsession->offsetUnset('price');
                    $bookingsession->offsetUnset('locationid');
                    $bookingsession->offsetUnset('serviceid');
                    $bookingsession->offsetUnset('durationid');
                    $bookingsession->offsetUnset('servicedate');
                    $bookingsession->offsetUnset('serviceprice');

                    echo json_encode(array('status' => '1', 'msg' => 'Booking successfully completed. <br /> Redirecting to invoice page..!!', 'booking_id' => $response['id']));
                } else {
                    echo json_encode(array('status' => '0', 'msg' => 'Transaction completed successfully with Transaction Id <strong>' . $result['transaction_id'] . '</strong>. <br /> Failed to book your request. Please contact to site admin..!!', 'errors' => $response['data']));
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
        $practitioners = new Practitioners;
        $common = new Common;
        $session = new Container('frontend');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        if ($auth->hasIdentity(4)) {  // Consumer invoice
            $data = $bookingModel->getBookings($api_url, '', $session->userid, '', '', '', '', $id);
            $userDetails = $consumers->getConsumerdetails($api_url, $data['results'][0]['user_id']);
        } else if ($auth->hasIdentity(3)) {   // service provider invoice
            $data = $bookingModel->getBookings($api_url, $session->userid, '', '', '', '', '', $id);
            $userDetails = $consumers->getConsumerdetails($api_url, $data['results'][0]['user_id']);
        } else {
            return $this->redirect()->toRoute('home');
        }

        $userDetails['address'] = json_decode($userDetails['address'][0], true);
        $userDetails['contact'] = json_decode($userDetails['contact'][0], true);
        //print_r($userDetails['address']); exit;
        if (isset($data['results']) && count($data['results']) == 1) {
	$sp_details = $practitioners->getSPDetails($api_url, $data['results'][0]['service_provider_id']);	
            $service_rendering_address = ($data['results'][0]['service_address_id'] != '' && $data['results'][0]['service_address_id'] != 'None')?$common->address($api_url, $data['results'][0]['service_address_id']):'Not Available';
            if ($this->getRequest()->getQuery('print') == 1) {
                $view = new viewModel(array('booking_details' => $data['results'], 'user_details' => $userDetails, 'service_rendering_address' => $service_rendering_address, 'service_rendering_details' =>$sp_details));
                $view->setTemplate('application/booking/printinvoice.phtml');
                $printData = $this->getServiceLocator()->get('viewrenderer')->render($view);
                // Store in PDF format
                $dompdf = new \DOMPDF();
                $dompdf->load_html($printData);
                $dompdf->render();
                $dompdf->stream('invoice.pdf', array('Attachment' => 0));
                exit;
            } else {
                $banners = $common->getBanner($api_url, 16);
                if ($session->user_type_id == 3) {
                    $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);
                    return new viewModel(array('booking_details' => $data['results'], 'user_details' => $userDetails, 'id' => $id, 'features' => $subscriptionDetails['features'], 'service_rendering_address' => $service_rendering_address, 'banners' => $banners,'service_rendering_details' =>$sp_details));
                } else {
                    return new viewModel(array('booking_details' => $data['results'], 'user_details' => $userDetails, 'id' => $id, 'service_rendering_address' => $service_rendering_address, 'banners' => $banners,'service_rendering_details' =>$sp_details));
                }
                
            }
        } else {
            return $this->redirect()->toRoute('home');
        }
        exit;
    }

    public function slotsAction()
    {
        $bookingModel = new Bookings;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $api = new Api();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $slots = $bookingModel->getAvailableSlots($api_url, $request->getPost('user'), date('Y-m-d', strtotime($request->getPost('service_date'))), $request->getPost('service_duration'), $request->getPost('address_id'));
            echo json_encode($slots);
        }
        exit;
    }

    public function getbookingAction()
    {
        $bookingModel = new Bookings;
        $session = new Container('frontend');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $page = $request->getPost('page');
            $user_id = $session->userid;
            $recordsPerPage = $request->getPost('items');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            $auth = new FrontEndAuth;

            if ($auth->hasIdentity(4)) {
                $bookings = $bookingModel->getBookings($api_url, '', $user_id, '', $page, $recordsPerPage,'', '','','',0);
            } else if ($auth->hasIdentity(3)) {
                $bookings = $bookingModel->getBookings($api_url, $user_id, '', '', $page, $recordsPerPage,'','','','',0);
            }

            echo json_encode($bookings['results']);
        }
        exit;
    }

    public function wishlistAction()
    {
        $request = $this->getRequest();
        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $data = array('user_id' => $request->getPost('sp_id'), 'created_by' => $session->userid, 'created_date' => date('Y-m-d h:i:s'), 'service_id' => $request->getPost('service_id'), 'service_duration_id' => $request->getPost('duration'), 'current_price' => $request->getPost('priceDel'), 'status_id' => 1);
        $url = $api_url . "/api/wishlist/";
        $res = $api->curl($url, $data, "POST");
        if ($res->getStatusCode() == 201 || $res->getStatusCode() == 200) {
            echo "Congratulation ! you have a new practitioner on your wishlist";
        } else {
            echo "Not Added Into Wishlist";
        }
        exit;
    }

    public function suggestAction()
    {
        $request = $this->getRequest();
        $bookingModel = new Bookings();
        $model = new Practitioners;
        $session = new Container('frontend');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        if ($request->isPost()) {
            $booking = $request->getPost('booking');
            $newDate = $request->getPost('date');

            if ($booking != '' && $newDate != '') {
                echo json_encode($bookingModel->suggestTime($api_url, $booking, str_replace('/', '-', $newDate), $session->userid, $session->user_type_id, $this->getServiceLocator()->get('Application\Model\Common')));
            } else {
                echo json_encode(array('status' => '0', 'msg' => 'Unable to suggest new data and time for appointment..!!', 'notifications' => $model->getNotifications($api_url)));
            }
        }
        exit;
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $model = new Practitioners;
            $bookingModel = new Bookings;
            $action = $request->getPost('action', 'profile');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            switch ($action) {
                case 'booking_status' :
                    $status = $request->getPost('status', 4);
                    $ids = $request->getPost('bookings');

                    if (is_array($ids) && count($ids) > 0) {
                        foreach ($ids as $id) {

                            if (!$bookingModel->changeBookingStatus($api_url, $id, $status, $this->getServiceLocator()->get('Application\Model\Common'))) {
                                echo json_encode(array('status' => 0, 'msg' => 'Failed to change booking status..!!'));
                                exit;
                            }
                        }
                        echo json_encode(array('status' => 1, 'msg' => 'Booking status changed successfully..!!', 'notifications' => $model->getNotifications($api_url)));
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'Failed to change booking status..!!'));
                    }

                    break;
            }
        }
        exit;
    }
    
    public function detailsAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $auth = new FrontEndAuth;
            $bookingModel = new Bookings;
            $id = $request->getPost('booking');
            $session = new Container('frontend');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            if ($auth->hasIdentity(4)) {
                $bookingDetails = $bookingModel->getBookings($api_url, '', $session->userid, '', '', '', '', $id);
            } else if ($auth->hasIdentity(3)) {
                $bookingDetails = $bookingModel->getBookings($api_url, $session->userid, '', '', '', '', '', $id);
            } else {
                echo json_encode(array('status' => 0, 'msg' => 'Not authorized to see the bookings details. Please login first..!!'));
                exit;
            }
            
            if ($bookingDetails != false) {
                echo json_encode(array('status' => 1, 'data' => $bookingDetails['results']));
            } else {
                echo json_encode(array('status' => 0, 'msg' => 'No booking found for this reference..!!'));
            }
        }
        
        exit;
    }
    
    public function mannualbookingAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $bookingModel = new Bookings;
            $session = new Container('frontend');
            parse_str($request->getPost('bookingData'), $bookingData);
            $name = explode(' ', $bookingData['username']);
            $bookingData['first_name'] = $name[0];
            $bookingData['last_name'] = isset($name[1])?$name[1]:'';
            $bookingData['booked_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $bookingData['booking_time'])));
            //echo '<pre>'; print_r($bookingData); exit;
            $bookingData['payment_status'] = 7;
            $bookingData['user_id'] = '';
            $bookingData['service_provider_id'] = $session->userid;
            $bookingData['status_id'] = 1;
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            
            if ($bookingData['booking_id'] != '') {
                if ($bookingModel->changeBookingStatus($api_url, $bookingData['booking_id'],$bookingData['booking_status'], $this->getServiceLocator()->get('Application\Model\Common'))) {
                    echo json_encode(array('status' => 1, 'msg' => 'Status updated successfully', 'data' => $bookingData['booking_id']));
                } else {
                    echo json_encode(array('status' => 0, 'msg' => 'Failed to update status', 'data' => $bookingData['booking_id']));
                }
            } else {
                unset($bookingData['booking_id']);
                echo json_encode($bookingModel->addManualBooking($api_url, $bookingData));
            }
        }
        exit;
    }

}
