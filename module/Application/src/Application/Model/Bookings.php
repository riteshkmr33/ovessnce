<?php

namespace Application\Model;

use Application\Model\Api;
use Zend\Session\Container;
use Application\Model\Common;
use Application\Model\Consumers;
use Application\Model\Practitioners;

class Bookings
{

    private $api;

    public function __construct()
    {
        $this->api = new Api();
    }

    /* Function to get bookings */

    public function getBookings($api_url, $sp_id = "", $user_id = "", $status = '', $page = '', $recordsPerPage = '', $service_id = '', $id = '', $booking_type = '', $booking_time = '', $includeManual = 1)
    {
        $params = array();
        ($sp_id != "") ? $params['service_provider_id'] = $sp_id : '';
        ($user_id != "") ? $params['user_id'] = $user_id : '';
        ($status != "") ? $params['booking_status'] = $status : '';
        ($id != "") ? $params['id'] = $id : '';
        ($page != "") ? $params['page'] = $page : '';
        ($service_id != "") ? $params['service_provider_service_id'] = $service_id : '';
        ($recordsPerPage != "") ? $params['no_of_records'] = $recordsPerPage : '';
        ($booking_type != "") ? $params['booking_type'] = $booking_type : '';
        ($booking_time != "") ? $params['booking_time'] = $booking_time : '';
        $params['manual'] = $includeManual;
        
        $booking_res = $this->api->curl($api_url . "/api/booking/", $params, "GET");
        if ($booking_res->getStatusCode() == 200) {
            $content = json_decode($booking_res->getBody(), true);
            $suggestion_res = $this->api->curl($api_url . "/api/booking/", array('booking_ids' => $content['booking_ids']), "GET");

            if ($suggestion_res->getStatusCode() == 200) {
                $suggestion = json_decode($suggestion_res->getBody(), true);

                foreach ($content['results'] as $key => $result) {

                    if (!is_numeric($content['results'][$key]['user_id'])) {
                        $content['results'][$key]['consumer_first_name'] = $content['results'][$key]['mb_first_name'];
                        $content['results'][$key]['consumer_last_name'] = $content['results'][$key]['mb_last_name'];
                    }
		    $content['results'][$key]['consumer_number'] = $content['results'][$key]['consumer_number'];
		    $content['results'][$key]['sp_number'] = $content['results'][$key]['sp_number'];	
                    if (isset($suggestion[$content['results'][$key]['id']]) && is_array($suggestion[$content['results'][$key]['id']]) && count($suggestion[$content['results'][$key]['id']]) > 0) {
                        $keys = array_keys($suggestion[$content['results'][$key]['id']]);
                        $no_of_confirmations = 0;
                        // Getting confirmed entries
                        foreach ($suggestion[$content['results'][$key]['id']] as $history) {
                            $no_of_confirmations = (array_search(4, $history) != false) ? ($no_of_confirmations + 1) : $no_of_confirmations;
                        }

                        $content['results'][$key]['booking_status'] = array('user_id' => $suggestion[$content['results'][$key]['id']][max($keys)]['user_id'], 'status_id' => $suggestion[$content['results'][$key]['id']][max($keys)]['booking_status'], 'booking_time' => $suggestion[$content['results'][$key]['id']][max($keys)]['booking_time'], 'confirmations' => $no_of_confirmations);
                    } else {
                        $content['results'][$key]['booking_status'] = array('user_id' => $content['results'][$key]['user_id'], 'status_id' => $content['results'][$key]['status_id'], 'booking_time' => $content['results'][$key]['booked_date'], 'confirmations' => 0);
                    }
                }
            }

            return $content;
        } else {
            return false;
        }
    }

    /* Function to get bookings count */

