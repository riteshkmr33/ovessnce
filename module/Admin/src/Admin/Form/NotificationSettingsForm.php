<?php
namespace Admin\Form;

 use Zend\Form\Form;
 use Admin\Model\Users;
 use Admin\Model\UsersTable;
 use Admin\Model\SiteModules;
 use Admin\Model\SiteModulesTable;

 class NotificationSettingsForm extends Form
 {
	 private $users;
	 private $modules;
	 
     public function __construct(UsersTable $users,SiteModulesTable $modules)
     {
		 $this->users = $users;
		 $this->modules = $modules;
		 	 
         // we want to ignore the name passed
         parent::__construct('notificationsettings');

         $this->add(array(
             'name' => 'id', 
             'type' => 'Hidden',
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'user_id',
             'options' => array(
                     'label' => 'User',
                     'value_options' => $this->getUsers(),
                     'empty_option'  => '--- Select Users ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2',
			 )
         ));
         
         $this->add(array(
             'type' => 'Zend\Form\Element\Select',
             'name' => 'module_id',
             'options' => array(
                     'label' => 'Module',
                     'value_options' => $this->getModules(),
                     'empty_option'  => '--- Select Module ---'
             ),
             'attributes' => array(
				'class'=>'form-control input-large select2',
			 )
         ));
                
         $this->add(array(
             'type' => 'Zend\Form\Element\Checkbox',
             'name' => 'sms_flag',   
             'options' => array(
                     'label' => 'Sms Flag',
             ),        
		 ));
		 
         $this->add(array(
             'type' => 'Zend\Form\Element\Checkbox',
             'name' => 'email_flag',      
             'options' => array(
                     'label' => 'Email Flag',
             ),      
		 ));
		 
         $this->add(array(
             'type' => 'Zend\Form\Element\Checkbox',
             'name' => 'page_alert_flag',  
             'options' => array(
                  'label' => 'Page Alert Flag',
             ),          
		 ));
		 
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
     }
    
    public function getUsers()
    {
        $data  = $this->users->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->user_name);
        }

        return $selectData; 
	}
	
    public function getModules()
    {
        $data  = $this->modules->fetchAll(false);

        $selectData = array();

        foreach ($data as $selectOption) {
            $selectData[$selectOption->id] = ucwords($selectOption->module_name);
        }

        return $selectData; 
	}
	
	
 }
