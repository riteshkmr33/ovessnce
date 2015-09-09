<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Messages;
use Admin\Form\ComposeMessageForm;
use Admin\Form\ViewMessageForm;
use Zend\Session\Container;


class MessagesController extends AbstractActionController
{
	protected $MessagesTable;
	
	public function indexAction()
    {
        $paginator = $this->getMessagesTable()->fetchAll();   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'messages' => $paginator,
		));
        
    }
    
    public function inboxAction()
    {
		
		$user_details = new Container('user_details');
		$details = $user_details->details;
		
		$paginator = $this->getMessagesTable()->fetchAll(true, "inbox" ,$details['user_id']);   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'messages' => $paginator,
		));
		
	}
	
    public function outboxAction()
    {
		
		$user_details = new Container('user_details');
		$details = $user_details->details;
		
		$paginator = $this->getMessagesTable()->fetchAll(true, "outbox" ,$details['user_id']);   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'messages' => $paginator,
		));
		 
	}
	
	public function composeAction()
    {
		$form = new ComposeMessageForm($this->getServiceLocator()->get('Admin\Model\UsersTable'));
		$form->get('submit')->setValue('Add');
		
		$request = $this->getRequest();
		if($request->isPost()){
			
			$messages = new Messages();
			$form->setInputFilter($messages->getInputFilter());
			$form->setData($request->getPost());
			
			if ($form->isValid()) {
				$messages->exchangeArray($form->getData());
				$this->getMessagesTable()->saveMessage($messages,$request->getPost('to_user_id'));
				return $this->redirect()->toRoute('admin/messages', array('action' => 'outbox'));
			}
		}
		return array('form' => $form);
	}
	
    public function trashAction()
    {
		
		$paginator = $this->getMessagesTable()->fetchAll(true, "trash");   
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(10);  
		
		return new ViewModel(array(
			'messages' => $paginator,
		));
		 
	}
	
	public function viewAction()
	{
		
		$id = (int) $this->params()->fromRoute('id', 0);
		
        if (!$id) {
            return $this->redirect()->toRoute('admin/messages', array(
                'action' => 'inbox'
            ));
        }
        
        $master_message = $this->getMessagesTable()->getMessage($id);
        $msg_error = '';
        $replyMessage = '';
        
        if($master_message->readFlag != "1"){
			
			$master_message->readFlag = "1"; // set readflag to 1 
			$this->getMessagesTable()->saveMessage($master_message,$id);
		}
	
		$replies = $this->getMessagesTable()->fetchAllReplies($master_message->topLevel_id);
			
		$request = $this->getRequest();
        if ($request->isPost()) {
			
			/* get current logged in user details - start here */
			$user_details = new Container('user_details');
			$details = $user_details->details;
			/* get current logged in user details - ends here */
					
			$formData = $request->getPost();
			$reply_id = $formData['replyId'];

            $data['subject'] = $formData["subject$reply_id"];
            $data['message'] = $formData["ReplyMessage$reply_id"];
            $data['to_user_id'] = $formData["toUserID$reply_id"];
            $data['from_user_id'] = $details['user_id'];
            $data['replyId'] = $formData["replyId"];
            $data['topLevel_id'] = $formData["topLevel_id$reply_id"];
            $data['from_name'] = $details['user_name'];
            $data['readFlag'] = '0';
			$data['deleteFlag'] = '0';
			$data['created_date'] = date('Y-m-d H:i:s');
			
			if($data['message']!=''){			
				
				if($this->getMessagesTable()->saveReply($data)){
					
					$replyMessage = "Reply Submitted Successfully";
					$replies = $this->getMessagesTable()->fetchAllReplies($master_message->topLevel_id);
					
				}else{
					$replyMessage = "Error! Reply Could not be submitted";
				}
					
			}else{
				$msg_error = "Reply cannot be empty";
			}
        }
        
        $user_details = new Container('user_details');
        $current_user_id = $user_details->details['user_id'];
		
        return array(
            'id' => $id,
            'master_message' => $master_message,
            'replies' => $replies,
			'msg_error' => $msg_error,
			'replyMessage' => $replyMessage,
			'current_user_id' => $current_user_id
        );
        
        
	}
    
    public function readAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);
		
        if (!$id) {
            return $this->redirect()->toRoute('admin/messages', array(
                'action' => 'inbox'
            ));
        }
        
        $master_message = $this->getMessagesTable()->getMessage($id);   
           
        if($master_message->readFlag != "1"){
			$master_message->readFlag = "1"; // set readflag to 1 
			$this->getMessagesTable()->saveMessage($master_message,$id);
		}
		
		$replies = $this->getMessagesTable()->fetchAllReplies($master_message->topLevel_id);
			
		
        return array(
            'id' => $id,
            'master_message' => $master_message,
            'replies' => $replies,
        );
	}
		
    public function deleteAction()
    {
		
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/messages', array('action' => 'inbox'));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getMessagesTable()->deleteMessage($id);
            }

            // Redirect to list of pages
            return $this->redirect()->toRoute('admin/messages', array('action' => 'inbox'));
        }

        return array(
            'id'    => $id,
            'messages' => $this->getMessagesTable()->getMessage($id)
        );
         
	}	
	
    public function undodeleteAction()
    {
		
		$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/messages', array('action' => 'trash'));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getMessagesTable()->undodeleteMessage($id);
            }

            // Redirect to list of pages
            return $this->redirect()->toRoute('admin/messages', array('action' => 'trash'));
        }

        return array(
            'id'    => $id,
            'messages' => $this->getMessagesTable()->getMessage($id)
        );
         
	}	
	
	public function getMessagesTable()
	{
		if (!$this->MessagesTable) {
			$sm = $this->getServiceLocator();
			$this->MessagesTable = $sm->get('Admin\Model\MessagesTable');
		}
	
		return $this->MessagesTable;
	}
	
}
