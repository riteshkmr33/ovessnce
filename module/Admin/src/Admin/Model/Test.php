<?php 
namespace Admin\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Test implements InputFilterAwareInterface
{
	public $id;
	public $name;
	public $contactNumber;
	public $status;
	public $document;
	public $language;
	
	public function exchangeArray($data){
		$this->id = (!empty($data['id']))?($data['id']):0;
		$this->name = (!empty($data['name']))?($data['name']):null;
		$this->contactNumber = (!empty($data['contactNumber']))?($data['contactNumber']):null;
		$this->status = (!empty($data['status']))?($data['status']):'1';
		$this->document = (!empty($data['document']))?($data['document']):'';
		$this->country = (!empty($data['country']))?($data['country']):null;
		$this->language = (!empty($data['language']))?($data['language']):null;
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
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                      array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 30,
                        ),
                    ),
                ),
                
            ));
            
            $inputFilter->add(array(
				'name'     => 'contactNumber',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
									array(
										'name'    => 'Digits',
									),
                                 array(
									'name'    => 'StringLength',
									'options' => array(
									'encoding' => 'UTF-8',
									'min'      => 1,
									'max'      => 30,
									),
								),
							),
				));
				
			$inputFilter->add(array(
							'name'=>'language',
							'required'=>true							
							));
            
			$inputFilter->add(array(
							'name'=>'country',
							'required'=>true							
							));
		           
            $this->inputFilter = $inputFilter;
            
        }
        
        return $this->inputFilter;
	
	}
	}
?>
