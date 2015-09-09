<?php

/**
 * Description of ConsumerController
 *
 * @author adarshkumar
 */

namespace Application\Model;

use Application\Model\Api;

class Wishlists
{

    private $api;

    public function __construct()
    {
        $this->api = new Api();
    }

    public function getwishlists($data, $api_url)
    {
        $wishlists = array();

        $wishlist_res = $this->api->curl($api_url . "/api/wishlist/", $data, "GET");
        if ($wishlist_res->getStatusCode() == 200) {
            $wishlists = json_decode($wishlist_res->getBody(), true);
            return $wishlists;
        } else {
            return false;
        }
    }

    /* Function to get email template */

    public function emailTemplate($api_url, $id)
    {
        $res = $this->api->curl($api_url . '/api/emailtemplate/' . $id . '/', array(), "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            return $content;
        } else {
            return false;
        }
    }

    /* Function to get service provider details */

    public function getCustomerDetails($api_url, $id)
    {
        $res = $this->api->curl($api_url . '/api/users/' . $id . '/', array(), "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            return $content;
        } else {
            return false;
        }
    }

}
