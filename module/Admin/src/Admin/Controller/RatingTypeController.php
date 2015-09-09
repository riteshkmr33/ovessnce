<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\RatingType;
use Admin\Form\RatingTypeForm;

class RatingTypeController extends AbstractActionController
{
	private $ratingTypeTable;
	
	private function getRatingTypeTable()
	{
		if (!$this->ratingTypeTable) {
			$this->ratingTypeTable = $this->getServiceLocator()->get('Admin\Model\RatingTypeTable');
		}
		
		return $this->ratingTypeTable;
	}
	
	private function getLoggedinUser()
	{
		return $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUserData(array('user_name' => $this->getServiceLocator()->get('AuthService')->getIdentity()));
	}

    public function indexAction()
    {
		$paginator = $this->getRatingTypeTable()->fetchAll();
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page',1)); 
		$paginator->setItemCountPerPage(10);
		
        return new ViewModel(array(
			'ratingtypes' => $paginator,
			'status' => $this->getServiceLocator()->get('Admin/Model/StatusTable')->fetchAll(false, array(1,2)),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }
    
    public function addAction()
    {
		$form = new RatingTypeForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $rt = new RatingType();
            
            // Adding already exist validation on runtime excluding the current record
			$rt->getInputFilter()->get('rating_type')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'lookup_rating','field' => 'rating_type','adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'))));
            
            $form->setInputFilter($rt->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $rt->exchangeArray($form->getData());
                $this->getRatingTypeTable()->saveRatingType($rt, $this->getLoggedinUser());
                $this->flashMessenger()->addSuccessMessage('Rating Type added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/ratingtypes');
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array('form' => $form,  'errors' => $this->errors);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/ratingtypes', array(
                'action' => 'add'
            ));
        }
        $rt = $this->getRatingTypeTable()->getRatingType($id);
        
        if ($rt == false) {
			$this->flashMessenger()->addErrorMessage('Rating type not found..!!');
			return $this->redirect()->toRoute('admin/ratingtypes');
		}
		
        $form = new RatingTypeForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($rt);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
			
			// Adding already exist validation on runtime excluding the current record
			$rt->getInputFilter()->get('rating_type')->getValidatorChain()->attach(new \Zend\Validator\Db\NoRecordExists(array('table' => 'lookup_rating','field' => 'rating_type','adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'), 'exclude' => array('field' => 'id','value' => $id))));
			
            $form->setInputFilter($rt->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getRatingTypeTable()->saveRatingType($form->getData(), $this->getLoggedinUser());
                $this->flashMessenger()->addSuccessMessage('Rating Type updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/ratingtypes');
            } else {
				$this->errors = $form->getMessages();
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
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/ratingtypes');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getRatingTypeTable()->deleteRatingType($id);
                $this->flashMessenger()->addSuccessMessage('Rating Type deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/ratingtypes');
        }

        return array(
            'id'    => $id,
            'ratingtype' => $this->getRatingTypeTable()->getRatingType($id)
        );
    }
    
    public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getRatingTypeTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}
}

