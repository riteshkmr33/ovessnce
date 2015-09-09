<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Application\Model\Practitioners;
use Application\Model\Transactions;
use Application\Model\Bookings;
use Application\Model\Common;
use Application\Model\Consumers;
use Application\Model\FrontEndAuth;
use Application\Model\Messages;
use Application\Model\Review;
use Zend\Session\Container;
use Application\Form\ReviewForm;
use Application\Form\AskForm;
use Application\Form\BookingForm;
use Application\Form\SPChangePasswordForm;
use Application\Form\SPaddservicesFrom;
use Application\Form\SPcomposemessageFrom;
use Application\Form\WishlistForm;
use Application\Form\SearchForm;
use Zend\ImageS3;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

//use DOMPDFModule\View\Model\PdfModel;

class PractitionerController extends AbstractActionController {

    public function indexAction() {
        $this->redirect()->toRoute('home');
        return new ViewModel(array(
        ));
    }

    public function viewAction() {

        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $getparams = $request->getQuery();
        isset($getparams['tab']) ? $tab = $getparams['tab'] : $tab = '';
        $common = new Common;

        $redirectUrl = array('controller' => 'Practitioner', 'action' => 'view');

        if (!empty($id) && $id != null) {

            $session = new Container('frontend');
            $model = new Practitioners();
            $bookingModel = new Bookings();
            $api = new Api();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $chat_url = $this->getServiceLocator()->get('config')['chatpath']['url'];
            $reviewFlag = false;

            if (isset($getparams['review']) && $getparams['review'] == 1 && isset($getparams['s_id'])) {
                // check service 
                $serivce_id = $getparams['s_id'];
                $user_id = $session->userid;
                $sp_id = $id;

                $result = $bookingModel->getBookings($api_url, $sp_id, $user_id, '4', "", "", $serivce_id);

                $reviewFlag = $model->setreviewFlg($result);
            } else {
                $reviewFlag = false;
            }

            $url = $api_url . "/api/spusers/" . $id . "/";
            $data = array('status_id' => 9);
            $res = $api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {

                $data = $this->getSpdata($res);
            } else {

                $redirectUrl = "/practitioner/list";
                return $this->redirect()->toUrl($redirectUrl);

                $content = '';
                $address = '';
                $work_address = '';
                $contact = '';
                $details = '';
                $service_list = '';
                $language = '';
            }



            //$forWishlist = new Container('last_url');

            $feedback_list = $this->getSPfeedback($id, $api, $api_url); //getting feedback list for service provider

            $video_list = $this->getSPmedia($id, $api, $api_url); //getting video list for service provider

            $options = $model->getSPoptions($id, $api_url); //getting available options for service provider

            $varification = $model->getSPvarification($id, $api_url); //getting varified/unverified ids for service provider	

            $bookings_count = $bookingModel->getBookingsCount($api_url, $id); //getting bookings count for service provider

            $ratings = $model->getSPRatings($id, $api_url, 'detailed'); //getting ratings for service provider

            $workdays = $model->getSPWorkdays($id, $api_url); //getting workdays for service provider
            //$all_services = $common->getAllservices($api,$api_url); //getting workdays for service provider
            //echo '<pre>'; var_dump($data); exit;

            if ($request->isPost()) {
                $map = $this->generateMap($api_url, $api, array('address' => $data['work_address']), '', trim($request->getPost('zip_code')));
            } else {
                $map = $this->generateMap($api_url, $api, array('address' => $data['work_address']), $session->userid);
            }

            $features = $common->getFeatures($api_url, $id);

            isset($features['chat']) ? $chat = $features['chat'] : $chat = 0;

            $bookingsession = new Container('bookingsession');
            $bookingsession->currency = $currency = $model->getcurrency($api_url, $this->getRequest()->getServer('REMOTE_ADDR'));

            /* Start:- Search form */

            $loggedInUserAddress = $model->getLoggedInUserAddress($session->userid, $session->user_type_id, $api_url);
            $treatment_list = $common->getAllservices($api_url);
            $search_form = new SearchForm($treatment_list, $common->getstatesByCountry($api_url, $loggedInUserAddress->country_id));
            /* $data = $this->request->getPost();
              if ($this->request->isPost()) {
              $search_form->bind($data);
              } */

            /* End:- Search form */

            // fetching subscription features 
            $subscriptionDetails = $common->getSubscriptiondetails($api_url, $id, true);

            // getting banner for this page
            $banners = $common->getBanner($api_url, 3);

            // getting response rate
            $response = $model->getResponseRate($api_url, $id);

            // getting advertisments
            $ad = $common->getAdvertisement($api_url, 3);

            return new ViewModel(array(
                'content' => $data['content'],
                'address' => $data['address'],
                'work_address' => $data['work_address'],
                'contact' => $data['contact'],
                'details' => $data['details'],
                'service_list' => $data['service_list'],
                'form_review' => $data['form_review'],
                'form_ask' => $data['form_ask'],
                'form_booking' => $data['form_booking'],
                'commission' => $data['commission'],
                'language' => $data['language'],
                'educations' => $data['educations'],
                'feedback' => $feedback_list,
                'ratings' => $ratings,
                'workdays' => $workdays,
                'response' => $response,
                'map' => $map['map'],
                'distances' => $map['distance'],
                'feedback_count' => count($feedback_list),
                'video_list' => $video_list,
                'options' => $options,
                'varification' => $varification,
                'booking_count' => $bookings_count,
                'auth' => $auth = new FrontEndAuth(),
                'loggedin_userid' => $session->userid,
                //'forWishlist' => $forWishlist, 
                'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
                'errors' => $this->flashMessenger()->getCurrentErrorMessages(),
                'wishlistform' => $data['form_wishlist'],
                'chat' => $chat,
                'chat_url' => $chat_url,
                'reviewFlag' => $reviewFlag,
                's_id' => $getparams['s_id'],
                'reviewTab' => $getparams['review'],
                'currency' => $currency,
                'reviewFlag' => $reviewFlag,
                'tab' => $tab,
                'errors' => $this->flashMessenger()->getCurrentErrorMessages(),
                'search_form' => $search_form,
                'baseurl' => $this->getServiceLocator()->get('config')['basepath']['url'],
                'features' => $subscriptionDetails['features'],
                'total_referred_from' => count($model->getSPreferrals($api_url, $id)),
                'banners' => $banners,
                'posted_zip' => $request->getPost('zip_code'),
                'advertisement' => $ad
            ));
        } else {

            $redirectUrl = "/practitioner/list";
            return $this->redirect()->toUrl($redirectUrl);
        }
    }

    public function getSpdata($res) {
        $content = json_decode($res->getBody(), true);
        $address = array();
        $work_address = array();
        $service = array();
        $educations = array();

        // retrieving addresses
        /* foreach ($content['address'] as $add) {

          $address[] = json_decode($add, true);
          //$temp = json_decode($temp, true);
          //echo '<pre>'; var_dump($temp); exit;
          } */

        //echo '<pre>'; var_dump($content['address']); exit;
        // retrieving work_address
        foreach ($content['work_address'] as $wadd) {
            $work_address[] = json_decode($wadd, true);
        }

        // retrieving contact
        foreach ($content['contact'] as $con) {
            $contact = json_decode($con, true);
        }

        // retrieving details
        foreach ($content['details'] as $det) {
            $details = json_decode($det, true);
        }

        //echo '<pre>'; var_dump($details); exit;
        // retrieving location types
        foreach ($content['location'] as $loc) {
            $temp_loc = json_decode($loc, true);
            $details['locations'][] = $temp_loc['location_type'];
        }

        // retrieving services
        foreach ($content['service'] as $data) {
            $service[] = json_decode($data, true);
        }

        // retrieving educations
        foreach ($content['education'] as $data) {
            $education = json_decode($data, true);
            $educations[] = $education['education_label'];
        }

        if (count($service) > 0) {
            $tmp = array();
            foreach ($service as $item) {
                if (!in_array($item['service_id'], $tmp)) {
                    $service_list[] = $item;
                    $tmp[] = $item['service_id'];
                }
            }
        } else {
            $service_list = array();
        }

        // question answer data
        $i = 0;
        $responseTimes = array();
        $answered = 0;

        // retrieving commision

        if (is_array($content['sp_commision'])) {
            foreach ($content['sp_commision'] as $sp_commission) {
                $sp_commission = json_decode($sp_commission, true);
                if ($sp_commission['status_id'] == 1) {
                    $commission = $sp_commission['commision'];
                }
            }
        }

        if ($commission == 0) {
            $api = new Api();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            $setting_res = $api->curl($api_url . "/api/sitesetting/1/", array(), "GET");
            $setting_data = json_decode($setting_res->getBody(), true);
            $commission = $setting_data['setting_value'];
        }

        // setting payment price
        $bookingsession = new Container('bookingsession');
        $bookingsession->price = $commission;

        // Calculating response
        $response = array('years' => (count($responseTimes) > 0 && array_sum($responseTimes) > 0) ? floor((array_sum($responseTimes) / count($responseTimes)) / 31536000) : 0,
            'months' => (count($responseTimes) > 0 && array_sum($responseTimes) > 0) ? floor((array_sum($responseTimes) / count($responseTimes)) / 2592000) : 0,
            'days' => (count($responseTimes) > 0 && array_sum($responseTimes) > 0) ? floor((array_sum($responseTimes) / count($responseTimes)) / 86400) : 0,
            'hours' => (count($responseTimes) > 0 && array_sum($responseTimes) > 0) ? floor((array_sum($responseTimes) / count($responseTimes)) / 3600) : 0,
            'minutes' => (count($responseTimes) > 0 && array_sum($responseTimes) > 0) ? floor((array_sum($responseTimes) / count($responseTimes)) / 60) : 0);

        $form_review = new ReviewForm($service_list);
        $form_ask = new AskForm();
        //array_push($address, $work_address);
        $form_booking = new BookingForm($work_address, $service_list);
        $form_wishlist = new WishlistForm($address, $service_list);

        $session = new Container('bookingData');
        if (isset($session->bookingData)) {
            $bookingData = $session->bookingData;
            $form_booking->bind($session->bookingData);
            $form_booking->get('service_id')->setAttributes(array('disabled' => false));
            $form_booking->get('duration')->setAttributes(array('disabled' => false));
            $form_booking->get('service_date')->setAttributes(array('disabled' => false));
            //$session->offsetUnset('bookingData');			
        }

        // retrieving language
        foreach ($content['language'] as $data) {

            $langArr = json_decode($data, true);

            if (!empty($langArr)) {
                $lang[] = $langArr['service_language'];
            }
        }

        if (count($lang) > 0) {
            $language = implode(', ', $lang);
        } else {
            $language = '';
        }

        return array(
            'content' => $content,
            'address' => $address,
            'work_address' => $work_address,
            'contact' => $contact,
            'details' => $details,
            'service' => $service,
            'educations' => $educations,
            'service_list' => $service_list,
            'commission' => $commission,
            'response' => $response,
            'form_review' => $form_review,
            'form_ask' => $form_ask,
            'form_booking' => $form_booking,
            'language' => $language,
            'form_wishlist' => $form_wishlist,
        );
    }

