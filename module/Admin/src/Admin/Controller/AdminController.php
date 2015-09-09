<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SmsHistory;
use Zend\Session\Container;

class AdminController extends AbstractActionController
{

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $module = $request->getPost('module');
            $tab = $request->getPost('tab');

            switch ($module) {
                case 'subscriptions' :
                    $data = $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable')->getSubscriptionByStates($tab);
                    break;

                case 'bookings' :
                    $data = $this->getServiceLocator()->get('Admin\Model\BookingsTable')->getBookingsByStates($tab);
                    break;

                case 'revenue' :
                    $data = $this->getServiceLocator()->get('Admin\Model\RevenuesTable')->getRevenueStats($tab);
                    break;

                case 'message' :
                    $data = $this->getServiceLocator()->get('Admin\Model\SmsHistoryTable')->getSmsByDays($tab);
                    break;

                case 'registration' :
                    $data = $this->getRegistrationStats($tab);
                    break;

                case 'video' :
                    $data = $this->getVideoStats($tab);
                    break;

                case 'PRtop10' :
                    $data = $this->UserStatsByCities('3', '9', $tab);
                    break;

                case 'CRtop10' :
                    $data = $this->UserStatsByCities('4', '9', $tab);
                    break;

                case 'CPCRtop10' :
                    $data = $this->CancStats($tab);
                    break;
            }

