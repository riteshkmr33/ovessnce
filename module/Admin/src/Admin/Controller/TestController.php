<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Test;
use Admin\Form\TestForm;

class TestController extends AbstractActionController
{
	private $testTable;
	public $error;
	
	private function getTestTable(){
			if(!$this->testTable){
				$sm = $this->getServiceLocator();
				$this->testTable = $sm->get("Admin\Model\TestTable");
			}
		return $this->testTable;
	}
	
	 public function indexAction()
    {
		$paginator = $this->getTestTable()->fetchAll();
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);

		return new ViewModel(array(
			'testes' => $paginator,
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
    }
    
    public function addAction()
    {
        $form = new TestForm($this->getServiceLocator()->get('Admin\Model\TestTable'));
        
        //$form = new ActivityForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $test = new Test();
            
            $form->setInputFilter($test->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
				$formData = array();
				$formData = $form->getData();
				
				$renameUpload = new \Zend\Filter\File\RenameUpload(array('target' => "./public/uploads/", 'randomize' => true, 'use_upload_name' => true));
                if($fileDetails = $renameUpload->filter($_FILES['document'])){
						$formData['document'] = $fileDetails['tmp_name'];		
					$test->exchangeArray($formData);
				}
				
                $test->exchangeArray($formData);
                $this->getTestTable()->saveTest($test);
                $this->flashMessenger()->addSuccessMessage('Test added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/test');
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array('form' => $form, 'errors' => $this->errors);
    }
    
    public function deleteAction()
    {	
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/test');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getTestTable()->deleteTest($id);
                $this->flashMessenger()->addSuccessMessage('Test deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/test');
        }

        return array(
            'id'    => $id,
            'test' => $this->getTestTable()->getTest($id)
        );
    }

     public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/test', array(
                'action' => 'add'
            ));
        }
        $test = $this->getTestTable()->getTest($id);
       
        if ($test == false) {
			$this->flashMessenger()->addErrorMessage('Test not found..!!');
			return $this->redirect()->toRoute('admin/test');
		}
		
		$form = new TestForm($this->getServiceLocator()->get('Admin\Model\TestTable'));
        $form->bind($test);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($test->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getTestTable()->saveTest($form->getData());
                $this->flashMessenger()->addSuccessMessage('Test updated successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/test');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'errors' => $this->errors
        );
    }
}
?>