    public function getBookingsCount($api_url, $sp_id = "", $user_id = "", $status = '')
    {
        $params = array();
        ($sp_id != "") ? $params['service_provider_id'] = $sp_id : '';
        ($user_id != "") ? $params['user_id'] = $user_id : '';
        ($status != "") ? $params['booking_status'] = $status : '';

        $booking_res = $this->api->curl($api_url . "/api/booking/", $params, "GET");
        if ($booking_res->getStatusCode() == 200) {
            $content = json_decode($booking_res->getBody(), true);
            $count = 0;
            foreach ($content['results'] as $key => $result) {
                if (is_numeric($content['results'][$key]['user_id'])) {
                    $count++;
                }
            }
            return $count;
        } else {
            return $count;
        }
    }

    /* Function to get available slots for booking */

    public function getAvailableSlots($api_url, $user_id, $date, $service_duration, $address = '', $bookings = array())
    {
        $availableSlots = $bookedSlots = array();
        $day = date('l', strtotime($date));

        // Fetching service providers workdays and time
        $filter = array('user_id' => $user_id);
        ($address != '') ? $filter['address_id'] = $address : '';
        $workdays_data = $this->api->curl($api_url . "/api/sp_availability/", $filter, "GET");
        $results = json_decode($workdays_data->getBody(), true);

        if (isset($results) && is_array($results) && count($results) > 0) {
            foreach ($results as $result) {
                $timeSlotsPerDay[$result['day']] = (isset($result['lunch_start_time']) && $result['lunch_start_time'] != '00:00:00' && $result['lunch_start_time'] != 'None' && $result['lunch_start_time'] != '') ? array('fullDay' => array('start' => $result['start_time'], 'end' => $result['end_time']), 'lunch' => array('start' => $result['lunch_start_time'], 'end' => $result['lunch_end_time'])) : array('first' => array('start' => $result['start_time'], 'end' => $result['end_time']));
            }

            // Fetching service providers appointment delay time
            $delay_data = $this->api->curl($api_url . "/api/appointment_delay_list/", array('user_id' => $user_id), "GET");
            $delay = json_decode($delay_data->getBody(), true);

            $delay = isset($delay[0]['delay_time']) ? $delay[0]['delay_time'] : '30';
            $delay_time = $delay * 60;
            $slotDuration = ($service_duration * 60) + $delay_time;

            if (count($bookings['results']) == 0) {
                // Fetching service providers bookings
                /* $booking_data = $this->api->curl($api_url . "/api/booking/", array('service_provider_id' => $user_id), "GET");
                  $bookings = json_decode($booking_data->getBody(), true); */
                $bookings = $this->getBookings($api_url, $user_id);
            }

            foreach ($bookings['results'] as $booking) {
                if (date('Y-m-d', strtotime($booking['booking_status']['booking_time'])) == $date && $booking['booking_status']['status_id'] != '6') {
                    $duration = $booking['duration'] * 60;
                    $bookingDay = date('l', strtotime($booking['booked_date']));
                    $bookedSlots[] = array('start' => strtotime(date('H:i:s', strtotime($booking['booking_status']['booking_time']))), 'end' => strtotime(date('H:i:s', (strtotime($booking['booking_status']['booking_time']) + $duration + $delay_time))));
                }
            }

            if (isset($timeSlotsPerDay[$day]['fullDay']['start'])) {
                $dayStart = strtotime($timeSlotsPerDay[$day]['fullDay']['start']);
                $dayEnd = ($timeSlotsPerDay[$day]['fullDay']['end'] != 'None') ? strtotime($timeSlotsPerDay[$day]['fullDay']['end']) : strtotime('23:45:00');
                $lunchStart = (isset($timeSlotsPerDay[$day]['lunch']['start']) && $timeSlotsPerDay[$day]['lunch']['start'] != '00:00:00' && $timeSlotsPerDay[$day]['lunch']['start'] != 'None') ? strtotime($timeSlotsPerDay[$day]['lunch']['start']) : '';
                $lunchEnd = (isset($timeSlotsPerDay[$day]['lunch']['end']) && $timeSlotsPerDay[$day]['lunch']['end'] != '00:00:00' && $timeSlotsPerDay[$day]['lunch']['end'] != 'None') ? strtotime($timeSlotsPerDay[$day]['lunch']['end']) : strtotime('23:45:00');
            } else {
                $dayStart = strtotime($timeSlotsPerDay[$day]['first']['start']);
                $dayEnd = ($timeSlotsPerDay[$day]['first']['end'] != 'None') ? strtotime($timeSlotsPerDay[$day]['first']['end']) : strtotime('23:45:00');
                $lunchStart = '';
                $lunchEnd = strtotime('23:45:00');
            }

            $slotStartTime = $dayStart;
            $slotEndTime = $dayStart + $slotDuration;

            //echo $slotEndTime.' <= '.$dayEnd;
            while ($slotEndTime <= $dayEnd) {
                $flag = false;
                $startFrom = false;
                // Check slot time should be in working hours
                // echo $dayStart." != '' && ".$slotStartTime." >= ".$dayStart.") && (".$dayEnd." == '' || ".$slotEndTime." <= ".$dayEnd;
                if (($dayStart != '' && $slotStartTime >= $dayStart) && ($dayEnd == '' || $slotEndTime <= $dayEnd)) {
                    // Check slot time should NOT be in lunch hours
                    if (($lunchStart == '') || (($slotStartTime >= $lunchEnd) || ($slotStartTime < $lunchStart && $slotEndTime <= $lunchStart))) {
                        if (isset($bookedSlots) && count($bookedSlots) > 0) {
                            foreach ($bookedSlots as $bookedSlot) {
                                // Check slot time should NOT be in any booking hours
                                if (($slotStartTime >= $bookedSlot['end']) || ($slotStartTime < $bookedSlot['start'] && $slotEndTime <= $bookedSlot['start'])) {
                                    //echo date('h:iA', $slotEndTime).'>='.date('h:iA', $bookedSlot['start'])." || (".date('h:iA', $slotStartTime)." < ".date('h:iA', $bookedSlot['start'])." && ".date('h:iA', $slotEndTime)." <= ".date('h:iA', $bookedSlot['start']).") <br/>";
                                    $flag = true;
                                } else {
                                    $slotStartTime = $bookedSlot['end'];
                                    $startFrom = true;
                                    $flag = false;
                                    break;
                                }
                            }
                        } else {
                            $flag = true;
                        }
                    } else {
                        $slotStartTime = $lunchEnd;
                        $startFrom = true;
                    }
                }

                ($flag == true) ? $availableSlots[] = array('start' => date('h:iA', $slotStartTime), 'end' => date('h:iA', ($slotEndTime - $delay_time))) : '';
                $slotStartTime = ($startFrom == false) ? $slotStartTime + $slotDuration : $slotStartTime;
                $slotEndTime = $slotStartTime + $slotDuration;
            }
            //print_r($availableSlots); exit;
        }

        return $availableSlots;
    }

