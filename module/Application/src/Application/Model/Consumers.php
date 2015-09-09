<?php

namespace Application\Model;

use Application\Model\Api;
use Zend\Session\Container;

class Consumers
{

    private $api;

    public function __construct()
    {
        $this->api = new Api();
    }

    /* Function to get consumer details */

    public function getConsumerdetails($api_url, $id)
    {
        $res = $this->api->curl($api_url . '/api/users/' . $id . '/', array(), "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            return $content;
        } else {
            return false;
        }
    }

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

    /* Function to get contact details */

    public function getContact($api_url, $id)
    {
        $result = $this->api->curl($api_url . '/api/users/contact/', array('user_id' => $id), "GET");

        if ($result->getStatusCode() == 200) {
            return json_decode($result->getBody(), true);
        } else {
            return false;
        }
    }
    
    /* Function to get contacted practitioners */
    
    public function getContactedListCount($id, $api_url)
    {
        $url = $api_url . "/api/messages/";
        $data = array('from_user_id' => $id);
        $res = $this->api->curl($url, $data, "GET");

        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            (count($content) > 0) ? $count = count($content) : $count = '0';
        } else {
            $count = '0';
        }
        return $count;
    }

}
