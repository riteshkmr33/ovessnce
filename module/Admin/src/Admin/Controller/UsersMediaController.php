<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\UsersMedia;
use Admin\Form\UsersMediaForm;
use Admin\Form\UsersMediaFilterForm;
use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use \Zend\Validator\File\IsImage;
use Zend\Session\Container;
use Zend\Http\Request;
use Zend\ImageS3;

class UsersMediaController extends AbstractActionController
{
	private $getUserMediaTable;
	
	private function getUserMediaTable()
	{
		if (!$this->getUserMediaTable) {
			$this->getUserMediaTable = $this->getServiceLocator()->get('Admin\Model\UsersMediaTable');
		}
		return $this->getUserMediaTable;
	}

    public function indexAction()
    {
		$user_id = (int) $this->params()->fromRoute('user_id', 0);
        if (!$user_id) {
            return $this->redirect()->toRoute('admin/users');
        }
        
        $user = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUser($user_id);
        
        if ($user == false) {
			$this->flashMessenger()->addErrorMessage('User not found..!!');
			return $this->redirect()->toRoute('admin/users');
		}
        
        $form = new UsersMediaFilterForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $request = $this->getRequest();
        $postedData = array();
        $getData = (array)$request->getQuery();
        unset($getData['page']);
        
        if ($request->isPost()) {
			$postedData = $request->getPost();
			$form->bind($postedData);
			$filter = array(
				'name' => trim($postedData['username']),
				'title' => trim($postedData['title']),
				'media_type' => trim($postedData['media_type']),
				'from_date' => ($postedData['from'] != "")?date("Y-m-d",strtotime($postedData['from'])):"",
				'to_date' => ($postedData['to'] != "")?date("Y-m-d",strtotime($postedData['to'])):"",
				'status_id' => $postedData['status_id'],
			);
			$paginator = $this->getUserMediaTable()->fetchAll($user_id, true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
		} else if (count($getData) > 0) {
			$filter = array();
			$form->bind($request->getQuery());
			$postedData = $getData;
			
			isset($getData['username'])?$filter['name'] = $getData['username']:"";
			isset($getData['title'])?$filter['title'] = $getData['title']:"";
			isset($getData['media_type'])?$filter['media_type'] = $getData['media_type']:"";
			(isset($getData['from']) && $getData['from'] != "")?$filter['from_date'] = date("Y-m-d",strtotime($getData['from'])):"";
			(isset($getData['to']) && $getData['to'] != "")?$filter['to_date'] = date("Y-m-d",strtotime($getData['to'])):"";
			isset($getData['status_id'])?$filter['status_id'] = trim($getData['status_id']):"";
			
			$paginator = $this->getUserMediaTable()->fetchAll($user_id, true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
		} else {
			$paginator = $this->getUserMediaTable()->fetchAll($user_id);
		}
		
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);

		return new ViewModel(array(
			'usersmedia' => $paginator,
			'form' => $form,
			'user_id' => $user_id,
			'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(9,5,10)),
			'postedData' => array_filter((array)$postedData),
			'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
			'errors' => $this->flashMessenger()->getCurrentErrorMessages()
		));
    }
    
    public function addAction()
    {
		$user_id = (int) $this->params()->fromRoute('user_id', 0);
        if (!$user_id) {
            return $this->redirect()->toRoute('admin/users');
        }
        
        $user = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUser($user_id);
        
        if ($user == false) {
			$this->flashMessenger()->addErrorMessage('User not found..!!');
			return $this->redirect()->toRoute('admin/users');
		}
        
        $form = new UsersMediaForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
			
			$File = $this->params()->fromFiles('media_url');
			
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				 array('media_url'=> $File['name'], 'media_type' => 1)
			);
			
            $usersMedia = new UsersMedia();
            $form->setInputFilter($usersMedia->getInputFilter());
            $form->setData($post);
			
            if ($form->isValid()) {
				
				$formData = array();
				$formData = $form->getData();
				
				$adapter = new \Zend\File\Transfer\Adapter\Http();
				
				$validator = new IsImage();
				if ($validator->isValid($File['tmp_name'])) {
					
					/* Image uploading code starts */
					$size = new Size(array('min'=>200,'max'=> 20000000));
					
					$adapter->setValidators(array($size), $File['name']);
					
					if (!$adapter->isValid()){	
					
						$dataError = $adapter->getMessages();
						
						return array('form' => $form, 'file_errors' => $dataError, 'user_id' => $user_id);
					}else{
						
						$S3 = new ImageS3;
						$data = $S3->uploadFiles($_FILES['media_url'],"Media",array(), array('Media' => 100, 'Media_thumb' => 20));
						
						if(is_array($data) && count($data) > 0 ){
							$formData['media_url'] = $data['Media'];
						}
					}
					/* Image uploading code ends */
				} else {
					return array('form' => $form, 'file_errors' => $validator->getMessages(), 'user_id' => $user_id);
				}
				
                $usersMedia->exchangeArray($formData);
                $this->getUserMediaTable()->saveUserMedia($usersMedia);
                $this->flashMessenger()->addSuccessMessage('User Media added successfully..!!');

                // Redirect to list of usersmedia
                return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
            } else {
				$this->errors = $form->getMessages();
			}
        }
        return array('form' => $form, 'errors' => $this->errors, 'user_id' => $user_id);
	}
	
	public function editAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
		$user_id = (int) $this->params()->fromRoute('user_id', 0);
		
		$user = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUser($user_id);
        
        if ($user == false) {
			$this->flashMessenger()->addErrorMessage('User not found..!!');
			return $this->redirect()->toRoute('admin/users');
		}
		
        if (!$id) {
            return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
        }
        
        $usersMedia = $this->getUserMediaTable()->getMedia($id);
        
        if ($usersMedia == false) {
			$this->flashMessenger()->addErrorMessage('User media not found..!!');
			return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
		}
		
        $form = new UsersMediaForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $old_image = $usersMedia->media_url;
        $form->bind($usersMedia);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
			
			$File = $this->params()->fromFiles('media_url');
			
			// Make certain to merge the files info!
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				 array('media_url'=> $File['name'])
			);
			
            $form->setInputFilter($usersMedia->getInputFilter());
            $form->setData($post);
            $adapter = new \Zend\File\Transfer\Adapter\Http();

            if ($form->isValid()) {
				
				$S3 = new ImageS3;
				$formData = $form->getData();
				
				if ($File['name'] != "") {
					
					$validator = new IsImage();
					if ($validator->isValid($File['tmp_name'])) {
						
						/* Image uploading code starts */
						$size = new Size(array('min'=>200,'max'=> 20000000));
						
						$adapter->setValidators(array($size), $File['name']);
						
						if (!$adapter->isValid()){	
						
							$dataError = $adapter->getMessages();
							
							return array('form' => $form, 'file_errors' => $dataError, 'user_id' => $user_id);
						}else{
							
							$data = $S3->uploadFiles($_FILES['media_url'],"Media",array(), array('Media' => 100, 'Media_thumb' => 20));
							
							if(is_array($data) && count($data) > 0 ){
								$formData->media_url = $data['Media'];
								
								// deleting old image
								$S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/','',$old_image));
								$S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/','',str_replace('Media','Media_thumb',$old_image)));
							}
						}
						/* Image uploading code ends */
					} else {
						return array('form' => $form, 'file_errors' => $validator->getMessages(), 'user_id' => $user_id);
					}
				} else {
					$formData->media_url = $old_image;
				}
				
                $this->getUserMediaTable()->saveUserMedia($form->getData());
                $this->flashMessenger()->addSuccessMessage('User media updated successfully..!!');

                // Redirect to list of media
                return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
            }
        }

        return array(
            'id' => $id,
            'usermedia' => $usersMedia,
            'user_id' => $user_id,
            'errors' => $this->errors,
            'form' => $form,
        );
         
	}
		
    public function deleteAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
		$user_id = (int) $this->params()->fromRoute('user_id', 0);
		
		$user = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUser($user_id);
        
        if ($user == false) {
			$this->flashMessenger()->addErrorMessage('User not found..!!');
			return $this->redirect()->toRoute('admin/users');
		}
		
        if (!$id) {
            return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
        }
        
        $usersMedia = $this->getUserMediaTable()->getMedia($id);
        
        if ($usersMedia == false) {
			$this->flashMessenger()->addErrorMessage('User media not found..!!');
			return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
		}

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                
                $usermedia = $this->getUserMediaTable()->getMedia($id);
				
				/* Deleting Images from amazon - starts here */
				$S3 = new ImageS3;
				$S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/','',$usermedia->media_url));
				$S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/','',str_replace('Media','Media_thumb',$usermedia->media_url)));
				/* Deleting Images from amazon - ends here */
				
			$this->getUserMediaTable()->deleteUserMedia($id); // Delete recode from database
			$this->flashMessenger()->addSuccessMessage('User Media deleted successfully..!!');
            }

            // Redirect to list of banners
            return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
        }

        return array(
            'id'    => $id,
            'user_id'    => $user_id,
            'usermedia' => $this->getUserMediaTable()->getMedia($id)
        );
        
	}
	
	public function changeStatusAction()
    {
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$status = $request->getPost('status', '1');
			if ($id != null && $status != null) {
				$this->getUserMediaTable()->changeStatus($id, $status);
				echo json_encode(array("msg"=>"Status successfully changed..!!"));
			} else {
				echo json_encode(array("msg"=>"Failed to change the status..!!"));
			}
			exit;
		}
	}
	
	public function avtarAction()
	{
		$id = (int) $this->params()->fromRoute('id', 0);
		$user_id = (int) $this->params()->fromRoute('user_id', 0);
		
		$user = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUser($user_id);
        
        if ($user == false) {
			$this->flashMessenger()->addErrorMessage('User not found..!!');
			return $this->redirect()->toRoute('admin/users');
		}
		
        if (!$id) {
            return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
        }
        
        $usersMedia = $this->getUserMediaTable()->getMedia($id);
        
        $file = explode('/', $usersMedia->media_url);
        $fileName = "./public/uploads/".end($file);
        file_put_contents($fileName, fopen($usersMedia->media_url, 'r'));
        
		$S3 = new ImageS3;
		$data = $S3->uploadFile($fileName, array('Avtars' => '378x378'));
		$old_image = $this->getUserMediaTable()->getUserAvtar($user_id);
		($old_image != '')?$S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/','',$old_image)):'';
		$this->getUserMediaTable()->setUserAvtar($user_id, $data['Avtars']);
		
		$this->flashMessenger()->addSuccessMessage('Avtar image uploaded successfully..!!');
		return $this->redirect()->toRoute('admin/usersmedia', array('user_id' => $user_id));
	}


}