    /* Function to send suggestion */

    public function suggestTime($api_url, $booking, $newDate, $userid, $user_type_id, $common)
    {
        $model = new Practitioners;
        $suggestion_res = $this->api->curl($api_url . "/api/suggestionhistory/", array('booking_id' => $booking), "GET");
        if ($suggestion_res->getStatusCode() == 200) {
            $suggestions = json_decode($suggestion_res->getBody(), true);

            // change status of all previous pending suggestions to cancel
            foreach ($suggestions as $suggestion) {
                ($suggestion['booking_status'] == '5') ? $this->api->curl($api_url . "/api/suggestionhistory/" . $suggestion['id'] . "/", array('booking_status' => 6), "PUT") : '';
            }

            $booking_data = $this->getBookings($api_url, '', '', '', '', '', '', $booking);

            if ($user_type_id == 3) {
                $userObject = new Consumers;
                $user_type = 'Practitioner';
                $sender_id = $booking_data['results'][0]['user_id'];
                $user_name = $booking_data['results'][0]['consumer_first_name'] . ' ' . $booking_data['results'][0]['consumer_last_name'];
                $user_email = $booking_data['results'][0]['consumer_email'];
                $contact = $userObject->getContact($api_url, $booking_data['results'][0]['user_id']);
                $phone = $contact[0]['home_phone'];
            } else {
                $userObject = new Practitioners;
                $user_type = 'Consumer';
                $sender_id = $booking_data['results'][0]['service_provider_id'];
                $user_name = $booking_data['results'][0]['sp_first_name'] . ' ' . $booking_data['results'][0]['sp_last_name'];
                $user_email = $booking_data['results'][0]['sp_email'];
                $contact = $userObject->getContact($api_url, $booking_data['results'][0]['service_provider_id']);
                $phone = $contact[0]['phone_number'];
            }

            // add new suggestion
            $add_res = $this->api->curl($api_url . "/api/suggestionhistory/", array('user_id' => $userid, 'booking_id' => $booking, 'booking_time' => date('Y-m-d H:i:s', strtotime($newDate)), 'booking_status' => 5), "POST");
            if ($add_res->getStatusCode() != 200 && $add_res->getStatusCode() != 201) {
                return array('status' => '0', 'msg' => 'Failed to suggest new date and time for appointment..!!', 'errors' => json_decode($add_res->getBody(), true));
            } else {

                $pattern = array('/{{user_name}}/i', '/{{user_type}}/i', '/{{booking_id}}/i', '/{{new_date_time}}/i');
                $replace = array('<strong>' . $user_name . '</strong>', $user_type, '<strong>#' . $booking . '</strong>', '<strong>' . date('l d/m/Y h:i A', strtotime($newDate)) . '</strong>');
                
                $subscriptionDetails = $common->getSubscriptiondetails($api_url, $booking_data['results'][0]['service_provider_id'], true);
                
                if ($user_type == 'Consumer') {
                    $userFeatures = $common->getFeatures($api_url, $booking_data['results'][0]['user_id']);

                    if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features']) && ($userFeatures['email'] == 1)) {
                        $common->sendMail($api_url, $user_email, '', 12, '', $pattern, $replace);
                    }

                    if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && ($userFeatures['sms'] == 1)) {
                        $common->sendMsg($phone, 4, '', $pattern, array($user_name, $user_type, $booking, date('l d/m/Y h:i A', strtotime($newDate))));
                    }
                } else {
                    $userFeatures = $common->getFeatures($api_url, $booking_data['results'][0]['service_provider_id']);
                    $common->sendMail($api_url, $user_email, '', 12, '', $pattern, $replace);
                    
                    if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && $userFeatures['sms'] == 1) {
                        $common->sendMsg($phone, 4, '', $pattern, array($user_name, $user_type, $booking, date('l d/m/Y h:i A', strtotime($newDate))));
                    }
                }

