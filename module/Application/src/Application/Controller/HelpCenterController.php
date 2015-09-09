<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Application\Model\Common;

class HelpCenterController extends AbstractActionController 
{
    public function indexAction()
    {
        return $this->redirect()->toRoute('helpcenter', array('action' => 'consumer'));
    }
    
    public function consumerAction()
    {
        $common = new Common;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $faqs = $common->getFaqs($api_url, 4);
        $banners = $common->getBanner($api_url, 15);
        
        return new ViewModel(array('faqs' => $faqs, 'banners' => $banners));
    }
    
    public function practitionerAction()
    {
        $common = new Common;
        $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
        $faqs = $common->getFaqs($api_url, 3);
        $banners = $common->getBanner($api_url, 15);
        
        return new ViewModel(array('faqs' => $faqs, 'banners' => $banners));
    }
    
    public function faqdetailsAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $common = new Common;
            $id = $request->getPost('id');
            $user_type = $request->getPost('user_type');
            $api_url = $this->getServiceLocator()->get('config')['api_url']['value'];
            
            $faqs = $common->getFaqs($api_url, $user_type, $id, 'single');
            echo json_encode($faqs);
        }
        exit;
    }
}
