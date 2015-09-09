<?php

namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class UserRights implements InputFilterAwareInterface
{
	public $id;
    public $user_id;
    public $module_id;
    public $module_name;
    public $can_add;
    public $can_edit;
    public $can_view;
    public $can_del;
    protected $inputFilter;                       // <-- Add this variable

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
		$this->module_id = (!empty($data['module_id'])) ? $data['module_id'] : null;
		$this->module_name = (!empty($data['module_name'])) ? $data['module_name'] : null;
		$this->can_add = (!empty($data['can_add'])) ? $data['can_add'] : null;
		$this->can_edit = (!empty($data['can_edit'])) ? $data['can_edit'] : null;
		$this->can_view = (!empty($data['can_view'])) ? $data['can_view'] : null;
		$this->can_del = (!empty($data['can_del'])) ? $data['can_del'] : null;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    // Add content to this method: 
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
		
		if (!$this->inputFilter) {
			
            $inputFilter = new InputFilter();
                        
            $inputFilter->add(array(
                'name'     => 'user_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'module_id',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'can_add',
                'required' => false,
            ));
            
            $inputFilter->add(array(
                'name'     => 'can_edit',
                'required' => false,
            ));
            
            $inputFilter->add(array(
                'name'     => 'can_view',
                'required' => false,
            ));
            
            $inputFilter->add(array(
                'name'     => 'can_del',
                'required' => false,
            ));
                        
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}

}
		
