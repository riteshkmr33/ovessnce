<?php

/**
 * Api.php
 * @author Bade lal <badelalk@clavax.us><badelal143@gmail.com>
 * @package API
 */

namespace Application\Model;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client as HttpClient;

class Api extends AbstractActionController
{

    public function curl($url, array $params, $method = "POST")
    {

        $client = new HttpClient();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');

        $client->setUri($url);

        $client->setOptions(array(
            'maxredirects' => 0,
            'timeout' => 30
        ));

        $client->setMethod($method);

        $client->setHeaders(array(
            'username: apiuser',
            'password: 123456',
        ));

        //if(!empty($params)) {
        if ($method == "POST" || $method == "PUT" || $method == "DELETE") {
            $client->setParameterPOST($params);
        } else {
            $client->setParameterGET($params);
        }
        //}

        $response = $client->send();
        return $response;
    }

    public function curlUpdate($url, array $params, $method = "PUT")
    {
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        return $response = curl_exec($ch);
    }

    public function getCommonData($api_url)
    {
        $data = array();
        $res = $this->curl($api_url, array(), "GET");
        if ($res->getStatusCode() == 200) {
            $res_content = json_decode($res->getBody(), true);
            $data['services'] = $res_content;
        }
        return $data;
    }

}