    public function listAction() {
        $loggedInUserAddress = array();
        $common = new Common;
        $model = new Practitioners;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $session = new Container('frontend');
        $loggedInUser = $session->userid;
        $userType = $session->user_type_id;
        $auth = new FrontEndAuth();
        $loggedInUserAddress = $model->getLoggedInUserAddress($loggedInUser, $userType, $api_url);


        if ($this->getRequest()->isXmlHttpRequest()) {

            if ($this->getRequest()) {
                $post = $this->getRequest()->getPost();
                $remoteAddr = $this->getRequest()->getServer('REMOTE_ADDR');
                $model->userlistByFilterData($post, $api_url, $remoteAddr);
                exit;
            } else {

                echo ''; // return null
                exit;
            }
        } else {

            $sp_list = '';

            $treatment_list = $common->getAllservices($api_url);
            $country_list = $common->getCountries($api_url);


            $search_form = new SearchForm($treatment_list, $common->getstatesByCountry($api_url, $loggedInUserAddress->country_id));

            $data = $this->request->getPost();
            if ($this->request->isPost()) {
                $search_form->bind($data);
            }

            // getting banner for this page
            $banners = $common->getBanner($api_url, 2);

            // getting advertisments
            $ad = $common->getAdvertisement($api_url, 2);

            return new ViewModel(array(
                'treatment_list' => $treatment_list,
                'country_list' => $country_list,
                'address' => $loggedInUserAddress,
                'state' => $common->getstatesByCountry($api_url, $loggedInUserAddress->country_id),
                'search_form' => $search_form,
                'auth' => $auth,
                'location_types' => $model->getLocationTypes($api_url),
                'banners' => $banners,
                'advertisement' => $ad
            ));
        }
    }

    public function dashboardAction() {
        $auth = new FrontEndAuth;
        $model = new Practitioners;
        $bookingModel = new Bookings;
        $common = new Common;
        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toRoute('login', array('action' => 'index'));
        }
        $session = new Container('frontend');
        // 0:- both(sms & mail) are verified 1:- any one or both(sms & mail) are unverified
        $verifystatus = (($session->email_verification_status == 1) && ($session->sms_verification_status == 1)) ? 0 : 1;

        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $res = $api->curl($api_url . "/api/spusers/" . $session->userid . "/", array(''), "GET");

        if ($res->getStatusCode() != 200) {
            return $this->redirect()->toRoute('practitioner', array('action' => 'list'));
        }

        $data = $this->getSpdata($res);
        array_pop($data['address']);

        //echo '<pre>'; print_r($data); exit;

        $services_count = $this->getservicesdata($session->userid, $api, $api_url); // get all services list
        //echo '<pre>'; print_r($services_count); exit;
        $parentService = isset($services_count['results'][0]['category_id']) ? $model->getParentService($api_url, $services_count['results'][0]['category_id']) : 'Not Available';
        $bussCategoryName = ($parentService) ? $parentService['category_name'] : 'Not Avaialable';

        $service_category_list = $this->getCategories(); //getting services list for service provider

        $feedback_list = $this->getSPfeedback($session->userid, $api, $api_url); //getting feedback list for service provider

        $video_list = $this->getSPmedia($session->userid, $api, $api_url, 2, 5); //getting video list for service provider

        $image_list = $this->getSPmedia($session->userid, $api, $api_url, 1, 5); //getting images list for service provider

        $options = $model->getSPoptions($session->userid, $api_url); //getting available options for service provider	

        $varification = $model->getSPvarification($session->userid, $api_url); //getting varified/unverified ids for service provider	

        $bookings_count = $bookingModel->getBookingsCount($api_url, $session->userid); //getting bookings count for service provider

        $workdays = $model->getSPWorkdays($session->userid, $api_url); //getting workdays for service provider

        $addservice_form = new SPaddservicesFrom($service_category_list);

        // Getting practitioner's organization
        $org = $model->getSPOrganization($api_url, $session->userid);
        $data['details']['organization'] = isset($org['organization_name']) ? $org['organization_name'] : '';

