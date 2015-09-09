<?php
namespace Application\Model;
 
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
 
class Forgot implements InputFilterAwareInterface
{
    public $user_type_id;
	public $email;
    
    protected $inputFilter;
     
    public function exchangeArray($data)
    {
        $this->user_type_id  = (isset($data['user_type_id']))  ? $data['user_type_id'] : null; 
        $this->email  = (isset($data['email']))  ? $data['email'] : null; 
    } 
     
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
     
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
              
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'user_type_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'User Type is required',
                                ),
                            ),
                        ),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'email',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(                        
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Email is required',
                                ),
                                'break_chain_on_failure' => true
                            ),
                        ),
                        array(
                            'name' => 'EmailAddress',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Invalid email address',
                                ),
                                'break_chain_on_failure' => true
                            ),
                        ),
                    ),
                ))
            );
            
            $this->inputFilter = $inputFilter;
        }
         
        return $this->inputFilter;
    }
    
}
