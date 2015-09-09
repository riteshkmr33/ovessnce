<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SiteSettings;
use Admin\Form\SiteSettingsForm;

class SiteSettingsController extends AbstractActionController
{
	private $getSiteSettingTable;
	
	private function getSSTable()
	{
		if (!$this->getSiteSettingTable) {
			$this->getSiteSettingTable = $this->getServiceLocator()->get('Admin\Model\SiteSettingsTable');
		}
		
		return $this->getSiteSettingTable;
	}

    public function indexAction()
    {
        $paginator = $this->getSSTable()->fetchAll();
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);

		return new ViewModel(array(
			'sitesettings' => $paginator,
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
    }
    
    public function addAction()
    {
		return $this->redirect()->toRoute('admin/sitesettings');  // Add functionality is disabled for time being
		
        $form = new SiteSettingsForm();
        
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $sitesettings = new SiteSettings();
            
            $form->setInputFilter($sitesettings->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $sitesettings->exchangeArray($form->getData());
                $this->getSSTable()->saveSiteSetting($sitesettings);
                $this->flashMessenger()->addSuccessMessage('Site Settings added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/sitesettings');
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array('form' => $form, 'errors' => $this->errors);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sitesettings');
        }
        $sitesetting = $this->getSSTable()->getSiteSetting($id);
        
        if ($sitesetting == false) {
			$this->flashMessenger()->addErrorMessage('Site setting not found..!!');
			return $this->redirect()->toRoute('admin/sitesettings');
		}
		
		$form = new SiteSettingsForm();
        $form->bind($sitesetting);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($sitesetting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSSTable()->saveSiteSetting($form->getData());
                $this->flashMessenger()->addSuccessMessage('Site setting updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/sitesettings');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'errors' => $this->errors
        );
    }
    
    public function deleteAction()
    {
		return $this->redirect()->toRoute('admin/sitesettings');  // delete functionality is disabled for time being
		
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sitesettings');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSSTable()->deleteSiteSetting($id);
                $this->flashMessenger()->addSuccessMessage('Site setting deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/sitesettings');
        }

        return array(
            'id'    => $id,
            'sitesetting' => $this->getSSTable()->getSiteSetting($id)
        );
    }


}

