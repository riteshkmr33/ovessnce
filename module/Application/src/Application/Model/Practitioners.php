<?php

namespace Application\Model;

use Application\Model\Api;
use Zend\Session\Container;
use Application\Model\Bookings;

class Practitioners
{

    private $api;

    public function __construct()
    {
        $this->api = new Api();
    }

    /* Function to get service provider availability */

    public function getAvailableDays($api_url)
    {
        $res = $this->api->curl($api_url . '/api/availability_days/', array(), "GET");
        $days = array();
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            foreach ($content as $day) {
                $days[$day['id']] = $day['day'];
            }
        }
        return $days;
    }

    /* Function to get service provider appointment delay */

    public function getAppointmentDelay($id, $api_url)
    {
        $res = $this->api->curl($api_url . '/api/appointment_delay_list/', array('user_id' => $id), "GET");
        $delay_time = 0;
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            $delay_time = array('id' => $content[0]['id'], 'delay_time' => $content[0]['delay_time']);
        }
        return $delay_time;
    }

    /* Function to get service provider notifications */

    public function getNotifications($api_url, $user_type = 'practitioner')
    {
        $booking_count = $referral_count = $review_count = 0;
        $session = new Container('frontend');
        $notifications = array('booking' => 0, 'reviews' => 0, 'invites' => 0, 'inbox' => 0);

        if ($user_type == 'consumer') {
            $booking_res = $this->api->curl($api_url . '/api/booking/', array('user_id' => $session->userid, 'booking_status' => 5), "GET");
            $inbox_res = $this->api->curl($api_url . '/api/messages/', array('to_user_id' => $session->userid, 'deleteFlag_c' => '0', 'readFlag_c' => '0'), "GET");
        } else {
            $booking_res = $this->api->curl($api_url . '/api/booking/', array('service_provider_id' => $session->userid, 'booking_status' => 5), "GET");
            $inbox_res = $this->api->curl($api_url . '/api/messages/', array('to_user_id' => $session->userid, 'deleteFlag_p' => '0', 'readFlag_p' => '0'), "GET");
        }
        //echo '<pre>'; print_r($inbox_res); exit;

        if ($booking_res->getStatusCode() == 200) {
            $booking = json_decode($booking_res->getBody(), true);
            //echo '<pre>'; print_r($booking); exit;
            if (isset($booking['booking_ids']) && $booking['booking_ids'] != '') {
                $suggestion_res = $this->api->curl($api_url . '/api/booking/', array('booking_ids' => $booking['booking_ids']), "GET");

                if ($suggestion_res->getStatusCode() == 200) {
                    $suggestions = json_decode($suggestion_res->getBody(), true);
                    //echo '<pre>'; print_r($suggestions); exit;
                    foreach ($suggestions as $suggestion) {
                        //echo '<pre>asdf'; var_dump($suggestion); exit;
                        $keys = array_keys($suggestion);
                        $suggestion = $suggestion[max($keys)];
                        $booking_count = ($suggestion['user_id'] != $session->userid && strtotime($suggestion['booking_time']) > strtotime(date('Y-m-d h:i:s'))) ? ($booking_count + 1) : $booking_count;
                    }
                }
            }

            $notifications['booking'] = $booking_count;
        }

        $review_res = $this->api->curl($api_url . '/api/feedback/', array('user_id' => $session->userid, 'view_status' => 0), "GET");

        if ($review_res->getStatusCode() == 200) {
            $reviews = json_decode($review_res->getBody(), true);

            $review_count = count($reviews['results']);

            /* foreach ($reviews['results'] as $review) {
              $review_count = (strtotime($review['created_date']) >= strtotime('-1 days')) ? $review_count + 1 : $review_count;
              } */
        }

        if ($inbox_res->getStatusCode() == 200) {
            $inbox = json_decode($inbox_res->getBody(), true);
            //echo '<pre>'; print_r($inbox); exit;
            //$messages['inbox'] = $inbox['count'];
            $messages['inbox'] = count($inbox);
        }

        // getting reference count

        $ref_res = $this->api->curl($api_url . '/api/sp/reference/', array('user_id' => $session->userid, 'view_status' => 0), "GET");
        if ($ref_res->getStatusCode() == 200) {
            $referrals = json_decode($ref_res->getBody(), true);
            $referral_count = count($referrals['results']);

            /* foreach ($referrals['results'] as $reference) {
              $referral_count = (strtotime($reference['created_date']) >= strtotime('-1 days')) ? $referral_count + 1 : $referral_count;
              } */
        }

        $notifications['reviews'] = $review_count;
        $notifications['referrals'] = $referral_count;
        $notifications['total'] = array_sum($notifications);
        $notifications['inbox'] = $messages['inbox'];
        //echo '<pre>'; print_r($notifications); exit;
        return $notifications;
    }

    /* Function to get service provider newsletters */

    public function getSPnewsletter($id, $api_url, $page = '', $itemsPerPage = '', $newsletter_id = '')
    {

        $url_newsletter = $api_url . "/api/newsletter/";
        $data_newsletter = array('created_by' => $id);
        ($page != "") ? $data_newsletter['page'] = $page : '';
        ($itemsPerPage != "") ? $data_newsletter['no_of_records'] = $itemsPerPage : '';
        $res_newsletter = ($newsletter_id != '') ? $this->api->curl($url_newsletter . $newsletter_id . '/', array(), "GET") : $this->api->curl($url_newsletter, $data_newsletter, "GET");

        if ($res_newsletter->getStatusCode() == 200) {

            $newsletters = json_decode($res_newsletter->getBody(), true);

            if (isset($newsletters['results']) && count($newsletters['results']) > 0) {
                $i = 0;
                foreach ($newsletters['results'] as $data) {

                    $newsletter_list[$i]['id'] = $data['id'];
                    $newsletter_list[$i]['subject'] = $data['subject'];
                    $newsletter_list[$i]['message'] = stripslashes($data['message']);
                    $newsletter_list[$i]['send_date'] = $this->getNewsletterSentDate($api_url, $data['id']);
                    $newsletter_list[$i]['status_id'] = $data['status_id'];
                    $newsletter_list[$i]['created_date'] = $data['date_created'];
                    $newsletter_list[$i]['created_by'] = $data['created_by'];
                    $i++;
                }
            } else if ($newsletter_id != "") {
                array_walk($newsletters, function(&$value) {
                    $value = stripslashes($value);
                });
                $newsletter_list = $newsletters;
            } else {
                $newsletter_list = array();
            }
        } else {
            $newsletter_list = array();
        }

        return $newsletter_list;
    }

    /* Function to get newsletter sent date */

    public function getNewsletterSentDate($api_url, $nid)
    {
        $res = $this->api->curl($api_url . '/api/newsletter/send/', array('newsletter_id' => $nid, 'status' => 1), "GET");
        $sendDate = "Not set";
        //echo '<pre>'; print_r($res); exit;
        if ($res->getStatusCode() == "200") {
            $content = json_decode($res->getBody(), true);

            if (count($content['results']) > 0) {
                $sendDate = (isset($content['results'][0]['sent_date']) && $content['results'][0]['sent_date'] != '') ? date('D d-m-Y', strtotime($content['results'][0]['sent_date'])) : "Not set";
            }
        }

        return $sendDate;
    }

    /* Function to add or update newsletter */

    public function addUpdateNewsletter($api_url, $user_id, $id = '', $subject, $message, $status = '')
    {
        if ($id != "") {
            $res = $this->api->curl($api_url . "/api/newsletter/" . $id . "/", array('created_by' => $user_id, 'subject' => $subject, 'message' => $message), "PUT");
        } else {
            $res = $this->api->curl($api_url . "/api/newsletter/", array('created_by' => $user_id, 'subject' => $subject, 'message' => $message, 'status_id' => 1), "POST");
        }
        $content = json_decode($res->getBody(), true);

        if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
            return array('status' => 1, 'msg' => 'Newsletter added successfully..!!', 'id' => $content['id']);
        } else {
            return array('status' => 0, 'msg' => 'Failed to add newsletter..!!', 'errors' => $content);
        }
    }

    /* Function to delete newsletter */

    public function deleteNewsletter($api_url, $ids)
    {
        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $res = $this->api->curl($api_url . "/api/newsletter/" . $id . "/", array(), "DELETE");
                if ($res->getStatusCode() != 200 && $res->getStatusCode() != 204) {
                    return array('status' => 0, 'msg' => 'Failed to delete newsletter(s)..!!');
                }
            }
            return array('status' => 1, 'msg' => 'Newsletter(s) deleted successfully..!!');
        } else {
            return array('status' => 0, 'msg' => 'Failed to delete newsletter(s)..!!');
        }
    }

    /* Function to change status of newsletters */

    public function changeNewsletterStatus($api_url, $ids, $status)
    {
        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $res = $this->api->curl($api_url . "/api/newsletter/" . $id . "/", array('status_id' => $status), "PUT");
                if ($res->getStatusCode() != 200 && $res->getStatusCode() != 201) {
                    return array('status' => 0, 'msg' => 'Failed to change status..!!');
                }
            }
            return array('status' => 1, 'msg' => 'Status changed successfully..!!');
        } else {
            return array('status' => 0, 'msg' => 'Failed to change status..!!');
        }
    }

    /* Function to get service provider ratings average */

    public function getSPRatings($user_id, $api_url, $data_type = 'average')
    {
        $url = $api_url . "/api/rating/";
        $data = array('users_id' => $user_id);
        $res = $this->api->curl($url, $data, "GET");

        if ($res->getStatusCode() == 200) {
            $ratings = json_decode($res->getBody(), true);
            //echo '<pre>'; print_r($ratings); exit;

            $count = $total_users = 0;
            $avgs = $rating_details = $ratings_sum = $cat_voted = $user_voted = array();

            foreach ($ratings as $rating) {

                if (array_key_exists($rating['created_by'], $ratings_sum) == true) {

                    if (isset($ratings_sum[$rating['created_by']][$rating['rating_type']])) {
                        $ratings_sum[$rating['created_by']][$rating['rating_type']] = $ratings_sum[$rating['created_by']][$rating['rating_type']] + $rating['rate'];
                        $user_voted[$rating['created_by']][$rating['rating_type']] = $user_voted[$rating['created_by']][$rating['rating_type']] + 1;
                    } else {
                        $ratings_sum[$rating['created_by']][$rating['rating_type']] = $rating['rate'];
                        $user_voted[$rating['created_by']][$rating['rating_type']] = 1;
                    }
                } else {
                    $ratings_sum[$rating['created_by']][$rating['rating_type']] = $rating['rate'];
                    $user_voted[$rating['created_by']][$rating['rating_type']] = 1;
                    $total_users++;
                }
                /* $count++;
                  if (isset($sum[$rating['created_by']])) {
                  $sum[$rating['created_by']] = $sum[$rating['created_by']] + $rating['rate'];
                  } else {
                  $sum[$rating['created_by']] = $rating['rate'];
                  $count = 1;
                  $total_users++;
                  } */

                //$user_voted[$rating['created_by']] = isset($user_voted[$rating['created_by']]) ? $user_voted[$rating['created_by']] + 1 : 1;
                $cat_voted[$rating['rating_type']] = isset($cat_voted[$rating['rating_type']]) ? $cat_voted[$rating['rating_type']] + 1 : 1;
                $rating_details[$rating['rating_type']] = isset($rating_details[$rating['rating_type']]) ? $rating_details[$rating['rating_type']] + $rating['rate'] : $rating['rate'];
            }

            //echo '<pre>'; print_r($ratings_sum); exit;
            //echo '<pre>'; print_r($user_voted); exit;
            //echo '<pre>'; echo $total_users; print_r($ratings_sum); exit;

            foreach ($ratings_sum as $user_id => $cats) {
                $sum = 0;
                foreach ($cats as $cat => $value) {
                    $sum = ($value > 0 && $user_voted[$user_id][$cat] > 0) ? $sum + round($value / $user_voted[$user_id][$cat]) : $sum;
                }

                $avgs[$user_id] = round($sum / count($cats));
            }

            foreach ($rating_details as $key => $value) {
                $rating_details[$key] = round($value / $cat_voted[$key]);
            }

            //echo '<pre>'.count($cat_voted); print_r($avgs); exit;
            //echo '<pre>'; echo $total_users; print_r($rating_details); exit;
            //echo array_sum($avgs).' / '.$total_users; exit;
            $averageRating = (count($avgs) > 0 && array_sum($avgs) > 0) ? round((array_sum($avgs) / $total_users)) : 0;
            //$averageRating = (count($cat_voted) > 0) ? round($average / count($cat_voted)) : 0;
            return ($data_type == 'detailed') ? array('average' => $averageRating, 'details' => $rating_details) : $averageRating;
        } else {
            return 0;
        }
    }

    /* Function to get service provider Workdays */

    public function getSPWorkdays($user_id, $api_url, $address = '', $with_time = false)
    {
        $url = $api_url . "/api/sp_availability/";
        $data = array('user_id' => $user_id);
        ($address != '') ? $data['address_id'] = $address : '';
        $res = $this->api->curl($url, $data, "GET");

        if ($res->getStatusCode() == 200) {
            $workdays = json_decode($res->getBody(), true);

            if (count($workdays) > 0) {
                $days = array();
                $time = array();
                foreach ($workdays as $workday) {
                    if ($workday['start_time'] != 'None' || $workday['end_time'] != 'None') {
                        $days[] = $workday['day'];
                    }

                    $time[$workday['days_id']] = array('id' => $workday['id'], 'start_time' => $workday['start_time'], 'end_time' => $workday['end_time'], 'lunch_start_time' => $workday['lunch_start_time'], 'lunch_end_time' => $workday['lunch_end_time'], 'address_id' => $workday['address_id']);
                }
                //echo '<pre>'; print_r($time); exit;
                return ($with_time == true) ? $time : implode(', ', $days);
            } else {
                return 'Not Available';
            }
        } else {
            return 'Not Available';
        }
    }

    /* Function to get educations */

    public function getEducations($api_url)
    {
        $res = $this->api->curl($api_url . '/api/education/', array('status_id' => 1), "GET");
        $educations = array();
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            foreach ($content as $education) {
                $educations[$education['id']] = $education['education_label'];
            }
        }
        return $educations;
    }

    /* Function to get languages */

    public function getLanguages($api_url)
    {
        $res = $this->api->curl($api_url . '/api/language/', array('status_id' => 1), "GET");
        $languages = array();
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            foreach ($content as $language) {
                $languages[$language['id']] = $language['language_name'];
            }
        }
        return $languages;
    }

    /* Function to get consumers who purchased services */

    public function getServiceConsumers($api_url, $sid, $page, $records, $incldSuspnded = true, $checkSettings = false)
    {
        $filter = array('check_booking' => 1, 'sp_provider' => $sid, 'page' => $page, 'no_of_records' => $records);
        ($incldSuspnded == false) ? $filter['suspended'] = 1 : '';
        ($checkSettings == true) ? $filter['check_perm'] = 1 : '';
        $res = $this->api->curl($api_url . '/api/users/', $filter, "GET");
        $consumers = array();

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);

            foreach ($content['results'] as $consumer) {
                $consumers[] = array('id' => $consumer['id'], 'avtar' => $consumer['avtar_url'], 'name' => $consumer['first_name'] . ' ' . $consumer['last_name'], 'email' => $consumer['email']);
            }
        }

        return $consumers;
    }

    /* Function to save users ids on which newsletter will be sent */

    public function sendNewsletter($api_url, $newsletter_id, $users = array())
    {
        $result = array();

        if (count($users) > 0) {
            foreach ($users as $user) {
                $res = $this->api->curl($api_url . '/api/newsletter/send/', array('user_id' => $user, 'newsletter_id' => $newsletter_id, 'status' => 0));
                if ($res->getStatusCode() != 200 && $res->getStatusCode() != 201) {
                    $content = json_decode($res->getBody(), true);
                    $result['status'] = 0;
                    $result['msg'] = 'Failed to send newsletter..!!';
                    $result['errors'] = $content;
                    return $result;
                }
            }
            $result['status'] = 1;
            $result['msg'] = 'Users added in recipient list for this newsletter. Redirecting to dashboard..!!';
        } else {
            $result['status'] = 0;
            $result['msg'] = 'No user selected to send newsletter..!!';
        }

        return $result;
    }

    /* Function to get service provider details */

    public function getSPDetails($api_url, $id)
    {
        $res = $this->api->curl($api_url . '/api/spusers/' . $id . '/', array(), "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            return $content;
        } else {
            return false;
        }
    }

    /* Function to get service provider referrals */

    public function getSPreferrals($api_url, $id = "", $referred_by = "", $page = '', $itemsPerPage = '')
    {
        $params = $data = array();
        ($id != "") ? $params['user_id'] = $id : '';
        ($referred_by != "") ? $params['referred_by'] = $referred_by : '';
        ($page != "") ? $params['page'] = $page : '';
        ($itemsPerPage != "") ? $params['no_of_records'] = $itemsPerPage : '';

        $res = $this->api->curl($api_url . '/api/sp/reference/', $params, "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);

            foreach ($content['results'] as $record) {
                $user = json_decode($record['user_info'], true);
                $referred_by = json_decode($record['referred_by_info'], true);
                $service = json_decode($record['service'], true);
                $data[] = array('id' => $record['id'],
                    'user_avtar' => $user['avtar_url'],
                    'user_name' => $user['first_name'] . " " . $user['last_name'],
                    'referred_by_id' => $referred_by['user_id'],
                    'referred_by_avtar' => $referred_by['avtar_url'],
                    'referred_by_name' => $referred_by['first_name'] . " " . $referred_by['last_name'],
                    'service' => $service['category_name'],
                    'message' => stripslashes($record['message']));
            }
        }

        return $data;
    }

    /* Function to get services */

    public function getSpserviceData($api_url, $id = "")
    {
        $url = $api_url . "/api/servicecategory/";
        $data = ($id != "") ? array('parent_id' => $id) : array();
        $res = $this->api->curl($url, $data, "GET");

        if ($res->getStatusCode() == 200) {
            return json_decode($res->getBody(), true);
        }
    }
    
    /* Function to get parent service name */
    
    public function getParentService($api_url, $id)
    {
        $res = $this->api->curl($api_url."/api/servicecategory/".$id."/", array(), "GET");
        
        if ($res->getStatusCode() == 200) {
            $serviceDetails = json_decode($res->getBody(), true);
            
            if ($serviceDetails['parent_id'] > 0) {
                $parentRes = $this->api->curl($api_url."/api/servicecategory/".$serviceDetails['parent_id']."/", array(), "GET");
                return json_decode($parentRes->getBody(), true);
            } else {
                return $serviceDetails;
            }
        } else {
            return false;
        }
    }

    /* Function to get service providers list */

    public function getSPlist($api_url, $exclude_user = '')
    {
        $filter = array();
        ($exclude_user != '') ? $filter['exclude_user'] = $exclude_user : '';
        $res = $this->api->curl($api_url . '/api/spusers/', $filter, "GET");
        $users = array();

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);

            foreach ($content['results'] as $user) {
                $users[] = array('id' => $user['id'], 'name' => $user['first_name'] . " " . $user['last_name']);
            }
        }
        return $users;
    }

    /* Function to add practitioner reference */

    public function addReference($api_url, $data, $user)
    {
        if (isset($data->id) && $data->id != "") {
            $res = $this->api->curl($api_url . '/api/sp/reference/' . $data->id . '/', array('user_id' => $data->practitioner, 'service_id' => $data->service, 'referred_by' => $user, 'message' => trim($data->message)), "PUT");
            $success = 'Reference updated successfully..!!';
            $error = 'Failed to update reference..!!';
        } else {
            $res = $this->api->curl($api_url . '/api/sp/reference/', array('user_id' => $data->practitioner, 'service_id' => $data->service, 'referred_by' => $user, 'message' => trim($data->message)), "POST");
            $success = 'Reference added successfully..!!';
            $error = 'Failed to add reference..!!';
        }

        if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
            return array('status' => 1, 'msg' => $success);
        } else {
            return array('status' => 0, 'msg' => $error, 'code' => $res);
        }
    }

    /* Function to delete practitioner reference */

    public function deleteReference($api_url, $ids = array())
    {
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $res = $this->api->curl($api_url . '/api/sp/reference/' . $id . '/', array(), "DELETE");
                if ($res->getStatusCode() != 200 && $res->getStatusCode() != 204) {
                    return array('status' => 0, 'msg' => 'Failed to delete reference..!!', 'code' => $res);
                }
            }
            return array('status' => 1, 'msg' => 'Reference successfully deleted..!!', 'code' => $res);
        }
    }

    /* function to set review flag - starts here */

    public function setreviewFlg($result)
    {
        $reviewFlag = false;

        if ($result) {
            if (count($result['results']) > 0) {
                foreach ($result['results'] as $data) {
                    $date_now = new \DateTime("now");
                    $date_booked = new \DateTime($data['booking_status']['booking_time']);
                    if ($date_booked < $date_now) {
                        $reviewFlag = true;
                        break;
                    } else {
                        $reviewFlag = false;
                    }
                }
            } else {
                $reviewFlag = false;
            }
        }

        return $reviewFlag;
    }

    /* function to set review flag - ends here */

    /* function to get sp services list - starts here */

    public function getSpserviceslist($api_url, $id)
    {
        $service_list = array();

        if ($id != '') {
            $url = $api_url . "/api/spusers/spservices/";
            $data = array('user_id' => $id);
            $res = $this->api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {
                $result = json_decode($res->getBody(), true);

                if (count($result['results']) > 0) {
                    $i = 0;
                    foreach ($result['results'] as $data) {
                        $service = json_decode($data['service'], true);
                        $service_list[$i]['id'] = $service['id'];
                        $service_list[$i]['service_name'] = $service['category_name'];
                        $i++;
                    }
                }
            }
        }

        return $service_list;
    }

    /* function to search on ip location basis 
     *  check wheather given IP belongs to canada or not
     *  get neighbourhood as well when parameter is passesd
     * */

    public function getLocationbyip($api_url, $remoteAddr, $neighbourhood = false)
    {
        $ipNumber = sprintf('%u', ip2long($remoteAddr));
        $ipNumber = ($ipNumber >= 4294967295) ? ($ipNumber - 1) : $ipNumber;
        $url = $api_url . "/api/iptolocation/";

        if ($neighbourhood) {
            /* return neighbourhood details */
            $data = array('ipNumber' => $ipNumber, 'distance' => 40);
        } else {
            /* return only the current location on the basis of IP */
            $data = array('ipNumber' => $ipNumber);
        }

        $res = $this->api->curl($url, $data, "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            if (count($content) > 0) {
                return $content;
            } else {
                return array();
            }
        }
    }

    /* get subscription */

    public function getSubscription($api_url, $id)
    {
        if ($id != '') {
            $url = $api_url . "/api/subscription/" . $id . "/";
            $res = $this->api->curl($url, array(''), "GET");

            if ($res->getStatusCode() == 200) {

                $content = json_decode($res->getBody(), true);

                if (count($content) > 0) {
                    return $content;
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

    /* Function to get visitor currency */

    public function getcurrency($api_url, $remoteAddr)
    {
        $result = $this->getLocationbyip($api_url, $remoteAddr);
        //print_r($result); exit;
        if ($result[0]['country_code'] == 'CAN' || $result[0]['country_code'] == 'CA') {
            return 'CAD';
        } else {
            return 'USD';
        }
    }

    /* Function to get service price */

    public function getServicePrice($api_url, $id)
    {
        $res = $this->api->curl($api_url . "/api/spusers/spservices/" . $id . "/", array(''), "GET");
        if ($res->getStatusCode() == 200) {

            $content = json_decode($res->getBody(), true);

            if (count($content) > 0) {
                $price = $content['price'];
            } else {
                $price = '0';
            }

            return $price;
        } else {
            return '0';
        }
    }

    /* Function to get logged in user address */

    public function getLoggedInUserAddress($loggedInUser, $userType, $api_url)
    {
        $loggedInUserAddress = array();

        if ($loggedInUser != "" && $userType != "") {

            $url = ($userType == 3) ? array("api_url" => $api_url . "/api/spusers/" . $loggedInUser . "/", "data_field" => "work_address") : array("api_url" => $api_url . "/api/users/" . $loggedInUser . "/", "data_field" => "address");

            $res = $this->api->curl($url['api_url'], array(''), "GET");

            if ($res->getStatusCode() == 200) {
                $res_data = json_decode($res->getBody(), true);
                $loggedInUserAddress = json_decode($res_data[$url['data_field']][0]);
            }
        }

        return $loggedInUserAddress;
    }

    /* Function to update service provider educations */

    public function updateEducations($api_url, $user_id, $educations = array())
    {
        $del_res = $this->api->curl($api_url . "/api/sprelateddata/", array('op' => "education", 'users_id' => $user_id), "DELETE");

        if (is_array($educations) && count($educations) > 0) {

            foreach ($educations as $education) {
                $user_educations[] = array('user_id' => $user_id, 'education_id' => $education);
            }

            $result = $this->api->curl($api_url . "/api/sprelateddata/", array('education' => json_encode($user_educations, true)), "POST");
            if ($result->getStatusCode() != 200 && $result->getStatusCode() != 201) {
                return false;
            }
        }
        return true;
    }

    /* Function to update service provider educations */

    public function updateLanguages($api_url, $user_id, $languages = array())
    {
        $del_res = $this->api->curl($api_url . "/api/sprelateddata/", array('op' => "language", 'users_id' => $user_id), "DELETE");

        if (is_array($languages) && count($languages) > 0) {

            foreach ($languages as $language) {
                $user_languages[] = array('user_id' => $user_id, 'language_id' => $language);
            }

            $result = $this->api->curl($api_url . "/api/sprelateddata/", array('language' => json_encode($user_languages, true)), "POST");
            if ($result->getStatusCode() != 200 && $result->getStatusCode() != 201) {
                return false;
            }
        }
        return true;
    }

    /* Function to add or update service provider details */

    public function updateDetails($api_url, $details_data, $detail_id = '')
    {
        if (count($details_data) > 0) {
            $detail_res = ($detail_id != "") ? $this->api->curl($api_url . "/api/spusers/details/" . $detail_id . "/", $details_data, "PUT") : $this->api->curl($api_url . "/api/spusers/details/", $details_data, "POST");
            $result = json_decode($detail_res->getBody(), true);

            if ($detail_res->getStatusCode() != 200 && $detail_res->getStatusCode() != 201) {
                return array('status' => 0, 'msg' => 'Failed to update profile..!!', 'errors' => $result);
            } else {
                return array('status' => 1, 'msg' => 'Profile successfully updated..!!', 'id' => $result['id']);
            }
        }
    }

    /* Function to add or update service provider contact details */

    public function updateContact($api_url, $contact_data, $contact_id = '')
    {
        if (count($contact_data) > 0) {
            $contact_res = ($contact_id != "") ? $this->api->curl($api_url . "/api/spusers/contact/" . $contact_id . "/", $contact_data, "PUT") : $this->api->curl($api_url . "/api/spusers/contact/", $contact_data, "POST");
            $result = json_decode($contact_res->getBody(), true);

            if ($contact_res->getStatusCode() != 200 && $contact_res->getStatusCode() != 201) {
                return array('status' => 0, 'msg' => 'Failed to update profile..!!', 'errors' => $result);
            } else {
                return array('status' => 1, 'msg' => 'Profile successfully updated..!!', 'id' => $result['id']);
            }
        }
    }

    /* Function to add or update service provider basic details */

    public function updateData($api_url, $user_data, $user_id = '')
    {
        if (count($user_data) > 0) {
            $user_res = $this->api->curl($api_url . "/api/spusers/" . $user_id . "/", $user_data, "PUT");

            if ($user_res->getStatusCode() != 200 && $user_res->getStatusCode() != 201) {
                return (is_array(json_decode($user_res->getBody(), true))) ? json_decode($user_res->getBody(), true) : array();
            } else {
                return true;
            }
        }
    }

    /* Function to get service duration */

    public function getServiceDurations($api_url, $service_id, $user_id, $duration = '')
    {
        $duration_list = array();
        $data = array('user_id' => $user_id, 'service_id' => $service_id);
        ($duration != '') ? $data['duration'] = $duration : '';
        $res = $this->api->curl($api_url . '/api/spusers/spservices/', $data, "GET");

        if ($res->getStatusCode() == 200) {

            $content = json_decode($res->getBody(), true);

            if (count($content['results']) > 0) {

                foreach ($content['results'] as $con) {
                    $duration_list[] = array('id' => $con['id'], 'duration' => $con['duration'] . ' Mins');
                }
            }

            return (count($duration_list) > 0) ? array('status' => 1, 'durations' => $duration_list) : array('status' => 0, 'msg' => 'Durations not defined for this service..!!');
        } else {
            return array('status' => 0, 'msg' => 'Failed to get duration for selected duration..!!');
        }
    }

    /* Function to add or update service provider address */

    public function updateSPAddress($api_url, $address_data, $address_id, $user_id)
    {
        if (isset($address_data['street1_address']) && isset($address_data['city']) && isset($address_data['city'])) {
            if (isset($address_data['country_id']) && $address_data['country_id'] != 0) {
                if (isset($address_data['state_id']) && $address_data['state_id'] != 0) {

                    $address_res = ($address_id != "") ? $this->api->curl($api_url . "/api/address/" . $address_id . "/", $address_data, "PUT") : $this->api->curl($api_url . "/api/address/", $address_data, "POST");
                    if ($address_res->getStatusCode() != 200 && $address_res->getStatusCode() != 201) {
                        $errors = (is_array(json_decode($address_res->getBody(), true))) ? json_decode($address_res->getBody(), true) : array();
                        return array('status' => 0, 'msg' => 'Failed to update address..!!', 'errors' => $errors);
                    }
                    $addressData = json_decode($address_res->getBody(), true);

                    return array('status' => 1, 'msg' => 'Address updated successfully..!!', 'id' => $addressData['id'], 'addresses' => $this->getSPaddresses($api_url, $user_id));
                } else {
                    return array('status' => 0, 'msg' => 'Please select a state');
                }
            } else {
                return array('status' => 0, 'msg' => 'Please select a country');
            }
        } else {
            return array('status' => 0, 'msg' => 'Please fill all the fields to add or update address..!!');
        }
    }

    /* Function to delete service rendering address */

    public function deleteSPAddress($api_url, $address_id, $user_id)
    {
        $del_res = $this->api->curl($api_url . "/api/address/" . $address_id . "/", array('user_type' => "sp"), "DELETE");

        if ($del_res->getStatusCode() != 200 && $del_res->getStatusCode() != 204) {
            return array('status' => '0', 'msg' => 'Failed to delete workplace address..!!', 'errors' => json_decode($del_res->getBody(), true), 'addresses' => $this->getSPaddresses($api_url, $user_id));
        }
        return array('status' => '1', 'msg' => 'Workplace address deleted successfully..!!', 'addresses' => $this->getSPaddresses($api_url, $user_id));
    }

    /* Function to get service provider feedback */

    public function getSPfeedback($id, $api_url, $page = '', $itemsPerPage = '', $status_id = 9)
    {
        $data_feedback = array('user_id' => $id, 'status_id' => $status_id);
        ($page != "") ? $data_feedback['page'] = $page : '';
        ($itemsPerPage != "") ? $data_feedback['no_of_records'] = $itemsPerPage : '';
        $res_feedback = $this->api->curl($api_url . "/api/feedback/", $data_feedback, "GET");

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

    /* Function to get service provider varification data */

    public function getSPvarification($id, $api_url)
    {
        $verification_list = array();
        $data = array('user_id' => $id);

        $res = $this->api->curl($api_url . "/api/userverification/", $data, "GET");
        $cert_res = $this->api->curl($api_url . "/api/users/certification/", $data, "GET");

        if ($res->getStatusCode() == 200) {

            $content = json_decode($res->getBody(), true);

            if (count($content) > 0) {
                $i = 0;
                foreach ($content as $key => $value) {
                    $varification_type = json_decode($value['verification_type'], true);

                    if ($varification_type['id'] == "1") {
                        //Email
                        $verification_list['email'] = $value;
                    } else if ($varification_type['id'] == "2") {
                        //Phone
                        $verification_list['phone'] = $value;
                    } else {
                        $verification_list[$i] = $value;
                    }
                    $i++;
                }
            }
        }

        if ($cert_res->getStatusCode() == 200) {
            $content = json_decode($cert_res->getBody(), true);
            //echo '<pre>'; print_r($content); exit;

            if (isset($content[0])) {
                $verification_list['membership_title'] = $content[0]['organization_name'];
                $verification_list['licence_number'] = $content[0]['professional_licence_number'];
                switch ($content[0]['status_id']) {
                    case '12' :
                        $verification_list['licence'] = $verification_list['membership'] = 'Verified';
                        break;
                    case '13' :
                        $verification_list['licence'] = $verification_list['membership'] = 'Verification under process';
                        break;
                    case '14' :
                        $verification_list['licence'] = $verification_list['membership'] = 'Non-Verified';
                        break;
                }
            } else {
                $verification_list['licence'] = $verification_list['licence_number'] = $verification_list['membership'] = $verification_list['membership_title'] = '';
            }
        } else {
            $verification_list['licence'] = $verification_list['licence_number'] = $verification_list['membership'] = $verification_list['membership_title'] = '';
        }

        $emailStatus = $verification_list['email']['verification_status'];
        $phoneStatus = $verification_list['phone']['verification_status'];
        $licenceStatus = ($verification_list['licence'] == 'Verified') ? '1' : '0';
        $membershipStatus = ($verification_list['membership'] == 'Verified') ? '1' : '0';
        $verification_list['verified'] = ($emailStatus == '1' && $phoneStatus == '1' && $licenceStatus == '1' && $membershipStatus == '1') ? 1 : 0;

        return $verification_list;
    }

    /* Function to add or edit service provider services */

    public function updateServices($api_url, $user_id, $service_id, $duration, $price, $action = 'add', $sp_edit_id = '')
    {
        $result = $this->getServiceDurations($api_url, $service_id, $user_id, $duration);
        if ($result['status'] == 1) {
            if ($sp_edit_id != '') {
                foreach ($result['durations'] as $value) {
                    if ($value['id'] != $sp_edit_id) {
                        return array('status' => 0, 'msg' => 'Service already exist with this duration..!!');
                    }
                }
            } else {
                return array('status' => 0, 'msg' => 'Service already exist with this duration..!!');
            }
        }

        $data = array('user_id' => $user_id, 'service_id' => $service_id, 'duration' => $duration, 'price' => $price, 'status_id' => 1);
        $res = ($action == "edit" && $sp_edit_id != '') ? $this->api->curl($api_url . "/api/spusers/spservices/" . $sp_edit_id . "/", $data, "PUT") : $this->api->curl($api_url . "/api/spusers/spservices/", $data, "POST");

        if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
            $msg = ($action == "edit" && $sp_edit_id != '') ? 'Service successfully updated..!!' : 'Service successfully added..!!';
            return array('status' => 1, 'msg' => $msg);
        } else {
            $msg = ($action == "edit" && $sp_edit_id != '') ? 'Failed to update service..!!' : 'Failed to add service..!!';
            $errors = json_decode($res->getBody(), true);
            return array('status' => 0, 'msg' => $msg, 'errors' => $errors);
        }
    }

    public function userlistByFilterData($data, $api_url, $remoteAddr)
    {
        $response_data = array();
        if (count($data) > 0) {
            $id = ($data['id'] != 0) ? $data['id'] : null;
            ($data['next'] != '') ? $next = $data['next'] : $next = '';
            ($data['practitioners_name'] != '') ? $practitioners_name = $data['practitioners_name'] : $practitioners_name = '';
            ($data['company_name'] != '') ? $company_name = $data['company_name'] : $company_name = '';
            ($data['paginate'] != '') ? $paginate = $data['paginate'] : $paginate = false;
            ($data['service_id'] != '') ? $service_id = $data['service_id'] : $service_id = '';
            ($data['avg_rating'] != '') ? $avg_rating = $data['avg_rating'] : $avg_rating = '';
            ($data['days_id'] != '') ? $days_id = $data['days_id'] : $days_id = '';
            //($data['country_id'] != '') ? $country_id = $data['country_id'] : $country_id = '';
            //($data['state_id'] != '') ? $state_id = $data['state_id'] : $state_id = '';
            /* ($data['country'] != '') ? $country = $data['country'] : $country = '';
              ($data['state'] != '') ? $state = $data['state'] : $state = '';
              ($data['county'] != '') ? $county = $data['county'] : $county = ''; */
            $country = $state = $county = '';  // this line added and above 3 lines are commented for task OVE-205 point 3c
            ($data['city'] != '') ? $city = $data['city'] : $city = '';
            ($data['zip_code'] != '') ? $zip_code = $data['zip_code'] : $zip_code = '';
            ($data['association_member'] != '') ? $association_member = $data['association_member'] : $association_member = '';
            ($data['maxPrice'] != '') ? $maxPrice = $data['maxPrice'] : $maxPrice = '0';
            ($data['minPrice'] != '') ? $minPrice = $data['minPrice'] : $minPrice = '0';
            ($data['treatmentLength'] != '') ? $treatmentLength = $data['treatmentLength'] : $treatmentLength = '';
            ($data['booking_no'] != '') ? $booking_no = $data['booking_no'] : $booking_no = '';
            ($data['price_filter'] != '') ? $price_filter = $data['price_filter'] : $price_filter = '';
            ($data['locationType'] != '') ? $location_type = $data['locationType'] : $location_type = '';
            ($data['feedback_filter'] != '') ? $feedback_filter = $data['feedback_filter'] : $feedback_filter = '';
            //($data['distance'] != '') ? $distance = round($data['distance'] * 0.621371) : $distance = '';
            ($data['distance'] != '') ? $distance = $data['distance'] : $distance = '';
            ($data['treatment_for_physically_disabled_person'] != '') ? $treatment_for_physically_disabled_person = $data['treatment_for_physically_disabled_person'] : $treatment_for_physically_disabled_person = '';
            ($data['auth_to_issue_insurence_rem_receipt'] != '') ? $auth_to_issue_insurence_rem_receipt = $data['auth_to_issue_insurence_rem_receipt'] : $auth_to_issue_insurence_rem_receipt = '';

            $ipNumber = sprintf('%u', ip2long($remoteAddr));
            $ipNumber = ($ipNumber >= 4294967295) ? ($ipNumber - 1) : $ipNumber;

            if ($service_id != '') {
                $service_id = implode(',', $service_id);
            } else {
                $service_id = '';
            }

            if ($avg_rating != '') {
                $avg_rating = implode(',', $avg_rating);
            } else {
                $avg_rating = '';
            }

            if ($days_id != '') {
                $days_id = implode(',', $days_id);
            } else {
                $days_id = '';
            }

            if ($location_type != '') {
                $location_type = implode(',', $location_type);
            }
        } else {
            $next = '1';
            $paginate = false;
        }
        if ($next != '') {

            $session = new Container('frontend');
            //$exclude_user = ($session->user_type_id == 3) ? $session->userid : '';   // To exclude current logged in user
            $exclude_user = '';

            /* $paramData = array('page' => $next, 'service_id' => $service_id, 'auth_to_issue_insurence_rem_receipt' => $auth_to_issue_insurence_rem_receipt, 'days_id' => $days_id,
              'avg_rating' => $avg_rating, 'practitioners_name' => $practitioners_name, 'company_name' => $company_name, 'booking_no' => $booking_no,
              'price_filter' => $price_filter, 'feedback_filter' => $feedback_filter, 'ipNumber' => $ipNumber, 'distance' => $distance,
              'association_member' => $association_member, 'country_id' => $country_id, 'state_id' => $state_id, 'city' => $city, 'maxPrice' => $maxPrice,
              'minPrice' => $minPrice, 'zip_code' => $zip_code, 'treatmentLength' => $treatmentLength, 'treatment_for_physically_disabled_person' => $treatment_for_physically_disabled_person,
              'id' => $id, 'exclude_user' => $exclude_user, 'location_type' => $location_type); */

            $paramData = array('page' => $next, 'service_id' => $service_id, 'auth_to_issue_insurence_rem_receipt' => $auth_to_issue_insurence_rem_receipt, 'days_id' => $days_id,
                'avg_rating' => $avg_rating, 'practitioners_name' => $practitioners_name, 'company_name' => $company_name, 'booking_no' => $booking_no,
                'price' => $price_filter, 'feedback_filter' => $feedback_filter, 'ipNumber' => $ipNumber, 'distance' => $distance,
                'association_member' => $association_member, 'country_name' => $country, 'state_name' => $state, 'county' => $county, 'city' => $city, 'maxPrice' => $maxPrice,
                'minPrice' => $minPrice, 'zip_code' => $zip_code, 'treatmentLength' => $treatmentLength, 'treatment_for_physically_disabled_person' => $treatment_for_physically_disabled_person,
                'id' => $id, 'exclude_user' => $exclude_user, 'location_type' => $location_type);

            /* sp availability parameter code start here */
            if (trim($data['weeDay']) != '') {
                $request_data['days_id'] = (trim($data['weeDay']) != '') ? $data['weeDay'] : '';
                (trim($data['startTime']) != '') ? $request_data['start_time'] = $data['startTime'] : '';
                (trim($data['endTime']) != '') ? $request_data['end_time'] = $data['endTime'] : '';

                $dateTime_res = $this->api->curl($api_url . '/api/sp_availability/', $request_data, "GET");
                $sp_availability = '';
                if ($dateTime_res->getStatusCode() == 200) {
                    $response = json_decode($dateTime_res->getBody(), true);
                    if (count($response) > 0) {
                        foreach ($response as $value) {
                            $response_data[] = $value['user_id'];
                        }
                        $sp_availability = implode(',', array_unique($response_data));
                    }
                }
                $paramData['sp_availability'] = $sp_availability;
            }
            /* sp availability parameter code ends here */

            $this->getSplistByFilter($paramData, $api_url);
        }
    }

    public function getSplistByFilter($data, $api_url)
    {
        if ($data['service_id'] == "" && $data['auth_to_issue_insurence_rem_receipt'] == "" && $data['days_id'] == "" && $data['avg_rating'] == "" &&
                $data['practitioners_name'] == "" && $data['company_name'] == "" && $data['booking_no'] == "" && $data['price'] == "" && $data['feedback_filter'] == "" &&
                $data['distance'] == "" && $data['association_member'] == "" && $data['country_id'] == "" && $data['state_id'] == "" && $data['city'] == "" && $data['maxPrice'] == "0" &&
                $data['minPrice'] == "0" && $data['zip_code'] == "" && $data['treatmentLength'] == "" && $data['treatment_for_physically_disabled_person'] == "" && $data['id'] == "" &&
                $data['exclude_user'] == "" && isset($data['sp_availability']) && $data['sp_availability'] == "" && $data['location_type'] == "") {
            $data['date_range_filter'] = "1";
        }

        $zip_code = $data['zip_code'];
        $data['status_id'] = 9;

        $sp_list = array();
        $bookingModel = new Bookings();
        $res = $this->api->curl($api_url . "/api/spusers/", $data, "GET");

        if ($res->getStatusCode() == 200) {

            $content = json_decode($res->getBody(), true);

            isset($content['next']) ? $sp_list['next'] = $content['next'] : $sp_list['next'] = '';
            isset($content['count']) ? $sp_list['count'] = $content['count'] : $sp_list['count'] = '';

            if (count($content['results']) > 0) {

                $i = 0;
                $distance = array();

                foreach ($content['results'] as $data) {
                    $sp_list['result'][$i]['id'] = $data['id'];
                    $sp_list['result'][$i]['first_name'] = $data['first_name'];
                    $sp_list['result'][$i]['last_name'] = $data['last_name'];
                    $sp_list['result'][$i]['avtar_url'] = $data['avtar_url'];
                    $sp_list['result'][$i]['email'] = $data['email'];
                    $sp_list['result'][$i]['user_type_id'] = $data['user_type_id'];
                    $sp_list['result'][$i]['login_user_type_id'] = $userType;
                    $sp_list['result'][$i]['rating'] = $this->getSPRatings($data['id'], $api_url);

                    if (count($data['details']) > 0) {

                        foreach ($data['details'] as $sp_details) {
                            $details = json_decode($sp_details, true);
                            $sp_list['result'][$i]['years_of_experience'] = $details['years_of_experience'];
                            $sp_list['result'][$i]['degrees'] = $details['degrees'];
                            $sp_list['result'][$i]['specialties'] = $details['specialties'];
                            $sp_list['result'][$i]['work_days'] = $this->getSPWorkdays($data['id'], $api_url);
                        }
                    }

                    /* Calculating nearest distance starts here */
                    $sp_list['result'][$i]['distance'] = "NA";

                    if (count($data['work_address']) > 0 && $zip_code != '') {

                        $all_distances = array();
                        foreach ($data['work_address'] as $raw_add) {
                            $address = json_decode($raw_add, true);
                            $zips = array('consumer_zip' => $zip_code, 'practitioner_zip' => $address['zip_code']);
                            $dist = $this->getDistance($api_url, $zip_code, $address['zip_code']);
                            (is_numeric($dist)) ? $all_distances[] = round($dist) : '';
                        }

                        $sp_list['result'][$i]['distance'] = (count($all_distances) > 0) ? min($all_distances) : "NA";
                        $distance[$i] = (is_numeric($sp_list['result'][$i]['distance'])) ? $sp_list['result'][$i]['distance'] : 0;
                    }
                    /* Calculating nearest distance ends here */

                    /* Code added by Ritesh starts here */
                    $verification_details = $this->getSPvarification($data['id'], $api_url);
                    
                    $sp_list['result'][$i]['verified'] = $verification_details['verified'];

                    /* Code added by Ritesh ends here */

                    if (count($data['contact']) > 0) {

                        foreach ($data['contact'] as $sp_contacts) {
                            $details = json_decode($sp_contacts, true);
                            $sp_list['result'][$i]['cellphone'] = $details['cellphone'];
                        }
                    }

                    $spPrice = array();
                    $price_arr = array();

                    if (count($data['service']) > 0) {
                        foreach ($data['service'] as $value) {
                            $spPrice[] = json_decode($value, true);
                        }
                    }

                    if (count($spPrice) > 0) {
                        foreach ($spPrice as $value) {
                            $price_arr[] = $value['price'];
                        }
                    }

                    (count($price_arr) > 0) ? $sp_list['result'][$i]['price'] = min($price_arr) : $sp_list['result'][$i]['price'] = '';
                    //$sp_list['result'][$i]['price'] = min($sp_price);

                    $sp_list['result'][$i]['bookings_count'] = $bookingModel->getBookingsCount($api_url, $data['id']);
                    $sp_list['result'][$i]['reviews_count'] = $this->getReviewscount($data['id'], $api_url);
                    $i++;
                }

                //echo '<pre>'; print_r($distance); //exit;

                if (count($distance) > 0) {
                    $sortedDistance = $distance;
                    sort($sortedDistance, SORT_NUMERIC);
                    $sp_list['result'] = $this->sortArrayByArray($sp_list['result'], $sortedDistance, $distance);
                }

                //echo '<pre>'; print_r($sp_list['result']); exit;

                if ($sp_list['next'] != '') {
                    $str = str_replace("page", "@!##", $sp_list['next']);
                    $str_arr = explode('@!##=', $str);
                    $next = explode('&', $str_arr[1]);
                    $sp_list['next'] = $next[0];
                }

                echo json_encode($sp_list);
                exit;
            } else {
                $sp_list = '';
                echo json_encode($sp_list); // no data found
                exit;
            }
        } else {
            $sp_list = array(
                'next' => '',
                'count' => '',
                'result' => '',
            );

            echo json_encode($sp_list);
            exit;
        }
    }

    /* Function to sort an array according to another array */

    function sortArrayByArray(Array $array, Array $orderArray, Array $referranceArray)
    {
        $ordered = array();
        foreach ($orderArray as $key => $value) {
            if (array_key_exists($key, $array)) {
                $ordered[] = $array[array_search($value, $referranceArray)];
                //unset($array[array_search($value, $referranceArray)]);
                unset($referranceArray[array_search($value, $referranceArray)]);
            }
        }

        return $ordered;
    }

    public function getReviewscount($id, $api_url)
    {

        if ($id != '') {

            $url = $api_url . "/api/feedback/";
            $data = array('user_id' => $id);
            $res = $this->api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {
                $feedback = json_decode($res->getBody(), true);
                return count($feedback['results']);
            } else {
                return "0";
            }
        } else {
            return "0";
        }
    }

    /* Function to get contact details */

    public function getContact($api_url, $id)
    {
        $result = $this->api->curl($api_url . '/api/spusers/contact/', array('user_id' => $id), "GET");

        if ($result->getStatusCode() == 200) {
            return json_decode($result->getBody(), true);
        } else {
            return false;
        }
    }

    /* Function to get all organizations */

    public function getOrganizations($api_url)
    {
        $result = $this->api->curl($api_url . '/api/sporganization/', array('status_id' => 1), "GET");

        if ($result->getStatusCode() == 200) {
            return json_decode($result->getBody(), true);
        } else {
            return false;
        }
    }

    /* Function to get service provider organization */

    public function getSPOrganization($api_url, $user_id)
    {
        $res = $this->api->curl($api_url . '/api/sporganizationlookup/', array('practitioner_id' => $user_id), "GET");

        if ($res->getStatusCode() == 200) {
            $result = json_decode($res->getBody(), true);
            return isset($result[0]['organization']) ? json_decode($result[0]['organization'], true) : array();
        } else {
            return false;
        }
    }

    /* Function to add or update organization */

    public function updateOrganization($api_url, $user_id, $org_id)
    {
        $res = $this->api->curl($api_url . '/api/sporganizationlookup/', array('practitioner_id' => $user_id), "GET");

        if ($res->getStatusCode() == 200) {
            $result = json_decode($res->getBody(), true);
            if (isset($result[0]['id'])) {
                $update_res = $this->api->curl($api_url . '/api/sporganizationlookup/' . $result[0]['id'] . '/', array('practitioner_id' => $user_id, 'organization_id' => $org_id), "PUT");

                if ($update_res->getStatusCode() == 200 || $update_res->getStatusCode() == 201) {
                    return array('status' => 1, 'msg' => 'Organization successfully updated..!!');
                } else {
                    return array('status' => 0, 'msg' => 'Failed to update organization..!!', 'errors' => json_decode($update_res->getBody(), true));
                }
            } else {
                $add_res = $this->api->curl($api_url . '/api/sporganizationlookup/', array('practitioner_id' => $user_id, 'organization_id' => $org_id), "POST");
                if ($add_res->getStatusCode() == 200 || $add_res->getStatusCode() == 201) {
                    return array('status' => 1, 'msg' => 'Organization successfully updated..!!');
                } else {
                    return array('status' => 0, 'msg' => 'Failed to update organization..!!', 'errors' => json_decode($add_res->getBody(), true));
                }
            }
        } else {
            return array('status' => 0, 'msg' => 'Unable to update organization..!!', 'errors' => json_decode($res->getBody(), true));
        }
    }

    /* Funtion to return user feature */

    public function getSPoptions($id, $api_url)
    {

        $url_options = $api_url . "/api/userfeaturesetting/" . $id . "/";
        $data_options = array('');
        $res_options = $this->api->curl($url_options, $data_options, "GET");

        if ($res_options->getStatusCode() == 200) {

            $content_options = json_decode($res_options->getBody(), true);

            if (count($content_options) > 0) {

                foreach ($content_options as $key => $value) {
                    $options[$key] = $value;
                }
            }
        } else {
            $options = array();
        }

        return $options;
    }

    /* Function to get location types */

    public function getLocationTypes($api_url)
    {
        $res = $this->api->curl($api_url . '/api/location-type/', array(), 'GET');

        if ($res->getStatusCode() == 200) {
            $results = json_decode($res->getBody(), true);
            return (isset($results) && is_array($results)) ? $results : array();
        } else {
            return array();
        }
    }

    /* Function to update service provider location type */

    public function updateLocationTypes($api_url, $user_id, $locations = array())
    {
        $del_res = $this->api->curl($api_url . "/api/sprelateddata/", array('op' => "location", 'users_id' => $user_id), "DELETE");

        if (is_array($locations) && count($locations) > 0) {

            foreach ($locations as $location) {
                $user_locations[] = array('user_id' => $user_id, 'location_type_id' => $location);
            }

            $result = $this->api->curl($api_url . "/api/sprelateddata/", array('location' => json_encode($user_locations, true)), "POST");
            if ($result->getStatusCode() != 200 && $result->getStatusCode() != 201) {
                return array('status' => 0, 'msg' => 'Failed to update service location types..!!', 'errors' => json_decode($result->getBody(), true));
            } else {
                return array('status' => 1, 'msg' => 'Service location types updated successfully..!!');
            }
        }
        return array('status' => 0, 'msg' => 'Please select atleast 1 location type to update..!!');
    }

    /* Function to get contacted practitioner list */

    public function getCPlist($api_url, $incldSuspnded = true)
    {
        $session = new Container('frontend');
        $lists = array();
        $uniqueId = array();
        $getSrvConsumer = $this->getServiceConsumers($api_url, $session->userid, '', '', false);
        //print_r($getSrvConsumer); die; 

        if (count($getSrvConsumer) > 0) {
            foreach ($getSrvConsumer as $value) {
                if (!in_array($value['id'], $uniqueId)) {
                    $lists[] = array('id' => $value['id'], 'name' => $value['name']);
                    array_push($uniqueId, $value['id']);
                }
            }
        }// die;

        $filter = array();
        $filter['from_user_id'] = $session->userid;
        $filter['to_user_id'] = $session->userid;
        $filter['deleteFlag'] = 0;
        ($incldSuspnded == false) ? $filter['suspended'] = 1 : '';

        $res = $this->api->curl($api_url . '/api/messages/', $filter, "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            //echo '<pre>';  print_r($content); exit;
            foreach ($content as $user) {
                $id = ($user['from_user_id'] == $session->userid) ? $user['to_user_id'] : $user['from_user_id'];
                $status = ($user['from_user_id'] == $session->userid) ? $user['to_user_status'] : $user['from_user_status'];
                // Get unique data
                if (!in_array($id, $uniqueId) && $status != 10) {
                    $getDetails = ($user['from_user_id'] == $session->userid) ? $user['to_user_details'] : $user['from_user_details'];
                    if ($getDetails != '' && count($getDetails) > 0) {
                        $details = json_decode($getDetails, true);
                        //foreach ($details as $contacts) {
                        $name = $details['first_name'] . ' ' . $details['last_name'];
                        // }
                        $lists[] = array('id' => $id, 'name' => $name);
                    }

                    array_push($uniqueId, $id);
                }
            }
        }
        //echo '<pre>';  print_r($lists); 
        //exit;
        return $lists;
    }

    /* Function to get service media */

    public function getSPMedia($api_url, $user_id, $media_type = 1)
    {
        $res = $this->api->curl($api_url . '/api/media/', array('user_id' => $user_id, 'media_type' => $media_type), 'GET');

        if ($res->getStatusCode() == 200) {
            $result = json_decode($res->getBody(), true);
            return $result['results'];
        }
    }

    /* Function to update service provider media */

    public function updateSPMedia($api_url, $videoData, $media_type = 1, $id = '')
    {
        $videoData['media_type'] = $media_type;
        $file = ($media_type == 2) ? 'Video' : 'Image';

        if ($id != "") {
            $res = $this->api->curl($api_url . "/api/media/" . $id . "/", $videoData, "PUT");
        } else {
            $res = $this->api->curl($api_url . "/api/media/", $videoData, "POST");
        }

        if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
            $upload_res = json_decode($res->getBody(), true);
            return array('status' => 1, 'msg' => $file . ' uploaded successfully..!!', 'id' => $upload_res['id'], 'title' => $upload_res['media_title'], 'media_description' => $upload_res['media_description'], 'url' => $upload_res['media_url']);
        } else {
            return array('status' => 0, 'msg' => 'Failed to upload ' . $file . '..!!', 'errors' => json_decode($res->getBody(), true));
        }
    }

    /* Function to find address */

    public function getAddress($api_url, $filter, $fields = array())
    {
        $response = $this->api->curl($api_url . '/api/search_city/', $filter, "GET");

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            $addresses = array();
            foreach ($data['results'] as $address) {
                $add = (in_array('city_name', $fields) && isset($address['city_name']) && $address['city_name'] != "none" && $address['city_name'] != null) ? $address['city_name'] . ', ' : '';
                $add .= (in_array('county_name', $fields) && isset($address['county_name']) && $address['county_name'] != "none" && $address['county_name'] != null) ? $address['county_name'] . ', ' : '';
                $add .= (in_array('region_name', $fields) && isset($address['region_name']) && $address['region_name'] != "none" && $address['region_name'] != null) ? $address['region_name'] . ', ' : '';
                $add .= (in_array('zip_code', $fields) && isset($address['zip_code']) && $address['zip_code'] != "none" && $address['zip_code'] != null) ? $address['zip_code'] . ', ' : '';
                $add .= (in_array('country_name', $fields) && isset($address['country_name']) && $address['country_name'] != "none" && $address['country_name'] != null) ? $address['country_name'] : '';

                ($add != '') ? $addresses[] = trim($add, ', ') : '';
            }
            return $addresses;
        }
    }

    /* Function to add subscription */

    public function addSubscription($api_url, $data)
    {
        $response = $this->api->curl($api_url . '/api/usersubscription/', $data, "POST");

        if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
            return true;
        } else {
            return false;
        }
    }

    /* Function to find service providers */

    public function getServiceProviders($api_url, $data)
    {
        $response = $this->api->curl($api_url . '/api/spusers/', $data, "GET");

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            $practitioners = array();
            foreach ($data['results'] as $sp) {
                $practitioners[$sp['id']] = $sp['first_name'] . ' ' . $sp['last_name'];
            }
            return $practitioners;
        }
    }

    /* Function to get service provider addresses */

    public function getSPaddresses($api_url, $user_id)
    {
        $userDetails = $this->getSPDetails($api_url, $user_id);
        $addresses = array();

        if (isset($userDetails['address']) && is_array($userDetails['address']) && count($userDetails['address'])) {
            foreach ($userDetails['address'] as $data) {
                $addresses[] = json_decode($data, true);
            }
        }

        if (isset($userDetails['work_address']) && is_array($userDetails['work_address']) && count($userDetails['work_address'])) {
            foreach ($userDetails['work_address'] as $data) {
                $addresses[] = json_decode($data, true);
            }
        }

        return $addresses;
    }

    /* Function to change service status */

    public function changeServiceStatus($api_url, $user_id, $service_ids, $status)
    {
        $data = array();
        $data['user_id'] = $user_id;
        $data['status_id'] = $status;

        if (is_array($service_ids) && count($service_ids) > 0) {
            foreach ($service_ids as $key => $value) {
                $data['duration'] = $value[0];
                $data['service_id'] = $value[1];
                $res = $this->api->curl($api_url . "/api/spusers/spservices/" . $key . "/", $data, "PUT");

                if ($res->getStatusCode() != 200 && $res->getStatusCode() != 201) {
                    return array('status' => 0, 'msg' => 'Failed to update all services..!!', 'errors' => json_decode($res->getBody(), true));
                }
            }

            return array('status' => 1, 'msg' => 'Services updated successfully..!!');
        } else {
            return array('status' => 0, 'msg' => 'Please select services to updated..!!');
        }
    }

    /* Function to read notifications */

    public function readNotifications($api_url, $tab, $userid = '')
    {
        $data = array();

        switch ($tab) {
            case 'reference' :
                ($userid != '') ? $data['user_id'] = $userid : '';
                $data['view_status'] = 1;
                $res = $this->api->curl($api_url . "/api/sp/reference/0/", $data, "PUT");

                return ($res->getStatusCode() == '200' || $res->getStatusCode() == '201') ? true : false;
                break;

            case 'reviews' :
                ($userid != '') ? $data['user_id'] = $userid : '';
                $data['view_status'] = 1;
                $res = $this->api->curl($api_url . "/api/feedback/", $data, "POST");

                return ($res->getStatusCode() == '200' || $res->getStatusCode() == '201') ? true : false;
                break;
        }
        return false;
    }

    /* Function to get practitioners response rate */

    public function getResponseRate($api_url, $id)
    {
        $response = array('rate' => 0, 'time' => 0);
        $filter = array('service_provider_id' => $id);
        $res = $this->api->curl($api_url . "/api/response_rate/", $filter, "GET");

        if ($res->getStatusCode() == "200") {
            $response['rate'] = json_decode($res->getBody(), true);
        }

        return $response;
    }

    /* Function to get distance between 2 zip codes */

    public function getDistance($api_url, $from_zip, $to_zip)
    {
        $distance = 0;
        if ($from_zip != '' && $to_zip != '') {

            $filter = array('consumer_zip' => $from_zip, 'practitioner_zip' => $to_zip);
            $res = $this->api->curl($api_url . "/api/distance/", $filter, "GET");

            if ($res->getStatusCode() == "200") {
                $content = json_decode($res->getBody(), true);
                $distance = (isset($content[0]['distance']) && is_numeric($content[0]['distance'])) ? round($content[0]['distance']) : 'NA';
            }
        }

        return $distance;
    }

}
