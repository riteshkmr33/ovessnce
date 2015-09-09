<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Application\Model\Common;
use Application\Model\Practitioners;
use Zend\Session\Container;
use Application\Form\SearchForm;

class IndexController extends AbstractActionController 
{

    public function indexAction() 
    {
        $api = new Api();
        $common = new Common;
        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
        
        //Start:-- Search form 
        $session = new Container('frontend');
        $loggedInUser = $session->userid;
        $userType = $session->user_type_id;
        $model_practitioner = new Practitioners;
        $loggedInUserAddress = $model_practitioner->getLoggedInUserAddress($loggedInUser, $userType, $api_url);
        $treatment_list = $common->getAllservices($api_url);
        $search_form = new SearchForm($treatment_list, $common->getstatesByCountry($api_url, $loggedInUserAddress->country_id));
        
        // getting banners
        $banners = $common->getBanner($api_url, 1);
        
        //End:-- Search form 
        
        $page1 = $common->getPage($api_url, 7);
        $page2 = $common->getPage($api_url, 8);
        $page3 = $common->getPage($api_url, 9);
        $page4 = $common->getMedia($api_url);
        $banner_content = $common->getPage($api_url, 13);
        
        $data = array('page1'=>  stripcslashes($page1), 'page2'=>stripcslashes($page2), 'page3'=>stripcslashes($page3), 'page4'=> $page4,'search_form' => $search_form, 'banners' => $banners, 'banner_content' => $banner_content);
        return new ViewModel($data);
    }
    
}
