<?php

namespace Application\Model;

use Application\Model\Api;
use Application\Model\Common;
use Zend\Session\Container;

class Transactions
{

    private $api;

    public function __construct()
    {
        $this->api = new Api();
    }

    /* Function to get user subscription transactions */

    public function getUserSubscriptionTransactions($api_url, $id)
    {
        $params = array();
        ($id != "") ? $params['user_id'] = $id : '';
        ($page != "") ? $params['page'] = $page : '';
        ($recordsPerPage != "") ? $params['no_of_records'] = $recordsPerPage : '';

        $res = $this->api->curl($api_url . '/api/invoicedetails/', $params, "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);
            $data = array();

            foreach ($content['results'] as $result) {
                $details = json_decode($result['invoice_details'][0], true);
                $payment_history = json_decode($result['payment_history'], true);
                //$payment_refund_history = json_decode($result['payment_refund_history'], true);
                print_r($result);
                exit;
                $data[] = array('subscription' => str_replace('Subscription Plan - ', '', $details['sale_item_details']),
                    'price' => $payment_history['amount_paid'],
                    'date' => $payment_history['payment_date'],
                    'status_id' => $payment_history['status_id'],
                );
            }
            return $data;
        } else {
            return false;
        }
    }

    /* Function to do transaction on Braintree */

    public function processPayment($config, $data, $autorenew = 0)
    {
        //return array('status' => 1, 'transaction_id' => 'bfc8cm');
        \Braintree_Configuration::environment($config['payment_gateway']['tree_env']);
        \Braintree_Configuration::merchantId($config['payment_gateway']['merchant_id']);
        \Braintree_Configuration::publicKey($config['payment_gateway']['public_key']);
        \Braintree_Configuration::privateKey($config['payment_gateway']['private_key']);

        // Get details store in vault
        /* $result = \Braintree_Creditcard::find("2nxtpm");

          echo "<pre>";
          print_r($result);
          echo "<br> get more data:--:";
          print_r($result->creditCards);
          echo "<br>Customer: ".$result->customerId;
          echo "<br>Month: ".$result->expirationMonth;
          echo "<br>Year:-: ".$result->expirationYear."<br>";
         */

        if ($autorenew) {

            // Transaction using vault customer id
            $result = \Braintree_Transaction::sale(array(
                        'customerId' => $data['customerDetails_id'], //'56863526',
                        'paymentMethodToken' => $data['creditCardDetails_token'], //'dn6nq6',
                        'amount' => $data['amount'],
            ));
        } else {
            $remember = ($data['rememberme'] == 1) ? true : false;
            //echo "get remember me :--:".$remember; die;

            $result = \Braintree_Transaction::sale(array('amount' => $data['amount'],
                        'creditCard' => array('number' => $data['card_no'],
                            "cvv" => $data['cvv_no'],
                            'expirationMonth' => $data['month'],
                            'expirationYear' => $data['year']),
                        'customer' => array(
                            'firstName' => $data['name'],
                            'email' => $data['email']
                        ),
                        "options" => array(
                            "submitForSettlement" => true,
                            'storeInVault' => ($data['rememberme'] == 1) ? true : false
                        )
            ));
        }

        if ($result->success) {
            // return array('status' => 1, 'transaction_id' => $result->transaction->id, 'user_card_id' =>$result->transaction->customerDetails->id);
            return array('status' => 1, 'transaction_id' => $result->transaction->id, 'creditCardDetails_token' => $result->transaction->creditCardDetails->token, 'customerDetails_id' => $result->transaction->customerDetails->id);
        } else if ($result->transaction) {
            return array('status' => 0, 'msg' => $result->message, 'errors' => array('code' => $result->transaction->processorResponseCode, 'text' => $result->transaction->processorResponseText));
            /* print_r("Error processing transaction:");
              print_r("\n  message: " . $result->message);
              print_r("\n  code: " . $result->transaction->processorResponseCode);
              print_r("\n  text: " . $result->transaction->processorResponseText); */
        } else {
            return array('status' => 0, 'msg' => $result->message, 'errors' => $result->errors->deepAll());
            /* print_r("Message: " . $result->message);
              print_r("\nValidation errors: \n");
              print_r($result->errors->deepAll()); */
        }
        /*
          $result = \Braintree_Transaction::sale(array('amount' => '1.00',
          'creditCard' => array('number' => '5105105105105100',
          "cvv" => '',
          'expirationMonth' => '05',
          'expirationYear' => '12'),
          'customer' => array(
          'firstName' => 'Piyush',
          'lastName' => 'Arya',
          'company' => 'Clavax',
          'phone' => '012-505-1234',
          'fax' => '312-555-1235',
          'website' => 'http://www.clavax.us',
          'email' => 'kanhaiyam@clavax.us'
          ),

          "options" => array(
          "submitForSettlement" => true
          )
          ));
         */
    }

