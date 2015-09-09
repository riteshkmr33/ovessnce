<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\NotificationSettings;
use Admin\Form\NotificationSettingsForm;

class NotificationSettingsController extends AbstractActionController
{
	protected $NotificationSettingsTable; 
	
	public function indexAction() 
    {
        $paginator = $this->getNotificationSettingsTable()->fetchAll();  
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1)); 
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'settings' => $paginator,
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
        
    }
    
    public function addAction()
    {
		$form = new NotificationSettingsForm($this->getServiceLocator()->get('Admin\Model\UsersTable'),$this->getServiceLocator()->get('Admin\Model\SiteModulesTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $NotificationSettings = new NotificationSettings();
            $form->setInputFilter($NotificationSettings->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $NotificationSettings->exchangeArray($form->getData());
                $this->getNotificationSettingsTable()->saveNotificationSettings($NotificationSettings);
                $this->flashMessenger()->addSuccessMessage('Settings added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/notificationsettings');
            }
        }
        return array('form' => $form);
	}
		
    public function editAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/notificationsettings', array(
                'action' => 'add'
            ));
        }
        $NotificationSettings = $this->getNotificationSettingsTable()->getNotificationSetting($id);
        if ($NotificationSettings == false) {
			$this->flashMessenger()->addErrorMessage('Setting not found..!!');
			return $this->redirect()->toRoute('admin/notificationsettings');
		}

        $form  = new NotificationSettingsForm($this->getServiceLocator()->get('Admin\Model\UsersTable'),$this->getServiceLocator()->get('Admin\Model\SiteModulesTable'));
        $form->bind($NotificationSettings);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($NotificationSettings->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getNotificationSettingsTable()->saveNotificationSettings($form->getData());
				$this->flashMessenger()->addSuccessMessage('Settings updated successfully..!!');
				
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/notificationsettings');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
	}
		
    public function deleteAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/notificationsettings');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getNotificationSettingsTable()->deleteNotificationSettings($id);
                $this->flashMessenger()->addSuccessMessage('Settings deleted successfully..!!'); 
            }

            // Redirect to list of pages
            return $this->redirect()->toRoute('admin/notificationsettings');
        }

        return array(
            'id'    => $id,
            'settings' => $this->getNotificationSettingsTable()->getNotificationSetting($id)
        );
	}	
	
	public function getNotificationSettingsTable()
	{
		if (!$this->NotificationSettings) {
			$sm = $this->getServiceLocator();
			$this->NotificationSettings = $sm->get('Admin\Model\NotificationSettingsTable');
		}
	
		return $this->NotificationSettings;
	}
	
}
