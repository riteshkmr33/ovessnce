<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;

class PageController extends AbstractActionController
{

    public function indexAction()
    {

        $slug = $this->params()->fromRoute('slug');
        $redirectUrl = array('controller' => 'index', 'action' => 'index');
        $page = "";
        if (!empty($slug) && $slug != null) {

            $api = new Api();
            $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
            $url = $api_url . "/api/pages/";
            $data = array('slug' => $slug);
            $res = $api->curl($url, $data, "GET");

            if ($res->getStatusCode() == 200) {

                $content = json_decode($res->getBody(), true);

                if (!(isset($content[0]['content']) && !empty($content[0]['content']))) {
                    return $this->redirect()->toRoute(null, $redirectUrl);
                }
                $page = stripcslashes($content[0]['content']);
                //var_dump($content); die;
                $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set($content[0]['title']);

                $this->getServiceLocator()->get('ViewHelperManager')->get('headMeta')->setName('keywords', $content[0]['meta_tags']);
                $this->getServiceLocator()->get('ViewHelperManager')->get('headMeta')->setName('description', $content[0]['title']);
            }
            
            return new ViewModel(array(
                'page' => $page
            ));
        } else {

            return $this->redirect()->toUrl('/');
        }
    }

}
