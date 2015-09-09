<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Testimonials;
use Admin\Form\TestimonialsForm;

class TestimonialsController extends AbstractActionController
{
	protected $TestimonialsTable;
	
	public function indexAction()
    {
        $paginator = $this->getTestimonialsTable()->fetchAll();   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));   
		$paginator->setItemCountPerPage(10);   
		
		return new ViewModel(array(
			'testimonials' => $paginator,
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1,2)),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
        
    }
    
    public function addAction()
    {
		
		$form = new TestimonialsForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $testimonials = new Testimonials();
            $form->setInputFilter($testimonials->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $testimonials->exchangeArray($form->getData());
                $this->getTestimonialsTable()->saveTestimonial($testimonials);
                $this->flashMessenger()->addSuccessMessage('Testimonial added successfully..!!');

                return $this->redirect()->toRoute('admin/testimonials');
            }
        }
        return array('form' => $form);
         
	}
		
    public function editAction()
    {
		
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/testimonials', array(
                'action' => 'add'
            ));
        }
        $testimonials = $this->getTestimonialsTable()->getTestimonial($id);
        if ($testimonials == false) {
			$this->flashMessenger()->addErrorMessage('Testimonial not found..!!');
			return $this->redirect()->toRoute('admin/testimonials');
		}
		
        $form  = new TestimonialsForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->bind($testimonials);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($testimonials->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getTestimonialsTable()->saveTestimonial($form->getData());
                $this->flashMessenger()->addSuccessMessage('testimonial updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/testimonials');
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
            return $this->redirect()->toRoute('admin/testimonials');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getTestimonialsTable()->deleteTestimonial($id);
                $this->flashMessenger()->addSuccessMessage('Testimonial deleted successfully..!!');
            }

            // Redirect to list of pages
            return $this->redirect()->toRoute('admin/testimonials');
        }

        return array(
            'id'    => $id,
            'testimonials' => $this->getTestimonialsTable()->getTestimonial($id)
        );
         
	}	
	
	public function getTestimonialsTable()
	{
		if (!$this->TestimonialsTable) {
			$sm = $this->getServiceLocator();
			$this->TestimonialsTable = $sm->get('Admin\Model\TestimonialsTable');
		}
	
		return $this->TestimonialsTable;
	}
	
	public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getTestimonialsTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}
	
}