        $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);

        $features = $common->getFeatures($api_url, $session->userid);
        isset($features['chat']) ? $chat = $features['chat'] : $chat = 0;

        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'states' => $common->getstatesByCountry($api_url),
            'countries' => $common->getCountries($api_url),
            'AllEducations' => $model->getEducations($api_url),
            'languages' => $model->getLanguages($api_url),
            'content' => $data['content'],
            'addresses' => $data['address'],
            'work_address' => $data['work_address'],
            'contact' => $data['contact'],
            'details' => $data['details'],
            'service_list' => $data['service_list'],
            'form_review' => $data['form_review'],
            'form_ask' => $data['form_ask'],
            'form_booking' => $data['form_booking'],
            'commission' => $data['commission'],
            'language' => $data['language'],
            'educations' => $data['educations'],
            'addservice_form' => $addservice_form,
            'feedback' => $feedback_list,
            'ratings' => $ratings,
            'workdays' => $workdays,
            'available_days' => $model->getAvailableDays($api_url),
            'availability' => $model->getSPWorkdays($session->userid, $api_url, '', true),
            'appointment_delay' => $model->getAppointmentDelay($session->userid, $api_url),
            'response' => $data['response'],
            'feedback_count' => count($feedback_list),
            'video_list' => $video_list,
            'image_list' => $image_list,
            'options' => $options,
            'varification' => $varification,
            'booking_count' => $bookings_count,
            'services_count' => $services_count['count'],
            'newsletter_count' => count($model->getSPnewsletter($session->userid, $api_url)),
            'notifications' => $model->getNotifications($api_url),
            'auth' => $auth,
            'loggedin_userid' => $session->userid,
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages(),
            'baseurl' => $this->getServiceLocator()->get('config')['basepath']['url'],
            'chaturl' => $this->getServiceLocator()->get('config')['chatpath']['url'],
            'verifystatus' => $verifystatus,
            'organizations' => $model->getOrganizations($api_url),
            'location_types' => $model->getLocationTypes($api_url),
            'features' => $subscriptionDetails['features'],
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages(),
            'banners' => $banners,
            'commission' => $data['commission'],
            'chat' => $chat,
            'browser' => $common->getBrowser(),
            'bussCategoryName' => $bussCategoryName
        ));
    }

    public function getCategories() {
        $model = new Practitioners;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $data = $model->getSpserviceData($api_url, 0);

        $selectData = array();
        $selectData = $this->getChild('', 0, $data);

        return $selectData;
    }

    public function getChild($sep, $level, $data) {

        $res = array();
        $model = new Practitioners;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        if ($level > 0 && count($data) > 0) {
            $sep = "|";

            for ($i = 1; $i <= $level; $i++) {
                $sep .= "_";
            }
        }

        foreach ($data as $selectOption) {

            $res[$selectOption['id']] = $sep . ucwords($selectOption['category_name']);
            $newData = $model->getSpserviceData($api_url, $selectOption['id']);

            if (count($newData) > 0) {
                $res = $res + $this->getChild($sep, $level + 1, $newData);
            }
        }
        return $res;
    }

    public function getspservicesAction() {

        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $url = $api_url . "/api/spusers/spservices/";
        $error = false;
        $msg = '';
        $services_list = array();

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $sp_id = $request->getPost('sp_id');

            $sp_id = $request->getPost('sp_id');

            if ($sp_id != '') {

                $list = $this->getservicesdata($sp_id, $api, $api_url, $request->getPost('page'), $request->getPost('items'), $request->getPost('all'));
                $services_list = $list['results'];
            } else {
                $services_list = array();
            }
        } else {
            $model = new Practitioners;
            $session = new Container('frontend');
            $user_details = $model->getSPDetails($api_url, $session->userid);
            if ($user_details['id']) {
                $list = $this->getservicesdata($user_details['id'], $api, $api_url, '', '', true);
                $services_list = $list['results'];
                return json_encode(array('error' => $error, 'msg' => $msg, 'services_list' => $services_list));
                exit;
            } else {
                $services_list = array();
            }
        }

        echo json_encode(array('error' => $error, 'msg' => $msg, 'services_list' => $services_list));
        exit;
    }

    public function getservicesdata($sp_id, $api, $api_url, $page = '', $itemsPerPage = '', $all = '') {
        $url = $api_url . "/api/spusers/spservices/";
        $services_list = array();

        if ($sp_id != '') {

            $data = array('user_id' => $sp_id);
            ($page != "") ? $data['page'] = $page : '';
            ($itemsPerPage != "") ? $data['no_of_records'] = $itemsPerPage : '';
            ($all != '') ? $data['all'] = $all : '';
            //echo "get all value:--:".$all;
            $res = $api->curl($url, $data, "GET");


            if ($res->getStatusCode() == 200) {

                $services = json_decode($res->getBody(), true);
                $noservices = ($all != '') ? count($services) : count($services['results']);
                $servicesDetail = ($all != '') ? $services : $services['results'];
                //echo "number of services:--:".$noservices;

                if ($noservices > 0) {
                    $services_list['count'] = $services['count'];
                    $i = 0;
                    foreach ($servicesDetail as $list) {

                        $service_id = json_decode($list['service'], true);

                        isset($service_id['category_name']) ? $services_list['results'][$i]['name'] = $service_id['category_name'] : $services_list['results'][$i]['name'] = '';
                        isset($service_id['id']) ? $services_list['results'][$i]['category_id'] = $service_id['id'] : $services_list['results'][$i]['category_id'] = '';
                        $services_list['results'][$i]['price'] = $list['price'];
                        $services_list['results'][$i]['duration'] = $list['duration'];
                        $services_list['results'][$i]['status_id'] = $list['status_id'];
                        $services_list['results'][$i]['id'] = $list['id'];
                        $services_list['results'][$i]['parent_category'] = $service_id['parent_category'];
                        $i++;
                    }
                } else {
                    $services_list = array();
                }
            } else {
                $services_list = array();
            }
        } else {
            $services_list = array();
        }

        return $services_list;
    }

    public function deleteserviceAction() {
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $error = false;
        $msg = '';

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $ids = $request->getPost('id');

            if (is_array($ids) && count($ids) > 0) {
                foreach ($ids as $id) {
                    $url = $api_url . "/api/spusers/spservices/" . $id . "/";
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

    public function composeAction() {
        $auth = new FrontEndAuth;
        $getparams = $this->getRequest()->getQuery();
        isset($getparams['user_id']) ? $user_id = $getparams['user_id'] : $user_id = '';
        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        $model = new Practitioners;
        $common = new Common;
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);

        if (!isset($subscriptionDetails['features']) || !is_array($subscriptionDetails['features']) || !in_array(5, $subscriptionDetails['features'])) {
            $this->flashMessenger()->addErrorMessage("Either you have not subscribed any subscription or your subscription don't have permission to access this section..!!");
            return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
        }

        $model_consumer = new Consumers;
        $model_common = $this->getServiceLocator()->get('Application\Model\Common');
        $notifications = $model->getNotifications($api_url);
        $sp_details = $model->getSPDetails($api_url, $session->userid);
        $contacted_list = $model->getCPlist($api_url, false);
        //echo '<pre>';  print_r($contacted_list); exit;
        $form = new SPcomposemessageFrom($contacted_list);
        $form->get('to')->setValue($user_id);
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
                                // send email and sms notification as well
                                $consumer_data = $model_consumer->getConsumerdetails($api_url, $id);

                                if ($consumer_data) {
                                    
                                    $subscriptionDetails = $model_common->getSubscriptiondetails($api_url, $session->userid, true);
                                    $userFeatures = $model_common->getFeatures($api_url, $consumer_data['id']);

                                    if (count($consumer_data['contact']) > 0) {
                                        $contact_data = json_decode($consumer_data['contact'][0], true);
                                    }

                                    $pattern = array('/{{reciever}}/i', '/{{sender}}/i');
                                    $replace = array('<strong>' . $consumer_data['first_name'] . ' ' . $consumer_data['last_name'] . '</strong>', '<strong>' . $session->first_name . ' ' . $session->last_name . '</strong>');

                                    if (isset($consumer_data['email'])) {
                                        if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features']) && ($userFeatures['email'] == 1)) {
                                            $model_common->sendMail($api_url, $consumer_data['email'], '', 16, '', $pattern, $replace, '');
                                        }
                                    }

                                    if (count($contact_data) > 0 && isset($contact_data['cell_phone'])) {
                                        $replace = array($consumer_data['first_name'] . ' ' . $consumer_data['last_name'], $session->first_name . ' ' . $session->last_name);

                                        if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && ($userFeatures['sms'] == 1)) {
                                            $model_common->sendMsg($contact_data['cell_phone'], 6, '', $pattern, $replace);
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
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'form' => $form,
            'notifications' => $notifications,
            'avtar_url' => $sp_details['avtar_url'],
            'first_name' => $sp_details['first_name'],
            'last_name' => $sp_details['last_name'],
            'banners' => $banners
        ));
    }

    public function inboxAction() {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toRoute('login', array('action' => 'index'));
        }
        $session = new Container('frontend');
        $common = new Common;
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $model = new Practitioners;
        $notifications = $model->getNotifications($api_url);
        $sp_details = $model->getSPDetails($api_url, $session->userid);

        $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);

        if (!isset($subscriptionDetails['features']) || !is_array($subscriptionDetails['features']) || !in_array(5, $subscriptionDetails['features'])) {
            $this->flashMessenger()->addErrorMessage("Either you have not subscribed any subscription or your subscription don't have permission to access this section..!!");
            return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
        }

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $page = $request->getPost('page');
            $no_of_records = $request->getPost('no_of_records');

            $model = new Messages;
            $result = $model->getMessages($api_url, $session->userid, $session->user_type_id, $page, "inbox", $no_of_records);

            echo json_encode($result);
            exit;
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'notifications' => $notifications,
            'avtar_url' => $sp_details['avtar_url'],
            'first_name' => $sp_details['first_name'],
            'last_name' => $sp_details['last_name'],
            'banners' => $banners
        ));
    }

    public function sentAction() {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toUrl('/login');
        }
        $session = new Container('frontend');
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $model = new Practitioners;
        $common = new Common;
        $notifications = $model->getNotifications($api_url);
        $sp_details = $model->getSPDetails($api_url, $session->userid);

        $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);

        if (!isset($subscriptionDetails['features']) || !is_array($subscriptionDetails['features']) || !in_array(5, $subscriptionDetails['features'])) {
            $this->flashMessenger()->addErrorMessage("Either you have not subscribed any subscription or your subscription don't have permission to access this section..!!");
            return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
        }

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $page = $request->getPost('page');
            $no_of_records = $request->getPost('no_of_records');

            $model = new Messages;
            $result = $model->getMessages($api_url, $session->userid, $session->user_type_id, $page, "sent", $no_of_records);

            echo json_encode($result);
            exit;
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'notifications' => $notifications,
            'avtar_url' => $sp_details['avtar_url'],
            'first_name' => $sp_details['first_name'],
            'last_name' => $sp_details['last_name'],
            'banners' => $banners
        ));
    }

    public function trashAction() {
        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toUrl('/login');
        }
        $session = new Container('frontend');
        $common = new Common;
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $model = new Practitioners;
        $notifications = $model->getNotifications($api_url);
        $sp_details = $model->getSPDetails($api_url, $session->userid);

        $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);

        if (!isset($subscriptionDetails['features']) || !is_array($subscriptionDetails['features']) || !in_array(5, $subscriptionDetails['features'])) {
            $this->flashMessenger()->addErrorMessage("Either you have not subscribed any subscription or your subscription don't have permission to access this section..!!");
            return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
        }

        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $page = $request->getPost('page');
            $no_of_records = $request->getPost('no_of_records');

            $model = new Messages;
            $result = $model->getMessages($api_url, $session->userid, $session->user_type_id, $page, "trash", $no_of_records);

            echo json_encode($result);
            exit;
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'notifications' => $notifications,
            'avtar_url' => $sp_details['avtar_url'],
            'first_name' => $sp_details['first_name'],
            'last_name' => $sp_details['last_name'],
            'banners' => $banners
        ));
    }

    public function viewmessageAction() {
        $auth = new FrontEndAuth;

        $id = $this->params()->fromRoute('id');

        if ($id == '') {
            die('redirect to practitioner inbox');
        }

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        $model = new Practitioners;
        $common = new Common;
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);

        if (!isset($subscriptionDetails['features']) || !is_array($subscriptionDetails['features']) || !in_array(5, $subscriptionDetails['features'])) {
            $this->flashMessenger()->addErrorMessage("Either you have not subscribed any subscription or your subscription don't have permission to access this section..!!");
            return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
        }

        $url_master = $api_url . "/api/messages/" . $id . "/";
        $res_master = $api->curl($url_master, array(''), "GET");

        if ($res_master->getStatusCode() == 200) {
            $result_master = json_decode($res_master->getBody(), true);
            if ($result_master['readFlag_p'] == 0) {
                $res_master = $api->curl($url_master, array('readFlag_p' => 1), "PUT");
            }
        } else {
            $result_master = array();
        }

        if (isset($result_master['topLevel_id'])) {

            $result_replies = $this->fetchallreplies($api_url, $result_master);
        } else {
            $result_replies = array();
        }

        $model_consumer = new Consumers;
        $model_common = $this->getServiceLocator()->get('Application/Model/Common');
        $notifications = $model->getNotifications($api_url);
        $sp_details = $model->getSPDetails($api_url, $session->userid);

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

                    //$consumer_data = $model_consumer->getConsumerdetails($api_url, $id);
                    $consumer_data = $model_consumer->getConsumerdetails($api_url, $data['to_user_id']);

                    if ($consumer_data) {
                        
                        $subscriptionDetails = $model_common->getSubscriptiondetails($api_url, $session->userid, true);
                        $userFeatures = $model_common->getFeatures($api_url, $consumer_data['id']);

                        if (count($consumer_data['contact']) > 0) {
                            $contact_data = json_decode($consumer_data['contact'][0], true);
                        }

                        $pattern = array('/{{reciever}}/i', '/{{sender}}/i');
                        $replace = array('<strong>' . $consumer_data['first_name'] . ' ' . $consumer_data['last_name'] . '</strong>', '<strong>' . $session->first_name . ' ' . $session->last_name . '</strong>');

                        if (isset($consumer_data['email'])) {
                            if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features']) && ($userFeatures['sms'] == 1)) {
                                $model_common->sendMail($api_url, $consumer_data['email'], '', 16, '', $pattern, $replace, '');
                            }
                        }

                        if (count($contact_data) > 0 && isset($contact_data['cell_phone'])) {
                            $replace = array($consumer_data['first_name'] . ' ' . $consumer_data['last_name'], $session->first_name . ' ' . $session->last_name);
                            if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && ($userFeatures['sms'] == 1)) {
                                $model_common->sendMsg($contact_data['cell_phone'], 6, '', $pattern, $replace);
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
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'master_message' => $result_master,
            'replies' => $result_replies,
            'notifications' => $notifications,
            'current_user_id' => $session->userid,
            'avtar_url' => $sp_details['avtar_url'],
            'first_name' => $sp_details['first_name'],
            'last_name' => $sp_details['last_name'],
            'banners' => $banners
        ));
    }

    public function readmessageAction() {
        $auth = new FrontEndAuth;

        $id = $this->params()->fromRoute('id');

        if ($id == '') {
            die('redirect to practitioner inbox');
        }

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        $model = new Practitioners;
        $common = new Common;
        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);

        if (!isset($subscriptionDetails['features']) || !is_array($subscriptionDetails['features']) || !in_array(5, $subscriptionDetails['features'])) {
            $this->flashMessenger()->addErrorMessage("Either you have not subscribed any subscription or your subscription don't have permission to access this section..!!");
            return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
        }

        $url_master = $api_url . "/api/messages/" . $id . "/";
        $res_master = $api->curl($url_master, array(''), "GET");

        if ($res_master->getStatusCode() == 200) {
            $result_master = json_decode($res_master->getBody(), true);
            if ($result_master['readFlag_p'] == 0) {
                $res_master = $api->curl($url_master, array('readFlag_p' => 1), "PUT");
            }
        } else {
            $result_master = array();
        }

        if (isset($result_master['topLevel_id'])) {

            $result_replies = $this->fetchallreplies($api_url, $result_master);
        } else {
            $result_replies = array();
        }

        $notifications = $model->getNotifications($api_url);
        $sp_details = $model->getSPDetails($api_url, $session->userid);

        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'master_message' => $result_master,
            'replies' => $result_replies,
            'notifications' => $notifications,
            'avtar_url' => $sp_details['avtar_url'],
            'first_name' => $sp_details['first_name'],
            'last_name' => $sp_details['last_name'],
            'banners' => $banners
        ));
    }

    public function fetchallreplies($api_url, $result_master) {
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

    public function actionmsgsAction() {
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
                        $data = array('deleteFlag_p' => '1');
                        $sucess_msg = "Messages moved to trash successfully";
                        $error_msg = "Error!! could not moved to trash";
                    } else if ($msg_action == "untrash") {
                        $data = array('deleteFlag_p' => '0');
                        $sucess_msg = "Messages untrash successfully";
                        $error_msg = "Error!! could not untrash";
                    } else if ($msg_action == "markread") {
                        $data = array('readFlag_p' => '1');
                        $sucess_msg = "Message marked as read";
                        $error_msg = "Error!! cannot mark this message as read";
                    } else if ($msg_action == "markunread") {
                        $data = array('readFlag_p' => '0');
                        $sucess_msg = "Message marked as unread";
                        $error_msg = "Error!! cannot mark this message as unread";
                    } else if ($msg_action == "delete") {
                        $data = array('deleteFlag_p' => '2');
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

            $model = new Practitioners;
            $notifications = $model->getNotifications($api_url);

            echo json_encode(array('error' => $error, 'msg' => $msg, 'notifications' => $notifications));
            exit;
        }

        exit;
    }

    public function settingsAction() {

        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toUrl('/login');
        }

        $session = new Container('frontend');
        $common = new Common;

        $api = new Api();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        $featureData = $common->getFeatures($api_url, $session->userid);

        $subscriptionData = $this->getSpsubscription($session->userid, $api, $api_url);

        $result_newsletter = $common->chkNewsletter($api_url);

        $unsubscribe_reasons = $common->getUnsubscribereason($api_url);

        $model = new Practitioners;
        $notifications = $model->getNotifications($api_url);

        $sp_details = $model->getSPDetails($api_url, $session->userid);

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
            } else if ($action == "auto-renewal") {

                $autorenew = ($request->getPost('auto_renewal') != '') ? $request->getPost('auto_renewal') : '0';

                $subscription_id = ($request->getPost('subscription_id') != '') ? $request->getPost('subscription_id') : '';

                $result = $common->autorenew($api_url, $subscription_id, $autorenew);

                echo json_encode($result);
                exit;
            } else if ($action == "close-acc") {

                $reason_id = ($request->getPost('reason_id') != '') ? $request->getPost('reason_id') : '';
                $other_reason = ($request->getPost('other_reason') != '') ? $request->getPost('other_reason') : '';

                if ($reason_id !== '') {

                    $result = $common->isaccountRemovable($api_url);

                    if (!$result) {
                        $error = true;
                        $msg = "Sorry!! you cannot deactivate your account untill you clear all your pending bookings.";
                    } else {

                        if ($reason_id == 5 && $other_reason == '') {
                            $error = true;
                            $msg = "Please provide other reason in the text area";
                        } else {
                            $result = $common->closeAccount($api_url, $reason_id, $other_reason);
                            if ($result) {
                                // close acc here
                                $msg = "You account has been deactivated successfully..you will be logged out in 5 seconds";
                            } else {
                                // error acc could not be closed
                                $error = true;
                                $msg = "Some Error occured , Could not close your account , please try after some time";
                            }
                        }
                    }
                } else {
                    $error = true;
                    $msg = "Please provide us a reason to close your account";
                }

                echo json_encode(array('error' => $error, 'msg' => $msg));
                exit;
            } else if ($action == "unsubscribe") {

                $subscription_id = ($request->getPost('subscription_id') != '') ? $request->getPost('subscription_id') : '';

                $result = $common->unsubscribeMembership($api_url, $subscription_id);

                if ($result) {
                    $msg = "Unsubscribed Successfully";
                } else {
                    $error = true;
                    $msg = "Error, could not unsubscribe";
                }

                echo json_encode(array('error' => $error, 'msg' => $msg));
                exit;
            } else if ($action == "update_card") {

                $trans = new Transactions;
                echo json_encode($trans->updateCard($this->getServiceLocator()->get('config'), $request->getPost('card_data')));
                exit;
            } else {

                $error = true;
                $msg = "Invalid request";
                echo json_encode(array('error' => $error, 'msg' => $msg));
                exit;
            }
        }

        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'form' => $form,
            'featureData' => $featureData,
            'subscriptionData' => $subscriptionData,
            'notifications' => $notifications,
            'avtar_url' => $sp_details['avtar_url'],
            'first_name' => $sp_details['first_name'],
            'last_name' => $sp_details['last_name'],
            'newsletter_chk' => $result_newsletter,
            'reasonsList' => $unsubscribe_reasons,
            'card_details' => $common->getUserCardDetails($api_url, array('user_id' => $session->userid)),
            'banners' => $banners
        ));
    }

    public function getFeatures($id, $api, $api_url) {
        if ($id != '') {

            $url = $api_url . "/api/userfeaturesetting/";
            $data = array('user_id' => $id);
            $res = $api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {

                $content = json_decode($res->getBody(), true);

                $feature_list = array(
                    'id' => $content[0]['id'],
                    'chat' => $content[0]['chat'],
                    'sms' => $content[0]['sms'],
                    'email' => $content[0]['email'],
                );
            } else {
                $feature_list = array('chat' => '', 'sms' => '', 'email' => '');
            }
        } else {

            $feature_list = array('chat' => '', 'sms' => '', 'email' => '');
        }

        return $feature_list;
    }

    public function getSpsubscription($id, $api, $api_url) {
        if ($id != '') {

            $url = $api_url . "/api/usersubscription/";
            $data = array('user_id' => $id);
            $res = $api->curl($url, $data, "GET");
            $subsData = '';

            if ($res->getStatusCode() == 200) {

                $content = json_decode($res->getBody(), true);
                if (count($content) > 0) {
                    foreach ($content as $con) {
                        if ($con['status_id'] == 1) {
                            $subsData['status'] = "Active";
                            $subsData['id'] = $con['id'];
                            $subsData['end_date'] = $con['subscription_end_date'];
                            $subsData['auto_renewal'] = $con['auto_renewal'];
                            $subs = json_decode($con['subscription_duration'], true);
                            isset($subs['subscription_name']) ? $subsData['name'] = $subs['subscription_name'] : $subsData['name'] = '';
                        } else {
                            $subsData = '';
                        }
                    }
                } else {
                    $subsData = '';
                }
            } else {
                $subsData = '';
            }
        } else {
            $subsData = '';
        }

        return $subsData;
    }

    public function updateAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $action = $request->getPost('action');
            $api = new Api();
            $common = new Common;
            $model = new Practitioners;
            $bookingModel = new Bookings;
            $session = new Container('frontend');
            $user_id = $session->userid;
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            switch ($action) {
                case 'name' :
                    $contact_data['user_id'] = $user_id;
                    $contact_data = $user_data = $errors = array();
                    $contact_id = $request->getPost('contact_id');
                    $full_name = explode(' ', trim($request->getPost('practitioner_name')));

                    (isset($full_name[0]) && $full_name[0] != '') ? $contact_data['first_name'] = $user_data['first_name'] = ucfirst($full_name[0]) : '';
                    (isset($full_name[1]) && $full_name[1] != '') ? $contact_data['last_name'] = $user_data['last_name'] = ucfirst($full_name[1]) : $contact_data['last_name'] = $user_data['last_name'] = '';

                    $result = $model->updateContact($api_url, $contact_data, $contact_id);

                    if ($result == true) {
                        $result = $model->updateData($api_url, $user_data, $user_id);

                        if ($result == true) {
                            echo json_encode(array('status' => 1, 'msg' => 'Name successfully updated..!!'));
                        } else if (is_array($result)) {
                            echo json_encode(array('status' => 0, 'errors' => $result));
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Failed to update name..!!'));
                        }
                    } else if (is_array($result)) {
                        echo json_encode(array('status' => 0, 'errors' => $result));
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'Failed to update name..!!'));
                    }

                    break;

                case 'profile' :

                    $details_data = $address_data = array();
                    $address_data['user_id'] = $details_data['user_id'] = $user_id;
                    $address_data['user_type'] = '';
                    $detail_id = $request->getQuery('detail_id');
                    $address_id = $request->getQuery('address_id');

                    (trim($request->getQuery('designation')) != "") ? $details_data['designation'] = trim($request->getQuery('designation')) : '';
                    (trim($request->getQuery('company_name')) != "") ? $details_data['company_name'] = trim($request->getQuery('company_name')) : '';
                    (trim($request->getQuery('dob')) != "") ? $details_data['dob'] = date('Y-m-d', strtotime(trim($request->getQuery('dob')))) : '';
                    (trim($request->getQuery('specialties')) != "") ? $details_data['specialties'] = trim($request->getQuery('specialties')) : '';
                    (trim($request->getQuery('degrees')) != "") ? $details_data['degrees'] = trim($request->getQuery('degrees')) : '';
                    (trim($request->getQuery('years_of_experience')) != "") ? $details_data['years_of_experience'] = str_replace(' Years', '', trim($request->getQuery('years_of_experience'))) : '';
                    (trim($request->getQuery('prof_membership')) != "") ? $details_data['prof_membership'] = trim($request->getQuery('prof_membership')) : '';
                    (trim($request->getQuery('professional_license_number')) != "") ? $details_data['professional_license_number'] = trim($request->getQuery('professional_license_number')) : '';
                    (trim($request->getQuery('awards_and_publication')) != "") ? $details_data['awards_and_publication'] = trim($request->getQuery('awards_and_publication')) : '';
                    (trim($request->getQuery('auth_to_bill_insurence_copany')) != "") ? $details_data['auth_to_bill_insurence_copany'] = trim($request->getQuery('auth_to_bill_insurence_copany')) : '';
                    (trim($request->getQuery('auth_to_issue_insurence_rem_receipt')) != "") ? $details_data['auth_to_issue_insurence_rem_receipt'] = trim($request->getQuery('auth_to_issue_insurence_rem_receipt')) : '';
                    (trim($request->getQuery('treatment_for_physically_disabled_person')) != "") ? $details_data['treatment_for_physically_disabled_person'] = trim($request->getQuery('treatment_for_physically_disabled_person')) : '';
                    (trim($request->getQuery('offering_at_home')) != "") ? $details_data['offering_at_home'] = trim($request->getQuery('offering_at_home')) : '';
                    (trim($request->getQuery('offering_at_work_office')) != "") ? $details_data['offering_at_work_office'] = trim($request->getQuery('offering_at_work_office')) : '';

                    (trim($request->getQuery('age')) != "") ? $user_data['age'] = trim($request->getQuery('age')) : '';
                    (trim($request->getQuery('gender')) != "") ? $user_data['gender'] = trim($request->getQuery('gender')) : '';

                    // Updating languges
                    if ($model->updateLanguages($api_url, $user_id, $request->getQuery('language'))) {
                        // Updating educations
                        if ($model->updateEducations($api_url, $user_id, $request->getQuery('education'))) {
                            // Updating practitioner details
                            $result = $model->updateDetails($api_url, $details_data, $detail_id);

                            if ($result['status'] == 1) {
                                // Updating practitioner address

                                $res = $model->updateData($api_url, $user_data, $user_id);
                                if ($res == true) {
                                    echo json_encode(array('status' => 1, 'msg' => 'Profile updated successfully..!!', 'data' => array('detail_id' => $result['id'], 'address_id' => $response['id'])));
                                } else if (is_array($res)) {
                                    echo json_encode(array('status' => 0, 'errors' => $res));
                                } else {
                                    echo json_encode(array('status' => 0, 'msg' => 'Failed to update profile..!!'));
                                }
                            } else {
                                echo json_encode($result);
                            }
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Failed to update languages..!!'));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'Failed to update languages..!!'));
                    }

                    break;

                case 'about' :

                    $details_data = array();
                    $details_data['user_id'] = $user_id;
                    $detail_id = $request->getQuery('detail_id');
                    (trim($request->getQuery('description')) != "") ? $details_data['description'] = addslashes(trim(str_replace(array("\r", "\n"), '', nl2br($request->getQuery('description'))))) : '';

                    echo json_encode($model->updateDetails($api_url, $details_data, $detail_id));

                    break;

                case 'contact' :

                    $contact_data = array();
                    $contact_data['user_id'] = $user_id;
                    $contact_id = $request->getQuery('contact_id');
                    (trim($request->getQuery('cellphone')) != "") ? $contact_data['cellphone'] = trim($request->getQuery('cellphone')) : '';
                    (trim($request->getQuery('phone_number')) != "") ? $contact_data['phone_number'] = trim($request->getQuery('phone_number')) : '';

                    if (isset($contact_data['cellphone']) && $contact_data['cellphone'] != '' && !preg_match('/^\d{11}$/', $contact_data['cellphone'])) {
                        echo json_encode(array('status' => 0, 'msg' => 'Main phone number should be 11 digit number (1 as a prefix and 10 digit phone number)..!!'));
                    } elseif (isset($contact_data['phone_number']) && $contact_data['phone_number'] != '' && !preg_match('/^\d{11}$/', $contact_data['phone_number'])) {
                        echo json_encode(array('status' => 0, 'msg' => 'Office phone number should be 11 digit number (1 as a prefix and 10 digit phone number)..!!'));
                    } else {
                        echo json_encode($model->updateContact($api_url, $contact_data, $contact_id));
                    }

                    break;

                case 'organization' :
                    echo json_encode($model->updateOrganization($api_url, $user_id, $request->getQuery('organization')));
                    break;

                case 'location_type' :
                    echo json_encode($model->updateLocationTypes($api_url, $user_id, $request->getQuery('location_types')));
                    break;

                case 'image' :
                    $File = $this->params()->fromFiles('image');

                    if ($File['error'] == 0 && $File['size'] > 0) {
                        $S3 = new ImageS3;
                        $data = $S3->uploadFiles($_FILES['image'], "Media", array(), array('Media' => 100, 'Media_thumb' => '138x108\>\!'));
                        if (is_array($data) && count($data) > 0) {
                            $imageData = array();
                            $imageData['user_id'] = $user_id;
                            $imageData['media_url'] = $data['Media'];
                            $imageData['media_type'] = 1;
                            $imageData['media_title'] = trim($request->getPost('media_title'));
                            //$imageData['media_description'] = addslashes(trim($request->getPost('media_description')));
                            $imageData['media_description'] = addslashes(trim($request->getPost('media_title')));
                            $imageData['created_by'] = $user_id;
                            $imageData['updated_date'] = date('Y-m-d h:i:s');
                            $imageData['updated_by'] = $user_id;
                            $imageData['status_id'] = 5;

                            if ($request->getPost('id') != "") {
                                $res = $api->curl($api_url . "/api/media/" . $request->getPost('id') . "/", $imageData, "PUT");
                            } else {
                                $res = $api->curl($api_url . "/api/media/", $imageData, "POST");
                            }
                            if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
                                $image_res = json_decode($res->getBody(), true);
                                echo json_encode(array('status' => 1, 'msg' => 'Image uploaded successfully..!!', 'id' => $image_res['id'], 'title' => $image_res['media_title'], 'media_description' => $image_res['media_description'], 'url' => $data['Media_thumb']));
                            } else {
                                echo json_encode(array('status' => 0, 'msg' => 'Failed to upload image..!!', 'code' => $res));
                            }
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Failed to upload image on server..!!', 'code' => $File));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'Failed to upload image..!!', 'code' => $File));
                    }

                    break;

                case 'avtar' :
                    $id = $request->getPost('image');

                    if ($user_id != "" && $id != "") {
                        $media_res = $api->curl($api_url . "/api/media/" . $id . "/", array(), "GET");
                        if ($media_res->getStatusCode() == 200) {
                            $media = json_decode($media_res->getBody(), true);

                            $file = explode('/', $media['media_url']);
                            $fileName = "./public/uploads/" . end($file);
                            file_put_contents($fileName, fopen($media['media_url'], 'r'));

                            try {
                                $S3 = new ImageS3;
                                $data = $S3->uploadFile($fileName, array('Avtars' => '378x378'));
                            } catch (\Exception $e) {
                                echo json_encode(array('status' => 0, 'msg' => 'Failed to update avtar image..!!'));
                                exit;
                            }
                            $temp = $api->curl($api_url . "/api/spusers/" . $user_id . "/", array(), "GET");
                            $avtar = json_decode($temp->getBody(), true);

                            ($avtar['avtar_url'] != '') ? $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $avtar['avtar_url'])) : '';

                            $res = $api->curl($api_url . "/api/spusers/" . $user_id . "/", array('avtar_url' => $data['Avtars'], 'user_type_id' => 3), "PUT");
                            if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
                                echo json_encode(array('status' => 1, 'msg' => 'Avtar image changed successfully..!!', 'image_url' => $data['Avtars']));
                            } else {
                                echo json_encode(array('status' => 0, 'msg' => 'Failed to update avtar image..!!', 'errors' => json_decode($res->getBody(), true)));
                            }
                        }
                    }
                    break;

                case 'delete_image' :
                    $id = $request->getPost('image');

                    $media_res = $api->curl($api_url . "/api/media/" . $id . "/", array(), "GET");
                    if ($media_res->getStatusCode() == 200) {
                        $media = json_decode($media_res->getBody(), true);
                        $del_res = $api->curl($api_url . "/api/media/" . $id . "/", array(), "DELETE");
                        if ($del_res->getStatusCode() == 204) {
                            $S3 = new ImageS3;
                            ($media['media_url'] != '') ? $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $media['media_url'])) : '';
                            echo json_encode(array('status' => 1, 'msg' => 'Image deleted successfully..!!'));
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Failed to delete image..!!'));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'No image found..!!'));
                    }
                    break;

                case 'delete_video' :
                    $id = $request->getPost('video');

                    $media_res = $api->curl($api_url . "/api/media/" . $id . "/", array(), "GET");
                    if ($media_res->getStatusCode() == 200) {
                        $media = json_decode($media_res->getBody(), true);
                        $del_res = $api->curl($api_url . "/api/media/" . $id . "/", array(), "DELETE");
                        if ($del_res->getStatusCode() == 204) {

                            ($media['media_url'] != '') ? @unlink($media['media_url']) : '';
                            echo json_encode(array('status' => 1, 'msg' => 'Video deleted successfully..!!'));
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Failed to delete video..!!'));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'No video found..!!'));
                    }
                    break;

                case 'delete_avtar' :

                    $details = $model->getSPDetails($api_url, $session->userid);

                    if (isset($details['avtar_url']) && $details['avtar_url'] != '') {
                        $S3 = new ImageS3;
                        $res = $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $details['avtar_url']));

                        if ($model->updateData($api_url, array('avtar_url' => ''), $session->userid)) {
                            echo json_encode(array('status' => 1, 'msg' => 'Avtar image deleted successfully..!!', 'code' => $res));
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Failed to delete avtar image..!!'));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'No image found..!!'));
                    }
                    break;

                case 'video' :
                    $total = count($model->getSPMedia($api_url, $user_id, 2));
                    $subscriptionDetails = $common->getSubscriptiondetails($api_url, '', true);
                    $video_limit = $subscriptionDetails['video_limit']['limit'];

                    if ($total < $video_limit) {
                        $renameUpload = new \Zend\Filter\File\RenameUpload(array('target' => "./public/uploads/", 'randomize' => true, 'use_upload_name' => true));
                        if ($fileDetails = $renameUpload->filter($_FILES['video'])) {
                            $filePath = $fileDetails['tmp_name'];
                            // check video orientation and rotate if needed
                            /* exec("mediainfo ".$fileDetails['tmp_name']." | grep Rotation", $mediaInfo);
                              var_dump($mediaInfo); exit;

                              if (is_array($mediaInfo) && count($mediaInfo)>0) {
                              $tempPath = explode("/", $fileDetails['tmp_name']);
                              $filePath = "./public/uploads/new_".end($tempPath);
                              exec('ffmpeg -i '.$fileDetails['tmp_name'].' -vf "transpose=1" -strict -2 '.$filePath, $output, $response);
                              ($response == '0')?@unlink($fileDetails['tmp_name']):'';
                              } */

                            $videoData = array();
                            $videoData['user_id'] = $user_id;
                            $videoData['media_url'] = $filePath;
                            $videoData['media_title'] = trim($request->getPost('media_title'));
                            //$videoData['media_description'] = addslashes(trim($request->getPost('media_description')));
                            $videoData['media_description'] = addslashes(trim($request->getPost('media_title')));
                            $videoData['created_by'] = $user_id;
                            $videoData['updated_date'] = date('Y-m-d h:i:s');
                            $videoData['updated_by'] = $user_id;
                            $videoData['status_id'] = 5;

                            /* $length = exec("ffmpeg -i /var/www/html/ovessence".str_replace('.','',$fileDetails['tmp_name'])." 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//'");
                              echo var_dump($length); exit; */
                            echo json_encode($model->updateSPMedia($api_url, $videoData, 2, $request->getPost('id')));
                        } else {
                            echo json_encode(array('status' => 0, 'msg' => 'Unable to upload video..!!'));
                        }
                    } else {
                        echo json_encode(array('status' => 0, 'msg' => 'You have already uploaded the maximum number of videos (' . $video_limit . ') allowed by your subscription..!!'));
                    }

                    break;

                case 'workdays' :
                    //echo '<pre>'; print_r($request->getQuery()); exit;

                    $id = $request->getQuery('id');
                    $start_time = $request->getQuery('start_time');
                    $end_time = $request->getQuery('end_time');
                    $lunch_start_time = $request->getQuery('lunch_start_time');
                    $lunch_end_time = $request->getQuery('lunch_end_time');
                    $address_id = $request->getQuery('address_id');
                    $mode = 'add';

                    foreach ($start_time as $key => $value) {
                        $data = array();
                        $mode = (isset($id[$key]) && $id[$key] != '' && $mode == 'add') ? 'edit' : $mode;
                        $data['start_time'] = ($value != '') ? date('H:i:s', strtotime($value)) : '00:00:00';
                        $data['end_time'] = ($end_time[$key] != '') ? date('H:i:s', strtotime($end_time[$key])) : '00:00:00';
                        $data['lunch_start_time'] = ($lunch_start_time[$key] != '') ? date('H:i:s', strtotime($lunch_start_time[$key])) : '00:00:00';
                        $data['lunch_end_time'] = ($lunch_end_time[$key] != '') ? date('H:i:s', strtotime($lunch_end_time[$key])) : '00:00:00';
                        $data['address_id'] = ($address_id[$key] != '') ? $address_id[$key] : null;
                        $data['days_id'] = $key;
                        $data['user_id'] = $user_id;

                        if ($mode == 'edit') {
                            $data['id'] = ($id[$key] == "") ? $id[($key - 1)] + 1 : $id[$key];
                            $wk_res = $api->curl($api_url . "/api/sp_availability/?user_id=" . $user_id, $data, "PUT");
                        } else {
                            $wk_res = $api->curl($api_url . "/api/sp_availability/?user_id=" . $user_id, $data, "POST");
                        }

                        if ($wk_res->getStatusCode() != 200 && $wk_res->getStatusCode() != 201) {
                            echo json_encode(array('status' => 0, 'msg' => 'Failed to update workdays..!!', 'error' => json_decode($wk_res->getBody(), true), 'workdays' => $model->getSPWorkdays($user_id, $api_url)));
                            exit;
                        }
                    }

                    if ($request->getQuery('delay_id') != "") {
                        $delay_res = $api->curl($api_url . "/api/appointment_delay_list/" . $request->getQuery('delay_id') . "/", array('user_id' => $user_id, 'delay_time' => $request->getQuery('appointment_delay')), "PUT");
                    } else {
                        $delay_res = $api->curl($api_url . "/api/appointment_delay_list/", array('user_id' => $user_id, 'delay_time' => $request->getQuery('appointment_delay')), "POST");
                    }

                    if ($delay_res->getStatusCode() != 200 && $delay_res->getStatusCode() != 201) {
                        echo json_encode(array('status' => 0, 'msg' => 'Failed to update appointment delay time..!!', 'workdays' => $model->getSPWorkdays($user_id, $api_url)));
                    } else {
                        echo json_encode(array('status' => 1, 'msg' => 'Workdays updated successfully..!!', 'error' => json_decode($wk_res->getBody(), true), 'workdays' => $model->getSPWorkdays($user_id, $api_url)));
                    }

                    break;

                case 'sp_address' :
                    //echo json_encode(array('status' => 0, 'msg' => 'here')); exit;
                    if ($request->getPost('layout') == 'true') {
                        $view = new viewModel(array('states' => $common->getstatesByCountry($api_url), 'countries' => $common->getCountries($api_url), 'location_types' => $model->getLocationTypes($api_url), 'id' => $request->getPost('id')));
                        $view->setTemplate('application/practitioner/workplaceaddress.phtml');
                        $printData = $this->getServiceLocator()->get('viewrenderer')->render($view);
                        echo json_encode(array('layout' => $printData));
                        exit;
                    }

                    $address_data = array();
                    $address_data['user_type'] = 'sp';
                    $address_data['user_id'] = $user_id;
                    $address_id = $request->getQuery('address_id');

                    (trim($request->getQuery('street1_address')) != "") ? $address_data['street1_address'] = trim($request->getQuery('street1_address')) : '';
                    (trim($request->getQuery('zip_code')) != "") ? $address_data['zip_code'] = trim($request->getQuery('zip_code')) : '';
                    (trim($request->getQuery('city')) != "") ? $address_data['city'] = trim($request->getQuery('city')) : '';
                    (trim($request->getQuery('country_id')) != "") ? $address_data['country_id'] = trim($request->getQuery('country_id')) : '';
                    (trim($request->getQuery('state_id')) != "") ? $address_data['state_id'] = trim($request->getQuery('state_id')) : '';
                    (trim($request->getQuery('location_type_id')) != "") ? $address_data['location_type_id'] = trim($request->getQuery('location_type_id')) : '';

                    echo json_encode($model->updateSPAddress($api_url, $address_data, $address_id, $session->userid));
                    break;

                case 'remove_sp_address' :

                    $address_id = $request->getPost('id');
                    echo json_encode($model->deleteSPAddress($api_url, $address_id, $session->userid));

                    break;

                case 'invite' :

                    //if ($request->getPost('user') != "" && $request->getPost('email') != "") {
                    if ($request->getPost('email') != "") {
                        $common = new Common;
                        if ($template = $common->emailTemplate($api_url, 5)) {

                            $user_details = $model->getSPDetails($api_url, $session->userid);

                            $mail = new Message();
                            $transport = new \Zend\Mail\Transport\Sendmail();
                            $html = new MimePart(stripslashes(preg_replace('/{{user_name}}/i', '<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>', $template['content'])));
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

                    break;
            }
        }
        exit;
    }

    public function getstatesAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $common = new Common;
            $country_id = $request->getPost('country_id');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            $list = $common->getstatesByCountry($api_url, $country_id);
            echo json_encode($list);
        } else {
            return json_encode(array());
        }
        exit;
    }

    public function getReviewscount($id, $api, $api_url) {

        if ($id != '') {

            $url = $api_url . "/api/feedback/";
            $data = array('user_id' => $id);
            $res = $api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {
                $feedback = json_decode($res->getBody(), true);
                return count($feedback);
            } else {
                return "0";
            }
        } else {
            return "0";
        }
    }

    public function addreviewAction() {

        $id = $this->params()->fromRoute('id');
        $request = $this->getRequest();
        $session = new Container('frontend');

        if ($request) {
            ($request->getPost('service_id') != '') ? $s_id = $request->getPost('service_id') : $s_id = '';
        }

        $redirectUrl = "/practitioner/view/" . $id . "/?tab=review&review=1&s_id=" . $s_id;

        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $model = new Practitioners;

        if (!empty($id) && $s_id != '') {

            $services_list = $model->getSpserviceslist($api_url, $id);
            $form = new ReviewForm($services_list);
            $review_model = new Review();

            $form->setInputFilter($review_model->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $api = new Api();
                $data = array();
                $data['users_id'] = $id;
                ($request->getPost('comments') != '') ? $data['comments'] = $request->getPost('comments') : $data['comments'] = '';
                $data['created_date'] = date('Y-m-d h:i:s', time());
                ($request->getPost('created_by') != '') ? $data['created_by'] = $request->getPost('created_by') : $data['created_by'] = '';
                $data['status_id'] = '9';
                ($request->getPost('service_id') != '') ? $data['service_id'] = $request->getPost('service_id') : $data['service_id'] = '';
                ($request->getPost('comment') != '') ? $data['comments'] = $request->getPost('comment') : $data['comments'] = '';
                $data['created_by'] = $session->userid;

                $url = $api_url . "/api/feedback/";
                $res = $api->curl($url, $data, "POST");

                if ($res->getStatusCode() == 201) {

                    $this->flashMessenger()->addSuccessMessage('Your feedback has been submitted ... It will be displayed on frontend as soon as admin will aprove it');
                    return $this->redirect()->toUrl($redirectUrl);
                } else {

                    if ($res->getStatusCode() == 400) {
                        $result = json_decode($res->getBody(), true);
                        if (isset($result['__all__'])) {
                            $this->flashMessenger()->addErrorMessage('You have already submitted feedback for this practitioner for selected service');
                            return $this->redirect()->toUrl($redirectUrl);
                        }
                    }

                    $this->flashMessenger()->addErrorMessage('Error !! Your feedback has not been submitted');
                    return $this->redirect()->toUrl($redirectUrl);
                }
            } else {
                $messages = $form->getMessages();
                if (isset($messages['captcha']['badCaptcha'])) {
                    $this->flashMessenger()->addErrorMessage($messages['captcha']['badCaptcha']);
                } else {
                    $this->flashMessenger()->addErrorMessage($form->getMessages());
                }
                return $this->redirect()->toUrl($redirectUrl);
            }
        } else {
            return $this->redirect()->toUrl($redirectUrl);
        }
    }

    public function getSPfeedback($id, $api, $api_url, $page = '', $itemsPerPage = '') {

        $url_feedback = $api_url . "/api/feedback/";
        $data_feedback = array('user_id' => $id, 'status_id' => '9');
        ($page != "") ? $data_feedback['page'] = $page : '';
        ($itemsPerPage != "") ? $data_feedback['no_of_records'] = $itemsPerPage : '';
        $res_feedback = $api->curl($url_feedback, $data_feedback, "GET");

        if ($res_feedback->getStatusCode() == 200) {

            $feedback = json_decode($res_feedback->getBody(), true);

            if (count($feedback['results']) > 0) {
                $i = 0;
                foreach ($feedback['results'] as $data) {

                    $feedback_list[$i]['first_name'] = $data['first_name'];
                    $feedback_list[$i]['last_name'] = $data['last_name'];
                    $feedback_list[$i]['avtar_url'] = $data['avtar_url'];
                    $feedback_list[$i]['comments'] = $data['comments'];
                    $feedback_list[$i]['status_id'] = $data['status_id'];
                    $feedback_list[$i]['created_date'] = $data['created_date'];
                    $i++;
                }
            } else {
                $feedback_list = array();
            }
        } else {
            $feedback_list = array();
        }

        return $feedback_list;
    }

    public function getSPmedia($id, $api, $api_url, $media_type = 2, $status_id = 9) {

        $url_media = $api_url . "/api/media/";
        $data_media = array('user_id' => $id, 'media_type' => $media_type, 'status_id' => 9);
        $res_media = $api->curl($url_media, $data_media, "GET");

        if ($res_media->getStatusCode() == 200) {

            $content_media = json_decode($res_media->getBody(), true);
            $content_media_approved = $content_media['results'];

            if ($status_id != 9) {
                $media_res = $api->curl($api_url . "/api/media/", array('user_id' => $id, 'media_type' => $media_type, 'status_id' => $status_id), "GET");

                if ($media_res->getStatusCode() == 200) {
                    $content_media = json_decode($media_res->getBody(), true);
                    $content_media_approved = array_merge($content_media_approved, $content_media['results']);
                }
            }

            if (count($content_media_approved) > 0) {
                $i = 0;
                foreach ($content_media_approved as $data) {
                    $tmp = $data;
                    $media_list[$i]['id'] = $tmp['id'];
                    $media_list[$i]['status_id'] = $tmp['status_id'];
                    $media_list[$i]['media_url'] = $tmp['media_url'];
                    $media_list[$i]['media_title'] = $tmp['media_title'];
                    $media_list[$i]['media_description'] = $tmp['media_description'];
                    $i++;
                }
            } else {
                $media_list = array();
            }
        } else {
            $media_list = array();
        }

        return $media_list;
    }

    /* Function to generate maps */

    public function generateMap($api_url, $api, $data, $user_id = '', $zip_code = '') {
        if (is_numeric($user_id)) {
            $res = $api->curl($api_url . '/api/users/' . $user_id . '/', array(), "GET");

            if ($res->getStatusCode() == "200") {
                $details = json_decode($res->getBody(), true);
                $userAddress = json_decode($details['address'][0], true);
            }
        }

        $map = $this->getServiceLocator()->get('GMaps\Service\GoogleMap'); //getting the google map object using service manager
        $lats = $longs = array();

        if (isset($data['address']) && is_array($data['address']) && count($data['address']) > 0) {
            foreach ($data['address'] as $address) {
                $location = $address['city'] . '+' . $address['state_name'] . '+' . $address['zip_code'] . '+' . $address['country_name'];
                $params = array('location' => $location, 'key' => 'Fmjtd%7Cluur2h0r20%2C85%3Do5-9wbld0', 'callback' => 'renderGeocode', 'outFormat' => 'json');

                //$res = $api->curl('http://www.mapquestapi.com/geocoding/v1/address?key=Fmjtd%7Cluur2h0r20%2C85%3Do5-9wbld0&location=' . $location . '&callback=renderGeocode&outFormat=json', array(), "GET");
                $res = $api->curl('https://maps.googleapis.com/maps/api/geocode/json?address=' . $location . '&key=AIzaSyD1xUqk1WLYOYN700v8lOEL0TU8iLfuiuM', array(), "GET");

                //$response = json_decode(str_replace(array('renderGeocode(', ')'), array('', ''), $res->getBody()), true);
                $response = json_decode($res->getBody(), true);

                //$marker_lat = isset($response['results'][0]['locations'][0]['displayLatLng']['lat']) ? $response['results'][0]['locations'][0]['displayLatLng']['lat'] : '';
                $marker_lat = isset($response['results'][0]['geometry']['location']['lat']) ? $response['results'][0]['geometry']['location']['lat'] : '';
                //$marker_lng = isset($response['results'][0]['locations'][0]['displayLatLng']['lng']) ? $response['results'][0]['locations'][0]['displayLatLng']['lng'] : '';
                $marker_lng = isset($response['results'][0]['geometry']['location']['lng']) ? $response['results'][0]['geometry']['location']['lng'] : '';

                //$lats[] = $lat = isset($response['results'][0]['locations'][0]['displayLatLng']['lat']) ? $response['results'][0]['locations'][0]['displayLatLng']['lat'] : '56.130366';
                $lats[] = $lat = isset($response['results'][0]['geometry']['location']['lat']) ? $response['results'][0]['geometry']['location']['lat'] : '56.130366';
                //$longs[] = $lng = isset($response['results'][0]['locations'][0]['displayLatLng']['lng']) ? $response['results'][0]['locations'][0]['displayLatLng']['lng'] : '-106.346771';
                $longs[] = $lng = isset($response['results'][0]['geometry']['location']['lng']) ? $response['results'][0]['geometry']['location']['lng'] : '-106.346771';

                if (isset($zip_code) && !empty($zip_code)) {
                    $res = $api->curl($api_url . '/api/distance/', array('consumer_zip' => $zip_code, 'practitioner_zip' => $address['zip_code']), "GET");
                    if ($res->getStatusCode() == "200") {
                        $distance = json_decode($res->getBody(), true);
                    }
                } else if (isset($userAddress['zip_code']) && !empty($userAddress['zip_code'])) {
                    $res = $api->curl($api_url . '/api/distance/', array('consumer_zip' => $userAddress['zip_code'], 'practitioner_zip' => $address['zip_code']), "GET");
                    if ($res->getStatusCode() == "200") {
                        $distance = json_decode($res->getBody(), true);
                    }
                }

                if (isset($distance[0]['distance'])) {
                    $distances[] = array(
                        'distance' => round($distance[0]['distance']),
                        'address' => $address['city'] . ', ' . $address['state_name'] . ', ' . $address['zip_code'] . ', ' . $address['country_name']
                    );
                }

                $markers[$address['city']] = $marker_lat . ',' . $marker_lng;  //markers location with latitude and longitude
            }
        }

        $config = array(
            'sensor' => 'true', //true or false
            'div_id' => 'mapData', //div id of the google map
            'div_class' => 'grid_6', //div class of the google map
            'zoom' => 5, //zoom level
            'width' => "600px", //width of the div
            'height' => "300px", //height of the div
            'lat' => $lat, //lattitude
            'lon' => $lng, //longitude 
            'animation' => 'none', //animation of the marker
        );

        if ($marker_lat != '' && $marker_lng != '') {
            $config['markers'] = $markers;
        }

        $map->initialize($config);                                         //loading the config   
        $html = $map->generate();                                          //genrating the html map content 

        return array('map' => $html, 'distance' => $distances);
    }

    public function bookingsCalenderAction() {
        $bookingModel = new Bookings;
        $model = new Practitioners;
        isset($_REQUEST['sp_id']) ? $sp_id = $_REQUEST['sp_id'] : $sp_id = '';

        if ($sp_id != '') {

            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $content = $bookingModel->getBookings($api_url, $sp_id);
            $delay_time = $model->getAppointmentDelay($sp_id, $api_url);

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
                        $events[] = array('title' => '', 'start' => date('D F d Y H:i:s', strtotime($booking['booking_status']['booking_time'])), 'end' => date('D F d Y H:i:s', (strtotime($booking['booking_status']['booking_time']) + ($booking['duration'] * 60))), 'allDay' => false, 'backgroundColor' => $color, 'url' => '', 'id' => $booking['id'], 'user_id' => $booking['user_id'], 'duration' => $booking['duration']);
                        //$events[] = array('title' => $booking['consumer_first_name'] . ' ' . $booking['consumer_last_name'] . ' ' . $booking['category_name'], 'start' => '2014-08-29T10:30:00', 'end' => '2014-08-29T12:30:00', 'allDay' => false, 'backgroundColor' => $color, 'url' => '', 'id' => $booking['user_id']);
                    }
                }
                //print_r($events); exit;
                echo json_encode($events);
                exit;
            }
        }
        exit;
    }

    public function bookeddaysAction() {
        $model = new Practitioners;
        $bookingModel = new Bookings;
        $request = $this->getRequest();

        if ($request->isPost() && $request->getPost('user') != null) {
            $api = new Api();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $user_id = $request->getPost('user');
            $service_duration = $request->getPost('service_duration', 15);
            $address_id = $request->getPost('address_id');
            $data = array('bookedDates' => array());
            // Fetching service providers bookings
            $bookings = $bookingModel->getBookings($api_url, $user_id);

            if (isset($bookings['results']) && count($bookings['results']) > 0) {
                foreach ($bookings['results'] as $booking) {
                    $availableSlots = $bookingModel->getAvailableSlots($api_url, $user_id, date('Y-m-d', strtotime($booking['booked_date'])), $service_duration);
                    if (count($availableSlots) == 0 && !in_array(date('Y-m-d', strtotime($booking['booked_date'])), $data['bookedDates'])) {
                        $data['bookedDates'][] = date('Y-m-d', strtotime($booking['booked_date']));
                    }
                }
            }

            $data['workdays'] = explode(', ', $model->getSPWorkdays($user_id, $api_url, $address_id));

            echo json_encode($data);
        }
        exit;
    }

    public function servicedurationAction() {
        $request = $this->getRequest();

        ($request->getPost('id') != '') ? $user_id = $request->getPost('id') : $user_id = '';
        ($request->getPost('service_id') != '') ? $service_id = $request->getPost('service_id') : $service_id = '';

        if (!empty($user_id) && $user_id != null && $service_id != '') {
            $model = new Practitioners();
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            echo json_encode($model->getServiceDurations($api_url, $service_id, $user_id));
        } else {
            echo json_encode(array('status' => 0, 'msg' => 'Unable to get service duration for the selected service..!!'));
        }
        exit;
    }

    public function getservicepriceAction() {
        $model = new Practitioners;
        $request = $this->getRequest();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        ($request->getPost('id') != '') ? $id = $request->getPost('id') : $id = '';
        if (!empty($id) && $id != '') {
            // get currency
            $bookingsession = new Container('bookingsession');
            $bookingsession->currency = $currency = $model->getcurrency($api_url, $this->getRequest()->getServer('REMOTE_ADDR'));
            echo json_encode($model->getServicePrice($api_url, $id) . ' $ ' . $currency);
        }
        exit;
    }

    public function sendnewsletterAction() {
        $request = $this->getRequest();
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        if ($request->isPost()) {
            $model = new Practitioners;
            if ($request->getPost('users') != null && $request->getPost('nid') != null) {
                echo json_encode($model->sendNewsletter($api_url, $request->getPost('nid'), $request->getPost('users')));
            } else {
                echo json_encode(array('status' => 0, 'msg' => 'Unable to send newsletter..!!'));
            }
            exit;
        }

        $id = $this->params()->fromRoute('id');

        if (!isset($id) || $id == "") {
            return $this->redirect()->toRoute('practitioner', array('action' => 'dashboard'));
        }

        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toRoute('login', array('action' => 'index'));
        }

        $session = new Container('frontend');
        $common = new Common;
        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);
        return new ViewModel(array('user_id' => $session->userid, 'id' => $id, 'banners' => $banners));
    }

    public function spconsumersAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $model = new Practitioners;
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $consumers = $model->getServiceConsumers($api_url, $request->getPost('id'), $request->getPost('page'), $request->getPost('records'), false, true);

            echo json_encode($consumers);
        }

        exit;
    }

    public function referralsAction() {
        $api = new Api();
        $common = new Common;
        $model = new Practitioners;
        $session = new Container('frontend');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

        /* Ajax code starts here */
        $request = $this->getRequest();
        if ($request->isPost()) {

            switch ($request->getPost('action')) {
                case 'get' :

                    //echo json_encode($model->getSPreferrals($api_url, $request->getPost('user'), $request->getPost('referred_by')));
                    echo json_encode($model->getSPreferrals($api_url, $request->getPost('user'), $request->getPost('referred_by'), $request->getPost('page'), $request->getPost('items')));
                    break;

                case 'update' :
                    echo json_encode($model->addReference($api_url, $request->getPost(), $session->userid));
                    break;

                case 'delete' :
                    echo json_encode($model->deleteReference($api_url, $request->getPost('ids')));
                    break;
            }

            exit;
        }
        /* Ajax code ends here */

        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toRoute('login', array('action' => 'index'));
        }

        $res = $api->curl($api_url . "/api/spusers/" . $session->userid . "/", array(''), "GET");

        if ($res->getStatusCode() != 200) {
            return $this->redirect()->toRoute('practitioner', array('action' => 'list'));
        }

        $data = $this->getSpdata($res);

        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'user_id' => $session->userid,
            'content' => $data['content'],
            'notifications' => $model->getNotifications($api_url),
            'total_referred_from' => count($model->getSPreferrals($api_url, $session->userid)),
            'total_referred_to' => count($model->getSPreferrals($api_url, '', $session->userid)),
            'services' => $model->getSpserviceData($api_url),
            'practitioners' => $model->getSPlist($api_url, $session->userid),
            'banners' => $banners
        ));
    }

    public function transactionsAction() {
        $api = new Api();
        $trans = new Transactions;
        $model = new Practitioners;
        $common = new Common;
        $bookingModel = new Bookings;
        $session = new Container('frontend');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $request = $this->getRequest();

        if ($request->isPost()) {

            $page = $request->getPost('page');
            $recordsPerPage = $request->getPost('items');

            switch ($request->getPost('action')) {
                case 'subscriptions' :
                    $subscriptions = $bookingModel->getBookings($api_url, '', $session->userid, '', $page, $recordsPerPage, '', '', 'subscription');
                    echo json_encode($subscriptions['results']);
                    break;

                case 'bookings' :
                    $bookings = $bookingModel->getBookings($api_url, $session->userid, '', '', $page, $recordsPerPage);
                    echo json_encode($bookings['results']);
                    break;
            }

            exit;
        }

        $auth = new FrontEndAuth;

        if (!$auth->hasIdentity(3)) {
            return $this->redirect()->toRoute('login', array('action' => 'index'));
        }

        $res = $api->curl($api_url . "/api/spusers/" . $session->userid . "/", array(''), "GET");

        if ($res->getStatusCode() != 200) {
            return $this->redirect()->toRoute('practitioner', array('action' => 'list'));
        }

        $data = $this->getSpdata($res);

        // getting banner for this page
        $banners = $common->getBanner($api_url, 4);

        return new ViewModel(array(
            'user_id' => $session->userid,
            'content' => $data['content'],
            'notifications' => $model->getNotifications($api_url),
            'booking_total' => $bookingModel->getBookings($api_url, $session->userid)['count'],
            'subs_total' => $bookingModel->getBookings($api_url, '', $session->userid, '', '', '', '', '', 'subscription')['count'],
            'banners' => $banners
        ));
    }

    // Send a business card on mail
    public function mailbusinesscardAction() {
        $api = new Api();
        $model = new Practitioners;
        $common = new Common;
        $session = new Container('frontend');
        $request = $this->getRequest();
        //echo "get value:-:".$request->getPost('imageUrl'); die;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $imgname = ($request->getPost('imageUrl') == 'verso') ? 'verso' : 'recto';
        $url = rtrim($this->getServiceLocator()->get('config')['basepath']['url'], '/');
        $logo = $url . '/img/business-logo.png';
        $user_details = $model->getSPDetails($api_url, $session->userid);
        $profileurl = $this->getServiceLocator()->get('config')['basepath']['url'] . 'practitioner/view/' . $user_details['id'];

        /* commented by pi20jan
          if ($imgname == 'recto') {
          $address = $cellphone = 'Not Available';

          // getting address
          if (isset($user_details['work_address']) && is_array($user_details['work_address']) && count($user_details['work_address']) > 0) {
          $mainAddress = json_decode($user_details['work_address'][0], true);
          $address = $mainAddress['city'].', '.$mainAddress['state_name'];
          }

          // getting contact details
          if (isset($user_details['contact']) && is_array($user_details['contact']) && count($user_details['contact']) > 0) {
          $contactDetails = json_decode($user_details['contact'][0], true);
          $cellphone = $contactDetails['cellphone'];
          }

          $services_count = $this->getservicesdata($session->userid, $api, $api_url); // get all services list
          $bussCategoryName = isset($services_count['results'][0]['parent_category'])?$services_count['results'][0]['parent_category']:'Not Available';

          $patterns = array('/{{id}}/i', '/{{card_logo}}/i', '/{{main_category}}/i', '/{{user_name}}/i', '/{{url}}/i', '/{{cellphone}}/i', '/{{address}}/i', '/{{profile_url}}/i');
          $replacements = array($session->userid, $logo, $bussCategoryName, $user_details['first_name'] . ' ' . $user_details['last_name'], $url, $cellphone, $address, $profileurl);

          //Get tamplate
          $template = $common->emailTemplate($api_url, 25);

          } else {

          // Get services
          $getservices = $request->getPost('servicename');

          if (count($services['services_list']) > 0) {
          $getservices = array();
          foreach ($services['services_list'] as $name => $value) {
          array_push($getservices, $value['name']);
          }
          $getservices = implode(',', $getservices);
          }

          //$logo = $this->getServiceLocator()->get('config')['basepath']['url'].'img/business-logo.png';
          $bgimage = "background:url('".$url."/img/bg_" . $imgname . ".jpg') no-repeat center center;";

          $patterns = array('/{{card_logo}}/i', '/{{user_id}}/i', '/{{user_name}}/i', '/{{service_name}}/i', '/{{url_path}}/i', '/{{back_ground}}/i');
          $replacements = array($logo, '<strong>' . $user_details['id'] . '</strong>', '<strong>' . $user_details['first_name'] . ' ' . $user_details['last_name'] . '</strong>', '<strong>' . $getservices . '</strong>', $loginurl, $bgimage);
          //$replacements = array($logo,$request->getPost('url'),'<strong>'.$user_details['first_name'].' '.$user_details['last_name'].'</strong>','<strong>'.$getservices.'</strong>',$loginurl);
          //Get tamplate
          $template = $common->emailTemplate($api_url, 24);
          }
         */

        $address = $cellphone = 'Not Available';

        // getting address
        if (isset($user_details['work_address']) && is_array($user_details['work_address']) && count($user_details['work_address']) > 0) {
            $mainAddress = json_decode($user_details['work_address'][0], true);
            $address = $mainAddress['city'] . ', ' . $mainAddress['state_name'];
        }

        // getting contact details
        if (isset($user_details['contact']) && is_array($user_details['contact']) && count($user_details['contact']) > 0) {
            $contactDetails = json_decode($user_details['contact'][0], true);
            $cellphone = $contactDetails['cellphone'];
        }

        $services_count = $this->getservicesdata($session->userid, $api, $api_url); // get all services list
        $parentService = isset($services_count['results'][0]['category_id']) ? $model->getParentService($api_url, $services_count['results'][0]['category_id']) : 'Not Available';
        $bussCategoryName = ($parentService) ? $parentService['category_name'] : 'Not Avaialable';
        //$bussCategoryName = isset($services_count['results'][0]['parent_category'])?$services_count['results'][0]['parent_category']:'Not Available';
        $back_ground = ($imgname == 'recto') ? '' : 'background: url(' . $url . '/img/bg_verso.jpg) no-repeat scroll center center transparent;';

        $patterns = array('/{{id}}/i', '/{{card_logo}}/i', '/{{main_category}}/i', '/{{user_name}}/i', '/{{url}}/i', '/{{cellphone}}/i', '/{{address}}/i', '/{{profile_url}}/i', '/{{back_ground}}/i');
        $replacements = array($session->userid, $logo, $bussCategoryName, $user_details['first_name'] . ' ' . $user_details['last_name'], $url, $cellphone, $address, $profileurl, $back_ground);
        $template = ($imgname == 'recto') ? $common->emailTemplate($api_url, 25) : $common->emailTemplate($api_url, 24);

        $mail = new Message();
        $transport = new \Zend\Mail\Transport\Sendmail();
        $html = new MimePart(stripslashes(preg_replace($patterns, $replacements, stripslashes($template['content']))));

        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $mail->setBody($body)
                ->setFrom($template['fromEmail'], 'Ovessence')
                ->addTo($request->getPost('email'), '')
                ->setSubject($template['subject']);
        $transport->send($mail);
        echo json_encode(array('status' => 1, 'msg' => 'Business card sent to the email address..!!'));
        exit;
    }

    //Save business card  pdf
    public function savepdfbusinesscardAction() {

        $api = new Api();
        $model = new Practitioners;
        $common = new Common;
        $request = $this->getRequest();
        $session = new Container('frontend');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $imgname = ($request->getQuery('imgurl') == 'verso') ? 'verso' : 'recto';
        $url = './public';
        $logo = $url . '/img/business-logo.png';
        $profileurl = $this->getServiceLocator()->get('config')['basepath']['url'] . 'practitioner/view/' . $user_details['id'];
        $user_details = $model->getSPDetails($api_url, $session->userid);

        $address = $cellphone = 'Not Available';

        // getting address
        if (isset($user_details['work_address']) && is_array($user_details['work_address']) && count($user_details['work_address']) > 0) {
            $mainAddress = json_decode($user_details['work_address'][0], true);
            $address = $mainAddress['city'] . ', ' . $mainAddress['state_name'];
        }

        // getting contact details
        if (isset($user_details['contact']) && is_array($user_details['contact']) && count($user_details['contact']) > 0) {
            $contactDetails = json_decode($user_details['contact'][0], true);
            $cellphone = $contactDetails['cellphone'];
        }

        $services_count = $this->getservicesdata($session->userid, $api, $api_url); // get all services list
        $parentService = isset($services_count['results'][0]['category_id']) ? $model->getParentService($api_url, $services_count['results'][0]['category_id']) : 'Not Available';
        $bussCategoryName = ($parentService) ? $parentService['category_name'] : 'Not Avaialable';
        //$bussCategoryName = isset($services_count['results'][0]['parent_category'])?$services_count['results'][0]['parent_category']:'Not Available';
        $back_ground = ($imgname == 'recto') ? '' : 'background: url(' . $url . '/img/bg_verso.jpg) no-repeat scroll center center transparent;';

        $patterns = array('/{{id}}/i', '/{{card_logo}}/i', '/{{main_category}}/i', '/{{user_name}}/i', '/{{url}}/i', '/{{cellphone}}/i', '/{{address}}/i', '/{{profile_url}}/i', '/{{back_ground}}/i');
        $replacements = array($session->userid, $logo, $bussCategoryName, $user_details['first_name'] . ' ' . $user_details['last_name'], $url, $cellphone, $address, $profileurl, $back_ground);
        $template = ($imgname == 'recto') ? $common->emailTemplate($api_url, 25) : $common->emailTemplate($api_url, 24);

        $newtemp = preg_replace($patterns, $replacements, stripslashes($template['content']));

        // Store in PDF format
        $dompdf = new \DOMPDF();
        $dompdf->load_html($newtemp);
        $dompdf->render();
        //$dompdf->Output('businesscard.pdf');
        $dompdf->stream('businesscard.pdf', array('Attachment' => 0));

        die;
    }

    public function getslotsAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $bookingModel = new Bookings;
            $session = new Container('frontend');
            $service_duration = $request->getPost('duration', 0);
            $user = $request->getPost('user_id', $session->userid);
            $date = $request->getPost('selectedDate', date('Y-m-d'));
            $address = $request->getPost('address_id');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            $availableSlots = $bookingModel->getAvailableSlots($api_url, $user, date('Y-m-d', strtotime($date)), $service_duration, $address);
            $slots = array();

            foreach ($availableSlots as $slot) {
                $slots[] = array('start' => date('H:i', strtotime($slot['start'])), 'end' => date('H:i', strtotime($slot['end'])));
            }

            echo json_encode($slots);
        }

        exit;
    }
    
    // Send mail, sms to practitioner when customer contacts him
    
    public function contactPractitionerAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $common = $this->getServiceLocator()->get('Application\Model\Common');
            $session = new Container('frontend');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            if ($template = $common->emailTemplate($api_url, 13)) {
                $messageRes = $common->sendMessage($api_url, $request->getPost('id'), $session->userid, '', $session->user_name, $template['subject'], $request->getPost('content'));

                if ($messageRes->getStatusCode() == 201 || $messageRes->getStatusCode() == 200) {
                    $messageData = json_decode($messageRes->getBody(), true);
                    $messageUpdateRes = $common->sendMessage($api_url, $request->getPost('id'), $session->userid, $messageData['id'], $session->user_name, $template['subject'], $request->getPost('content'), $messageData['id'], $messageData['id']);

                    if ($messageUpdateRes->getStatusCode() == 201 || $messageUpdateRes->getStatusCode() == 200) {
                        $msg = "Message sent sucessfully";
                        $error = false;
                        $userFeatures = $common->getFeatures($api_url, $request->getPost('id'));
                        $subscriptionDetails = $common->getSubscriptiondetails($api_url, $request->getPost('id'), true);
                        
                        // send email
                        if ($request->getPost('emailId') != '' && $userFeatures['email'] == 1 && isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features'])) {
                            $patterns = array('/{{consumer_name}}/i', '/{{message}}/i');
                            $replacements = array('<strong>' . $session->first_name . ' ' . $session->last_name . '</strong>', $request->getPost('content'));
                            $common->sendMail($api_url, $request->getPost('emailId'), $template['fromEmail'], '', array('subject' => $template['subject'], 'message' => $request->getPost('content')), $patterns, $replacements);
                        }
                        
                        // send msg
                        if ($request->getPost('number') != '' && $userFeatures['sms'] == 1 && isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features'])) {
                            $common->sendMsg($request->getPost('number'), 2, '',array('/{{user_name}}/i'), array('Practitioner'));
                        }
                    } else {
                        $msg = "Sorry not able to send message..!!";
                        $error = true;
                    }
                } else {
                    $msg = "Sorry not able to send message..!!";
                    $error = true;
                }
            } else {
                $msg = "Sorry mail tempalte not found..!!";
                $error = true;
            }
        }
        echo json_encode(array('error' => $error, 'msg' => $msg));
        exit;
    }

    public function feedbacksAction() {
        $request = $this->getRequest();
        $model = new Practitioners;
        $session = new Container('frontend');
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $feedbacks = $model->getSPfeedback($session->userid, $api_url, $request->getPost('page'), $request->getPost('items'));
        //var_dump($feedbacks); exit;
        echo json_encode($feedbacks);
        exit;
    }

    public function newslettersAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $model = new Practitioners;
            $session = new Container('frontend');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            if (trim($request->getPost('subject')) != "" && trim($request->getPost('message')) != "") {

                echo json_encode($model->addUpdateNewsletter($api_url, $session->userid, $request->getPost('id'), trim($request->getPost('subject')), trim($request->getPost('message'))));
            } else if (trim($request->getPost('status')) != "" && count($request->getPost('ids')) > 0) {
                $status = $request->getPost('status', 1);
                $ids = $request->getPost('ids');

                echo json_encode($model->changeNewsletterStatus($api_url, $ids, $status));
            } else if ($request->getPost('delete_request') == '1' && count($request->getPost('ids')) > 0) {

                $ids = $request->getPost('ids');
                echo json_encode($model->deleteNewsletter($api_url, $ids));
            } else {
                $newsletters = $model->getSPnewsletter($session->userid, $api_url, $request->getPost('page'), $request->getPost('items'), $request->getPost('newsletter_id'));
                echo json_encode($newsletters);
            }
        }
        exit;
    }

    public function servicesAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $model = new Practitioners;
            $session = new Container('frontend');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $action = $request->getPost('action');

            if ($action == 'change_status') {
                $ids = ($request->getPost('ids') != '') ? $request->getPost('ids') : '';
                $status_id = ($request->getPost('status_id') != '') ? $request->getPost('status_id') : '1';
                echo json_encode($model->changeServiceStatus($api_url, $session->userid, array_filter($ids), $status_id));
                exit;
            } else {
                ($request->getPost('service_id') != '') ? $service_id = $request->getPost('service_id') : $service_id = '';
                ($request->getPost('duration') != '') ? $duration = $request->getPost('duration') : $duration = '';
                ($request->getPost('price') != '') ? $price = $request->getPost('price') : $price = '';
                ($request->getPost('sp_edit_id') != '') ? $id = $request->getPost('sp_edit_id') : $id = '';
                $sp_edit_id = $request->getPost('sp_edit_id');
                $error = false;

                if (is_numeric($duration) && is_numeric($price)) {
                    echo json_encode($model->updateServices($api_url, $session->userid, $service_id, $duration, $price, $action, $sp_edit_id));
                } else {
                    echo json_encode(array('status' => 0, 'msg' => 'Duration and price must be in numeric form..!!'));
                }
            }
        }

        exit;
    }

    public function listBYDateTimeAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $request = $this->getRequest();
            $post = $request->getPost();
            $model = new Practitioners;
            $remoteAddr = $this->getRequest()->getServer('REMOTE_ADDR');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $model->userlistByFilterData($post, $api_url, $remoteAddr);
        }
        exit;
    }

    /* Function to get address suggestions */

    public function getaddressAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $model = new Practitioners;
            $filter = array('param' => $request->getPost('word'));
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $fields = ($request->getPost('fields') != "") ? $request->getPost('fields') : array('city_name', 'county_name', 'region_name', 'zip_code', 'country_name');
            $results = $model->getAddress($api_url, $filter, $fields);
            echo json_encode(array('status' => 1, 'results' => $results));
        }

        exit;
    }

    /* Function to get service providers suggestions */

    public function getserviceprovidersAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $model = new Practitioners;
            $filter = array('practitioners_name' => $request->getPost('word'), 'status_id' => 9);
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            $results = $model->getServiceProviders($api_url, $filter);
            echo json_encode(array('status' => 1, 'results' => $results));
        }

        exit;
    }

    /* Function to read notifications */

    public function readnotificationsAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $model = new Practitioners;
            $session = new Container('frontend');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];

            if ($model->readNotifications($api_url, $request->getPost('tab'), $session->userid)) {
                echo json_encode(array('status' => 1, 'notifications' => $model->getNotifications($api_url)));
            } else {
                echo json_encode(array('status' => 0, 'notifications' => $model->getNotifications($api_url)));
            }
        }
        exit;
    }

}