            echo json_encode($data);
            exit;
        }

        $latest_orders_count = $this->getServiceLocator()->get('Admin\Model\RevenuesTable')->CountNewOrders();

        $latest_feedback_count = $this->getServiceLocator()->get('Admin\Model\FeedbacksTable')->CountNewFeedbacks();

        $latest_users = $this->getServiceLocator()->get('Admin\Model\UsersTable')->GetLatestUsers();

        /* Revenue data starts */
        /* $revenueStats['day'] = $this->getServiceLocator()->get('Admin\Model\RevenuesTable')->getRevenueStats('day');
          $revenueStats['week'] = $this->getServiceLocator()->get('Admin\Model\RevenuesTable')->getRevenueStats('week');
          $revenueStats['month'] = $this->getServiceLocator()->get('Admin\Model\RevenuesTable')->getRevenueStats('month');
          $revenueStats['year'] = $this->getServiceLocator()->get('Admin\Model\RevenuesTable')->getRevenueStats('year'); */
        /* Revenue data ends */

        /* Subscription data starts */
        /* $subscriptions['day'] = $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable')->getSubscriptionByStates('day');
          $subscriptions['week'] = $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable')->getSubscriptionByStates('week');
          $subscriptions['month'] = $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable')->getSubscriptionByStates('month');
          $subscriptions['year'] = $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable')->getSubscriptionByStates('year'); */
        /* Subscription data ends */

        /* Booking data starts */
        /* $bookings['day'] = $this->getServiceLocator()->get('Admin\Model\BookingsTable')->getBookingsByStates('day');
          $bookings['week'] = $this->getServiceLocator()->get('Admin\Model\BookingsTable')->getBookingsByStates('week');
          $bookings['month'] = $this->getServiceLocator()->get('Admin\Model\BookingsTable')->getBookingsByStates('month');
          $bookings['year'] = $this->getServiceLocator()->get('Admin\Model\BookingsTable')->getBookingsByStates('year'); */
        /* Booking data ends */

        $sent_sms = $this->getSmsStats();

        $registration_stats = $this->getRegistrationStats();

        $PracStatsByCities = $this->UserStatsByCities('3', '9'); //Practitioner stats by top 10 cities

        $ConsStatsByCities = $this->UserStatsByCities('4', '9'); //Consumer stats by top 10 cities

        $userCancStats = $this->CancStats(); //Practitioner and consumer cancellation stats

        $video_stats = $this->getVideoStats(); //Video views and uploaded stats 

        $latest_users = $this->getServiceLocator()->get('Admin\Model\UsersTable')->GetLatestUsers(); //Latest users stats 

        $pending_profiles = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getPendingProfiles(); //Tasks Pending profiles to approve

        $pending_feedback = $this->getServiceLocator()->get('Admin\Model\FeedbacksTable')->getPendingFeedback(); //Tasks Pending Feedback to approve

        $pending_media = $this->getServiceLocator()->get('Admin\Model\MediaTable')->getPendingVideo(); //Tasks Pending Feedback to approve

        $tasks = $pending_profiles['profile_count'] + $pending_media['video_count'] + $pending_feedback['pending_feedback_count'];  // total tasks
        $total_unread = $this->getServiceLocator()->get('Admin\Model\MessagesTable')->getUnreadMessages(1)->count(); // total unread messages

        $i = 0;
        $j = 0;

        if (count($latest_users) > 0 && $latest_users != false) {
            foreach ($latest_users as $users) {

                if ($users->user_type_id == "3") {

                    $practiotioner[$i]['user_type'] = $users->user_type;
                    $practiotioner[$i]['user_name'] = $users->user_name;
                    $practiotioner[$i]['user_type_id'] = $users->user_type_id;
                    $practiotioner[$i]['name'] = $users->first_name . " " . $users->last_name;
                    if ($users->status_id == "1") {
                        $practiotioner[$i]['status'] = 'Active';
                    } else if ($users->status_id == "2") {
                        $practiotioner[$i]['status'] = 'Inactive';
                    } else if ($users->status_id == "3") {
                        $practiotioner[$i]['status'] = 'Suspended';
                    } else if ($users->status_id == "5") {
                        $practiotioner[$i]['status'] = 'Pending Approval';
                    } else if ($users->status_id == "9") {
                        $practiotioner[$i]['status'] = 'Approved';
                    } else if ($users->status_id == "10") {
                        $practiotioner[$i]['status'] = 'Disapproved';
                    } else {
                        $practiotioner[$i]['status'] = 'Suspended';
                    }
                    $practiotioner[$i]['created_date'] = $users->created_date;
                    $i++;
                } else if ($users->user_type_id == "4") {

                    $consumer[$j]['user_type'] = $users->user_type;
                    $consumer[$j]['user_name'] = $users->user_name;
                    $consumer[$j]['user_type_id'] = $users->user_type_id;
                    $consumer[$j]['name'] = $users->first_name . " " . $users->last_name;
                    if ($users->status_id == "1") {
                        $consumer[$j]['status'] = 'Active';
                    } else if ($users->status_id == "2") {
                        $consumer[$j]['status'] = 'Inactive';
                    } else if ($users->status_id == "3") {
                        $consumer[$j]['status'] = 'Suspended';
                    } else if ($users->status_id == "5") {
                        $consumer[$j]['status'] = 'Pending Approval';
                    } else if ($users->status_id == "9") {
                        $consumer[$j]['status'] = 'Approved';
                    } else if ($users->status_id == "10") {
                        $consumer[$j]['status'] = 'Disapproved';
                    } else {
                        $consumer[$j]['status'] = 'Suspended';
                    }
                    $consumer[$j]['created_date'] = $users->created_date;
                    $j++;
                }
            }
        }

        return new ViewModel(array(
            'feedbacks' => $latest_feedback_count,
            'orders' => $latest_orders_count,
            /* 'subscriptions' => $subscriptions,
              'bookings' => $bookings,
              'revenueStats' => $revenueStats,

              'sent_sms' => $sent_sms,
              'registration_stats' => $registration_stats,
              'PracStatsByCities' => $PracStatsByCities,
              'ConsStatsByCities' => $ConsStatsByCities,
              'PracCancStats' => $PracCancStats,
              'ConsCancStats' => $ConsCancStats,
              'userCancStats' => $userCancStats,
              'video_stats' => $video_stats, */
            'practitioner' => $practiotioner,
            'consumer' => $consumer,
            'pending_profiles' => $pending_profiles,
            'pending_feedback' => $pending_feedback,
            'pending_media' => $pending_media,
            'tasks' => $tasks,
            'total_unread' => $total_unread,
        ));
    }

    public function bookingsAction()
    {
        $request = $this->getRequest();
        $getData = (array) $request->getQuery();
        if (isset($getData['start']) && isset($getData['end'])) {
            $startDate = date('Y-m-d', $getData['start']);
            $endDate = date('Y-m-d', $getData['end']);

            $bookings = $this->getServiceLocator()->get('Admin\Model\BookingsTable')->booked(array('startDate' => $startDate, 'endDate' => $endDate));
            $events = array();
            // Fri May 30 2014 24:00:00 GMT+0530 (IST)
            foreach ($bookings as $booking) {
                $events[] = array('title' => $booking->sale_item_details, 'start' => date('D F d Y h:i:s O (T)', strtotime($booking->booked_date)), 'allDay' => false, 'backgroundColor' => '#35aa47', 'url' => '/admin/bookings/');
            }
            echo json_encode($events);
            exit;
        }
    }

    public function getSmsStats($tab = 'day')
    {
        $count = array();

        $count['today'] = 0;
        $count['week'] = 0;
        $count['month'] = 0;
        $count['year'] = 0;

        $result = $this->getServiceLocator()->get('Admin\Model\SmsHistoryTable')->fetchAll(false);

        $today = date("Y-m-d", strtotime("now"));
        $week = date("Y-m-d", strtotime("-1 week"));
        $month = date("Y-m-d", strtotime("-1 month"));
        $year = date("Y-m-d", strtotime("-1 year"));

        foreach ($result as $data) {

            $sent_date = date("Y-m-d", strtotime($data->sent_date));

            if ($sent_date == $today) {
                $count['today'] ++;
            }
            if ($sent_date > $week) {
                $count['week'] ++;
            }
            if ($sent_date > $month) {
                $count['month'] ++;
            }
            if ($sent_date > $year) {
                $count['year'] ++;
            }
        }

        return $count;
    }

    public function getRegistrationStats($tab = 'day')
    {

        $count = array();

        $today = date("Y-m-d", strtotime("now"));
        $week = date("Y-m-d", strtotime("-1 week"));
        $month = date("Y-m-d", strtotime("-1 month"));
        $year = date("Y-m-d", strtotime("-1 year"));

        switch ($tab) {
            case 'day' :
                $count = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getRegistrationStats($today);
                break;

            case 'week' :
                $count = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getRegistrationStats($week);
                break;

            case 'month' :
                $count = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getRegistrationStats($month);
                break;

            case 'year' :
                $count = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getRegistrationStats($year);
                break;
        }

        return $count;
    }

    public function UserStatsByCities($user_type_id, $status, $tab = 'day')
    {
        $data = array();

        $today = date("Y-m-d", strtotime("now"));
        $week = date("Y-m-d", strtotime("-1 week"));
        $month = date("Y-m-d", strtotime("-1 month"));
        $year = date("Y-m-d", strtotime("-1 year"));

        switch ($tab) {
            case 'day' :
                $data = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getPracStatsByCities($user_type_id, $today, $status);
                break;

            case 'week' :
                $data = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getPracStatsByCities($user_type_id, $week, $status);
                break;

            case 'month' :
                $data = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getPracStatsByCities($user_type_id, $month, $status);
                break;

            case 'year' :
                $data = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getPracStatsByCities($user_type_id, $year, $status);
                break;
        }

        return $data;
    }

    public function CancStats($tab = 'day')
    {
        $data = array();

        $today = date("Y-m-d", strtotime("now"));
        $week = date("Y-m-d", strtotime("-1 week"));
        $month = date("Y-m-d", strtotime("-1 month"));
        $year = date("Y-m-d", strtotime("-1 year"));

        switch ($tab) {
            case 'day' :
                $data = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getCancStats($today);
                break;

            case 'week' :
                $data = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getCancStats($week);
                break;

            case 'month' :
                $data = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getCancStats($month);
                break;

            case 'year' :
                $data = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getCancStats($year);
                break;
        }

        return $data;
    }

    public function graphAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $startDate = $request->getPost('start');
            $endDate = $request->getPost('end');
            $tab = $request->getPost('tab');

            /*switch ($tab) {
                case 'consumer' :
                    $data = array('total' => $this->getServiceLocator()->get('Admin\Model\UsersTable')->getDataByMonth($startDate, $endDate));
                    break;

                case 'service_provider' :
                    $data = array('total' => $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getDataByMonth($startDate, $endDate));
                    break;

                case 'subscriptions' :
                    $data = array('total' => $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable')->getDataByMonth($startDate, $endDate));
                    break;

                case 'bookings' :
                    $data = array('total' => $this->getServiceLocator()->get('Admin\Model\BookingsTable')->getDataByMonth($startDate, $endDate));
                    break;
            }*/
            $data['consumer'] = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getDataByMonth($startDate, $endDate);
            $data['service_provider'] = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getDataByMonth($startDate, $endDate);
            $data['subscriptions'] = $this->getServiceLocator()->get('Admin\Model\SubscriptionsTable')->getDataByMonth($startDate, $endDate);
            $data['bookings'] = $this->getServiceLocator()->get('Admin\Model\BookingsTable')->getDataByMonth($startDate, $endDate);
            echo json_encode($data);
        }
        exit;
    }

    public function getVideoStats($tab = 'day')
    {
        $video_views_count = array();
        $Media_video_uploaded_count = array();
        $Banner_video_uploaded_count = array();
        $Total_uploaded_count = array();

        $today = date("Y-m-d", strtotime("now"));
        $week = date("Y-m-d", strtotime("-1 week"));
        $month = date("Y-m-d", strtotime("-1 month"));
        $year = date("Y-m-d", strtotime("-1 year"));

        switch ($tab) {
            case 'day' :
                $video_views_count = $this->getServiceLocator()->get('Admin\Model\VideoViewsTable')->getViewsCount($today);
                $Media_video_uploaded_count = $this->getServiceLocator()->get('Admin\Model\MediaTable')->getUploadCount($today);
                $Banner_video_uploaded_count = $this->getServiceLocator()->get('Admin\Model\BannerUploadsTable')->getUploadCount($today);
                $Total_uploaded_count = $video_views_count['upload_count'] + $Media_video_uploaded_count['upload_count'];
                break;

            case 'week' :
                $video_views_count = $this->getServiceLocator()->get('Admin\Model\VideoViewsTable')->getViewsCount($week);
                $Media_video_uploaded_count = $this->getServiceLocator()->get('Admin\Model\MediaTable')->getUploadCount($week);
                $Banner_video_uploaded_count = $this->getServiceLocator()->get('Admin\Model\BannerUploadsTable')->getUploadCount($week);
                $Total_uploaded_count = $video_views_count['upload_count'] + $Media_video_uploaded_count['upload_count'];
                break;

            case 'month' :
                $video_views_count = $this->getServiceLocator()->get('Admin\Model\VideoViewsTable')->getViewsCount($month);
                $Media_video_uploaded_count = $this->getServiceLocator()->get('Admin\Model\MediaTable')->getUploadCount($month);
                $Banner_video_uploaded_count = $this->getServiceLocator()->get('Admin\Model\BannerUploadsTable')->getUploadCount($month);
                $Total_uploaded_count = $video_views_count['upload_count'] + $Media_video_uploaded_count['upload_count'];
                break;

            case 'year' :
                $video_views_count = $this->getServiceLocator()->get('Admin\Model\VideoViewsTable')->getViewsCount($year);
                $Media_video_uploaded_count = $this->getServiceLocator()->get('Admin\Model\MediaTable')->getUploadCount($year);
                $Banner_video_uploaded_count = $this->getServiceLocator()->get('Admin\Model\BannerUploadsTable')->getUploadCount($year);
                $Total_uploaded_count = $video_views_count['upload_count'] + $Media_video_uploaded_count['upload_count'];
                break;
        }

        return array(
            'view_count' => $video_views_count['views_count'],
            'upload_count' => $Total_uploaded_count,
        );
    }

}