    /* Update credit card details on Braintree */

    public function updateCard($config, $data)
    {
        $common = new Common;
        $updateData = array();
        $oldCardExists = false;
        $session = new Container('frontend');
        \Braintree_Configuration::environment($config['payment_gateway']['tree_env']);
        \Braintree_Configuration::merchantId($config['payment_gateway']['merchant_id']);
        \Braintree_Configuration::publicKey($config['payment_gateway']['public_key']);
        \Braintree_Configuration::privateKey($config['payment_gateway']['private_key']);
        $cardDetails = $common->getUserCardDetails($config['api_url']['value'], array('user_id' => $session->userid));
        //print_r($cardDetails); exit;
        $card = isset($cardDetails[0]) ? $cardDetails[0] : '';

        if (!is_array($data)) {
            parse_str($data, $data);
        }
        if (isset($card) && is_array($card)) {
            try {
                $result = \Braintree_CreditCard::find($card['creditCardDetails_token']);
                $oldCardExists = true;
            } catch (\Exception $ex) {
                $oldCardExists = false;
                //echo $ex->getMessage();
            }

            try {
                $delete = \Braintree_CreditCard::delete($card['creditCardDetails_token']);
            } catch (\Exception $ex) {
                //echo $ex->getMessage();
            }
        }

        if (!isset($data['creditCardDetails_token']) && !isset($data['customerDetails_id'])) {

            $result = \Braintree_CreditCard::create(array(
                        'customerId' => isset($card['customerDetails_id'])?$card['customerDetails_id']:'',
                        'number' => $data['card_no'],
                        'cvv' => $data['cvv'],
                        'expirationDate' => $data['month'] . '/' . $data['year'],
                        'cardholderName' => $data['name_on_card'],
                        'options' => array(
                            'makeDefault' => true
                        )
                            )
            );

            if ($result->success) {
                $data['creditCardDetails_token'] = $result->creditCard->token;
                $data['customerDetails_id'] = $result->creditCard->customerId;
            } else {
                return array('status' => 0, 'msg' => $result->message, 'errors' => $result->errors->deepAll());
            }
        }
        
        $updateData['user_id'] = $session->userid;
        $updateData['card_expiration_hash'] = isset($data['card_expiration_hash'])?$data['card_expiration_hash']:md5($data['month'] . '-' . $data['Year']);
        $updateData['creditCardDetails_token'] = $data['creditCardDetails_token'];
        $updateData['customerDetails_id'] = $data['customerDetails_id'];
        isset($card['use_for_renew'])?$updateData['use_for_renew'] = $card['use_for_renew']:'';
        isset($data['use_for_renew'])?$updateData['use_for_renew'] = $data['use_for_renew']:'';
        $res = isset($card['id']) ? $this->api->curl($config['api_url']['value'] . '/api/card_details/' . $card['id'] . '/', $updateData, 'PUT') : $this->api->curl($config['api_url']['value'] . '/api/card_details/', $updateData, 'POST');

        if ($res->getStatusCode() == 200 || $res->getStatusCode() == 201) {
            return array('status' => 1, 'msg' => 'Card details successfully updated..!!');
        } else {
            return array('status' => 1, 'msg' => 'Failed to update card details..!!', 'errors' => json_decode($res->getBody(), true));
        }
    }

}
