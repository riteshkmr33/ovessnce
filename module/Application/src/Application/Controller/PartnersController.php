<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Application\Model\Common;

class PartnersController extends AbstractActionController
{

    public function indexAction()
    {
        $api = new Api();
        $common = new Common;
        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
        $url = $api_url . "/api/partners/?status_id=1";
        $res = $api->curl($url, array(), "GET");
        if ($res->getStatusCode() == 200) {
            $content = json_decode($res->getBody(), true);

            if ($this->getRequest()->isXmlHttpRequest()) {
                $partners_list = '';
                foreach ($content as $data) {
                    $partners_list .= "<li>";
                    $partners_list .= "<img src='" . $data['logo'] . "' alt='' >";
                    $partners_list .= "</li>";
                }

                echo $partners_list;
                die;
            }
            
            // getting banners
            $banners = $common->getBanner($api_url, 13);
            $banner_content = $common->getPage($api_url, 14);

            return new ViewModel(array(
                'partners' => $content,
                'banners' => $banners,
                'banner_content' =>$banner_content
            ));
        }
    }

}