                return array('status' => '1', 'msg' => 'New date and time suggested for appointment..!!', 'notifications' => $model->getNotifications($api_url), 'phone' => $phone, 'features' => $userFeatures, 'subscription' => $subscriptionDetails['features']);
            }
        } else {
            return array('status' => '0', 'msg' => 'Unable to suggest new date and time for appointment..!!', 'errors' => json_decode($suggestion_res->getBody(), true));
        }
    }

    /* Function to change booking status */

    public function changeBookingStatus($api_url, $id, $status, $common)
    {
        $suggestion_res = $this->api->curl($api_url . "/api/suggestionhistory/", array('booking_id' => $id), "GET");
        $session = new Container('frontend');
        //print_r($id); exit;
        if ($suggestion_res->getStatusCode() == 200) {
            $suggestions = json_decode($suggestion_res->getBody(), true);
            $last = end($suggestions);
            $user = json_decode($last['user'], true);
            $user_name = $user['first_name'] . ' ' . $user['last_name'];
            $user_email = $user['email'];

            if ($user['user_type_id'] != '3') {
                $userObject = new Consumers;
                $user_type = 'Practitioner';
                $contact = $userObject->getContact($api_url, $user['user_id']);
                $phone = $contact[0]['home_phone'];
            } else {
                $userObject = new Practitioners;
                $user_type = 'Consumer';
                $contact = $userObject->getContact($api_url, $user['user_id']);
                $phone = $contact[0]['phone_number'];
            }

            $update_res = $this->api->curl($api_url . "/api/suggestionhistory/" . $last['id'] . "/", array('booking_status' => $status), "PUT");
            if ($update_res->getStatusCode() != 200 && $update_res->getStatusCode() != 201) {
                return false;
            } else {
                switch ($status) {
                    case 4 :
                        $status = 'Confirmed';
                        break;
                    case 5 :
                        $status = 'Pending Approval';
                        break;
                    case 6 :
                        $status = 'Cancelled';
                        break;
                }

                $pattern = array('/{{user_name}}/i', '/{{user_type}}/i', '/{{booking_id}}/i', '/{{status}}/i');
                $replace = array('<strong>' . $user_name . '</strong>', $user_type, '<strong>#' . $last['booking_id'] . '</strong>', $status);
                
                $userFeatures = $common->getFeatures($api_url, $user['user_id']);
                
                if ($user_type == 'Consumer') {
                    $subscriptionDetails = $common->getSubscriptiondetails($api_url, $session->userid, true);

                    if ((isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(11, $subscriptionDetails['features'])) || ($userFeatures['email'] == 1)) {
                        $common->sendMail($api_url, $user_email, '', 14, '', $pattern, $replace);
                    }

                    if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && ($userFeatures['sms'] == 1)) {
                        $common->sendMsg($phone, 3, '', $pattern, array($user_name, $user_type, $last['booking_id'], $status));
                    }
                } else {
                    $subscriptionDetails = $common->getSubscriptiondetails($api_url, $user['user_id'], true);
                    $common->sendMail($api_url, $user_email, '', 14, '', $pattern, $replace);
                    
                    if (isset($subscriptionDetails['features']) && is_array($subscriptionDetails['features']) && in_array(12, $subscriptionDetails['features']) && $userFeatures['sms'] == 1) {
                        $common->sendMsg($phone, 3, '', $pattern, array($user_name, $user_type, $last['booking_id'], $status));
                    }
                }

                return true;
            }
        } else {
            return false;
        }
    }

    /* Function to add new booking */

    public function addBooking($api_url, $data = array())
    {
        if (count($data) > 0) {
            $booking_res = $this->api->curl($api_url . "/api/booking/", $data, "POST");
            //print_r($booking_res); exit;
            if ($booking_res->getStatusCode() == 200 || $booking_res->getStatusCode() == 201) {
                $content = json_decode($booking_res->getBody(), true);

                if (isset($content['id'])) {
                    return array('status' => 1, 'id' => $content['id']);
                } else {
                    return array('status' => 0, 'data' => $content);
                }
            } else {
                return array('status' => 0, 'data' => json_decode($booking_res->getBody(), true));
            }
        }
    }

    /* Function to add new users card details */

    public function addUsersCardDetails($api_url, $data = array())
    {
        if (count($data) > 0) {

            $res = $this->api->curl($api_url . "/api/card_details/", $data, "POST");

            if ($res->getStatusCode() == 200) {
                $content = json_decode($res->getBody(), true);

                if (isset($content['id'])) {
                    return array('status' => 1, 'id' => $content['id']);
                } else {
                    return array('status' => 0, 'data' => $content);
                }
            } else {
                return array('status' => 0, 'data' => json_decode($res->getBody(), true));
            }
        }
    }

    /* Function to add manual booking */

    public function addManualBooking($api_url, $data)
    {
        $res = $this->api->curl($api_url . "/api/manualbooking/", $data, "POST");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);

            if (isset($content['id'])) {
                return array('status' => 1, 'id' => $content['id'], 'msg' => 'Booking details successfully added..!!');
            } else {
                return array('status' => 0, 'data' => $content, 'msg' => 'Failed to add booking details..!!');
            }
        } else {
            return array('status' => 0, 'data' => json_decode($res->getBody(), true), 'msg' => 'Unable to add booking details..!!');
        }
    }

}
