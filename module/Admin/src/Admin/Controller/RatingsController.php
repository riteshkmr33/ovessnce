<?php

namespace Admin\Controller; 

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Ratings;
use Admin\Form\RatingForm;
use Zend\InputFilter\InputFilter;

class RatingsController extends AbstractActionController
{
	private $ratingTable;
	private $errors = array();
	
	private function getRatingTable()
	{
		if (!$this->ratingTable) {
			$sm = $this->getServiceLocator();
			$this->ratingTable = $sm->get('Admin\Model\RatingsTable');
		}
	
		return $this->ratingTable;
	}
	
	private function getLoggedinUser()
	{
		return $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUserData(array('user_name' => $this->getServiceLocator()->get('AuthService')->getIdentity()));
	}
	
    public function indexAction()
    {
        $paginator = $this->getRatingTable()->fetchAll();
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);

		return new ViewModel(array(
			'ratings' => $paginator,
			'ratingTypes' => $this->getServiceLocator()->get('ADmin\Model\RatingTypeTable')->fetchAll(false),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
    }
    
    public function addAction()
    {
		$filter = new InputFilter;
		
		$form = new RatingForm($this->getServiceLocator()->get('Admin\Model\ServiceProviderTable'), $this->getServiceLocator()->get('Admin\Model\ServicesTable'));
		$ratingTypes = $this->getServiceLocator()->get('ADmin\Model\RatingTypeTable')->fetchAll(false);
		
		$fields = array();
		
		foreach ($ratingTypes  as $ratingType) {
			$form->add(array(
				'type' => 'Zend\Form\Element\Radio',
				'name' => 'rating['.$ratingType->id.']',
				'options' => array(
					'label' => $ratingType->rating_type,
					'value_options' => array(
                             '1' => '',
                             '2' => '',
                             '3' => '',
                             '4' => '',
                             '5' => '',
                     ),
				),
				'attributes' => array(
					'class' => 'star'
				)
			));
			
			// adding validation rule at run time
			$filter->add(array('name' => 'rating['.$ratingType->id.']','required' => false,));
			
			$fields[] = $ratingType->id;
		}
        
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $rating = new Ratings();
            
            $form->setInputFilter($filter);
            $form->setData($request->getPost());
            //print_r($request->getPost()); exit;
            if ($form->isValid()) {
                $rating->exchangeArray($form->getData());
                if ($this->getRatingTable()->saveRating($rating, $request->getPost('rating'),$this->getLoggedinUser())) {
					$this->flashMessenger()->addSuccessMessage('Rating added successfully..!!');
				} else {
					$this->flashMessenger()->addErrorMessage('No rating added..!!');
				}

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/ratings');
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array('form' => $form,  'errors' => $this->errors, 'fields' => $fields);
	}
    
    public function deleteAction()
    { 
        $user = (int) $this->params()->fromRoute('user', 0);
        //$service = (int) $this->params()->fromRoute('service', 0);
        $createdby = (int) $this->params()->fromRoute('createdby', 0);
        $ratingtypeid = (int) $this->params()->fromRoute('ratingtypeid', 0);
        //if (!$user || !$service || !$createdby || !$ratingtypeid) {
        if (!$user || !$createdby || !$ratingtypeid) {
            return $this->redirect()->toRoute('admin/ratings');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $user = (int) $request->getPost('user');
               // $service = (int) $request->getPost('service');
                $createdby = (int) $request->getPost('createdby');
                $ratingtypeid = (int) $request->getPost('ratingtypeid');
                //$this->getRatingTable()->deleteRating($user, $service, $createdby, $ratingtypeid);
                $this->getRatingTable()->deleteRating($user, $createdby, $ratingtypeid);
                $this->flashMessenger()->addSuccessMessage('Rating deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/ratings');
        }

        return array(
            'user'    => $user,
            //'service'    => $service,
            'createdby' => $createdby,
            'ratingtypeid' => $ratingtypeid,
            //'rating' => $this->getRatingTable()->getRating($user, $service, $createdby, $ratingtypeid)
            'rating' => $this->getRatingTable()->getRating($user, $createdby, $ratingtypeid)
        );
    }
    
    public function deleteAllAction()
    {
		$request = $this->getRequest();
        if ($request->isPost()) {
			$users_ids = array_unique($request->getPost('users'));
			//$service_ids = array_unique($request->getPost('servs'));
			$created_by = array_unique($request->getPost('creats'));
			$rating_type_id = array_unique($request->getPost('rtids'));
			
			//if (count($users_ids) > 0 && count($service_ids) > 0 && count($created_by) > 0) {
			if (count($users_ids) > 0 && count($created_by) > 0) {
				//$this->getRatingTable()->deleteRating($users_ids, $service_ids, $created_by, $rating_type_id);
				$this->getRatingTable()->deleteRating($users_ids, $created_by, $rating_type_id);
				
				$paginator = $this->getRatingTable()->fetchAll();
				$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
				$paginator->setItemCountPerPage(10);

				return new ViewModel(array(
					'ratings' => $paginator,
					'ratingTypes' => $this->getServiceLocator()->get('ADmin\Model\RatingTypeTable')->fetchAll(false),
					'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
					'errors' => $this->flashMessenger()->getCurrentErrorMessages()
				));
			}
			
		}
		
		exit;
	}

}

