<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Http\Request;
use SlmGoogleAnalytics\View\Helper\GoogleAnalytics;
use Hybrid_Auth;
use Zend\View\Model\ModelInterface;

class TestController extends AbstractActionController
{
   
   	public function preDispatch(){
       #$this->_helper->layout()->disableLayout(); 
        #$this->_helper->viewRenderer->setNoRender(true);
    }
	
	public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $client = new \Services_Twilio($config['Twilio']['sid'], $config['Twilio']['token']);
		$msg = $client->account->messages->sendMessage($config['Twilio']['fromNumber'], "+918860668906","Helo world! I love you <3");
		//print_r($msg);
	   exit;
    }
	
    public function deleteCacheAction()
    {
        foreach(scandir("/var/www/websites/ovessence/public_html/data/cache") as $files){
            if($files != "." && $files != ".."){
				echo $files; 
                unlink("/var/www/websites/ovessence/public_html/data/cache/".$files);
            }
            
            
        }
	   exit;
    }
	
	public function gmapAction()
	{
	 $markers = array(
        'Mozzat Web Team' => '17.516684,79.961589',
        'Home Town' => '16.916684,80.683594'
		);  //markers location with latitude and longitude

		$config = array(
			'sensor' => 'true',         //true or false
			'div_id' => 'map',          //div id of the google map
			'div_class' => 'grid_6',    //div class of the google map
			'zoom' => 5,                //zoom level
			'width' => "600px",         //width of the div
			'height' => "300px",        //height of the div
			'lat' => 16.916684,         //lattitude
			'lon' => 80.683594,         //longitude 
			'animation' => 'none',      //animation of the marker
			'markers' => $markers       //loading the array of markers
		);

		$map = $this->getServiceLocator()->get('GMaps\Service\GoogleMap'); //getting the google map object using service manager
		$map->initialize($config);                                         //loading the config   
		$html = $map->generate();                                          //genrating the html map content  
		
        //$ga = $this->getServiceLocator()->get('google-analytics');
        
        //$ga->setContainerName('HeadScript');
		//$ga = $this->getServiceLocator()->get('google-analytics');
		
		
		//$event = new \SlmGoogleAnalytics\Analytics\Event('test');
		//$event->setLabel('Gone With the Wind Map');  // optionally
		//$event->setValue(5);  
        //$script = new \SlmGoogleAnalytics\View\Helper\Script\ScriptInterface();
		//$et = new GoogleAnalytics($script);
		// optionally
		//$ga->addEvent($event);
        //$ga->setEnableTracking(true);
		
        /*
        print "<pre>";
        
        $cls = get_class($ga);
        $mlist = get_class_methods($cls);
        print_r($mlist);
        exit;
        */
		return new ViewModel(array('map_html' => $html));                  //passing it to the view
	}
	
	// List all video uploaded to vimeo object
    public function vimeoAction()
    {
        //print "<pre>"; 
        $config = $this->getServiceLocator()->get('Config');        
        
        $vimeo = new \phpVimeo($config['Vimeo']['clientId'], $config['Vimeo']['clientSecrate']);
        
        // we shall manage token in session lets do that//
        $session = new Container('vimeo');
        //$session->accessToken = "hellow";
        //$session->accessSecret = "world";
        
        $request = new Request();
        
        // if request for all new request 
        if($request->getQuery()->clear =='All')
        {
            $session->offsetUnset('accessToken');
            $session->offsetUnset('accessSecret');
        }
        
        // Set up variables
        $state = $session->vimeo_state;
        $request_token = $session->oauth_request_token;
        $access_token = $session->oauth_access_token;

        // Coming back
        if ($request->getQuery()->oauth_token != NULL && $request->getQuery()->vimeo_state === 'start') {
            $request->getQuery()->vimeo_state = $state = 'returned';
        }

        // If we have an access token, set it
        if ($session->oauth_access_token != null) {
            $vimeo->setToken($session->oauth_access_token, $session->oauth_access_token_secret);
        }
        
        switch ($session->vimeo_state) {
            default:

        // Get a new request token
        $token = $vimeo->getRequestToken();

        // Store it in the session
        $session->oauth_request_token = $token['oauth_token'];
        $session->oauth_request_token_secret = $token['oauth_token_secret'];
        $session->vimeo_state = 'start';

        // Build authorize link
        $authorize_link = $vimeo->getAuthorizeUrl($token['oauth_token'], 'write');

        break;

        case 'returned':

        // Store it
        if ($session->oauth_access_token === NULL && $session->oauth_access_token_secret === NULL) {
            // Exchange for an access token
            $vimeo->setToken($session->oauth_request_token, $session->oauth_request_token_secret);
            $token = $vimeo->getAccessToken($_REQUEST['oauth_verifier']);

            // Store
            $session->oauth_access_token = $token['oauth_token'];
            $session->oauth_access_token_secret = $token['oauth_token_secret'];
            $session->vimeo_state = 'done';

            // Set the token
            $vimeo->setToken($session->oauth_access_token, $session->oauth_access_token_secret);
        }

        $videos = $vimeo->call('vimeo.videos.getUploaded', array('user_id' => '27579548'));


        // Do an authenticated call
        try {
            $videos = $vimeo->call('vimeo.videos.getUploaded', array('user_id' => '27579548'));
            
        }
        catch (VimeoAPIException $e) {
            echo "Encountered an API error -- code {$e->getCode()} - {$e->getMessage()}";
        }

        break;
        }
        //print_r($vimeo);
        //exit;
        $data = array();
        $data['session'] = $session;
        $data['authorize_link'] = $authorize_link;
        if(isset($videos)){
            $data['videos']= $videos;
        }
        return new ViewModel($data);
    }
	
    // upload video to vimeo //
    public function vimeoUploadAction()
    {
    
    $config = $this->getServiceLocator()->get('Config'); 
    $session = new Container('vimeo');
    
    
    $vimeo = new \phpVimeo($config['Vimeo']['clientId'], $config['Vimeo']['clientSecrate'], $session->oauth_access_token, $session->oauth_access_token_secret);
    //print_r($vimeo);
    try {
		$video_id = $vimeo->upload('uploads/bunny.mp4');

		if ($video_id) {
			echo '<a href="http://vimeo.com/' . $video_id . '">Upload successful!</a>';

			//$vimeo->call('vimeo.videos.setPrivacy', array('privacy' => 'nobody', 'video_id' => $video_id));
			$vimeo->call('vimeo.videos.setTitle', array('title' => 'My Video through script', 'video_id' => $video_id));
			$vimeo->call('vimeo.videos.setDescription', array('description' => 'Video put through zend script', 'video_id' => $video_id));
		}
		else {
			echo "Video file did not exist!";
		}
	}
	catch (VimeoAPIException $e) {
		echo "Encountered an API error -- code {$e->getCode()} - {$e->getMessage()}";
	}
    
    exit;
    } 
    
    // Brain tree payment gateway integration //
    public function braintreeAction()
    {
        $config = $this->getServiceLocator()->get('Config'); 
        
        
        
        \Braintree_Configuration::environment($config['payment_gateway']['tree_env']);
        \Braintree_Configuration::merchantId($config['payment_gateway']['merchant_id']);
        \Braintree_Configuration::publicKey($config['payment_gateway']['public_key']);
        \Braintree_Configuration::privateKey($config['payment_gateway']['private_key']);
       //
        
        $result = \Braintree_Transaction::sale(array('amount' => '100.00',
        'creditCard' => array('number' => '5105105105105100',
        "cvv" => '123',
        'expirationMonth' => '05',
        'expirationYear' => '12'),
        'customer' => array(
            'firstName' => 'Kanhaiya',
            'lastName' => 'Mishra',
            'company' => 'Clavax',
            'phone' => '012-505-1234',
            'fax' => '312-555-1235',
            'website' => 'http://www.clavax.us',
            'email' => 'kanhaiyam@clavax.us'
      ),
      'billing' => array(
        'firstName' => 'Abhijeet',
        'lastName' => 'Sawant',
        'company' => 'Braintree',
        'streetAddress' => '1 E Main View',
        'extendedAddress' => 'Suite 403',
        'locality' => 'Chicago',
        'region' => 'Illinois',
        'postalCode' => '60622',
        'countryCodeAlpha2' => 'US'
      ),
      'shipping' => array(
        'firstName' => 'Ravneet',
        'lastName' => 'K',
        'company' => 'Braintree',
        'streetAddress' => '1600 Amphitheatre Parkway',
        'extendedAddress' => 'Mountain View',
        'locality' => ' - ',
        'region' => 'CA',
        'postalCode' => '60103',
        'countryCodeAlpha2' => 'US'
      ),
        "options" => array(
            "submitForSettlement" => true
        )
        ));
        
        if ($result->success) {
            print_r("success!: " . $result->transaction->id);
        } else if ($result->transaction) {
            print_r("Error processing transaction:");
            print_r("\n  message: " . $result->message);
            print_r("\n  code: " . $result->transaction->processorResponseCode);
            print_r("\n  text: " . $result->transaction->processorResponseText);
        } else {
            print_r("Message: " . $result->message);
            print_r("\nValidation errors: \n");
            print_r($result->errors->deepAll());
        }
        print "<pre>";
        print_r($result);
        exit;
    
    
    }
    
    // login to word press from here //
    public function loginwpAction()
    {
        ini_set('session.cookie_domain', '.ovessence.loc' );
        //ini_set('session.cookie_domain', '.ovessence.in' );
        //die('hello');
        $user_login = 'admin';
        //$user_login = 'badelal';
        $user = get_userdatabylogin($user_login);
        //print_r($user);
        //exit;
        $user_id = $user->ID;
        wp_set_current_user($user_id, $user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user_login);
        return $this->redirect()->toUrl('http://localhost/ovessence/blog/wp-admin/');
    }
    
    public function wplAction()
    {
        
        $user_login = 'admin';
        //$user_login = 'badelal';
        $user = get_userdatabylogin($user_login);
//        print_r($user);
//        exit;
        $user_id = $user->ID;
        wp_set_current_user($user_id, $user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user_login);
        return $this->redirect()->toUrl('http://blog.ovessence.loc/wp-admin/');
    }
    
    // login to word press from here //
    public function logoutwpAction()
    { 
        wp_logout(); // need only this function to logout
        die('logoutBade 123');
        return $this->redirect()->toUrl('http://blog.ovessence.in/wp-admin/');
    }
    
     // login to word press from here //
    public function wpcreateuserAction()
    {
        $user_name="badelal";
        $user_id = username_exists( $user_name );
        $user_email = "badelalk@clavax.us";
        
        if ( !$user_id and email_exists($user_email) == false ) {
            //$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
            $random_password = "tech";
            $user_id = wp_create_user( $user_name, $random_password, $user_email );
        } else {
            $random_password = __('User already exists.  Password inherited.');
        }
        print $random_password;
        exit;
        //return $this->redirect()->toUrl('http://localhost/ovessence/blog/wp-admin');
    }
    
    
    //create login action //
    public function loginAction(){
     // $socialAuth = new scnsocialauth();
        $zfcUserLogin = $this->forward()->dispatch('zfcuser', array('action' => 'login'));
        if (!$zfcUserLogin instanceof ModelInterface) {
            return $zfcUserLogin;
        }
        
        $viewModel = new ViewModel();
        $viewModel->addChild($zfcUserLogin, 'zfcUserLogin');
       print_r($this->getOptions());
       exit;
        $viewModel->setVariable('options', $this->getOptions());

        $redirect = false;
        
        $viewModel->setVariable('redirect', $redirect);

        return $viewModel;
     
    }
    
    
    
}
