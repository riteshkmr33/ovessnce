<?php

/**
 * Description of Messages Model :
 * 
 * this model is for retriveing the list of 
 * inbox , sent	and trash messages for both
 * consumer and service provider users.
 * 
 * @author <piyush@clavax.us>
 */

namespace Application\Model;

use Application\Model\Api;

class Messages
{

    private $api;

    public function __construct()
    {
        $this->api = new Api();
    }

    public function getMessages($api_url, $id, $user_type_id, $page, $action, $no_of_records = 10)
    {
        $result = array();
        !empty($page) ? $page : $page = 1;

        if ($action == "inbox") {
            if ($user_type_id == "3") {
                // practitioner user
                $data = array('to_user_id' => $id, 'deleteFlag_p' => '0', 'page' => $page, 'no_of_records' => $no_of_records);
            } else {
                // consumer user
                $data = array('to_user_id' => $id, 'deleteFlag_c' => '0', 'page' => $page, 'no_of_records' => $no_of_records);
            }
        } else if ($action == "sent") {

            if ($user_type_id == "3") {
                // practitioner user
                $data = array('from_user_id' => $id, 'deleteFlag_p' => '0', 'page' => $page, 'no_of_records' => $no_of_records);
            } else {
                // consumer user
                $data = array('from_user_id' => $id, 'deleteFlag_c' => '0', 'page' => $page, 'no_of_records' => $no_of_records);
            }
        } else if ($action == "trash") {

            if ($user_type_id == "3") {
                // practitioner user
                $data = array('to_user_id' => $id, 'from_user_id' => $id, 'deleteFlag_p' => '1', 'page' => $page, 'no_of_records' => $no_of_records);
            } else {
                // consumer user
                $data = array('to_user_id' => $id, 'from_user_id' => $id, 'deleteFlag_c' => '1', 'page' => $page, 'no_of_records' => $no_of_records);
            }
        } else {
            $data = array('');
        }

        $res = $this->api->curl($api_url . "/api/messages/", $data, "GET");

        if ($res->getStatusCode() == 200) {
            $result = json_decode($res->getBody(), true);
        } else {
            $result = array();
        }

        return $result;
        exit;
    }

}
