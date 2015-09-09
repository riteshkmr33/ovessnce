<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\UserFeatureSetting;


class UserFeatureSettingController extends AbstractActionController
{
	private $UserFeatureSettingTable;
	
	private function getUserFeatureSettingTable()
	{
		if (!$this->UserFeatureSettingTable) {
			$this->UserFeatureSettingTable = $this->getServiceLocator()->get('Admin\Model\UserFeatureSettingTable');
		}
		
		return $this->UserFeatureSettingTable;
	}
	
	public function indexAction()
    {  
		$id = (int) $this->params()->fromRoute('id', 0); 
        if (!$id) {
            return $this->redirect()->toRoute('admin/users');
        }
       
        $data = $this->getUserFeatureSettingTable()->getFeatureById($id);
		$userData = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUser($id);
 		
        return new ViewModel(array('feature'=>$data,
        'id' => $id,
        'name' => ucfirst($userData->first_name),
        'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false),
        'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
		'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
        die;
    }
}
