<?php namespace Zend;
/**
 * The AuthorizeNet PHP SDK. Include this file in your project.
 *
 * @package AuthorizeNet
 */
use Zend/Payment/lib/shared/AuthorizeNetRequest;
use Zend/Payment/lib/shared/AuthorizeNetTypes;
use Zend/Payment/lib/shared/AuthorizeNetXMLResponse;
use Zend/Payment/lib/shared/AuthorizeNetResponse;
use Zend/Payment/lib/AuthorizeNetAIM;
use Zend/Payment/lib/AuthorizeNetARB;
use Zend/Payment/lib/AuthorizeNetCIM;
use Zend/Payment/lib/AuthorizeNetSIM;
use Zend/Payment/lib/AuthorizeNetDPM;
use Zend/Payment/lib/AuthorizeNetTD;
use Zend/Payment/lib/AuthorizeNetCP;

if (class_exists("SoapClient")) {
    use Zend/Payment/lib/AuthorizeNetSOAP;
}

class AuthorizeNet
{
	private $gateway;
	
	public function __construct($method='AuthorizeNetAIM')
	{
		$this->gateway = new $method;
	}
	
	
}
