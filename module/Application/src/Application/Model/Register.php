<?php
namespace Application\Model;
 
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
 
class Register implements InputFilterAwareInterface
{
    public $user_type_id;
    public $first_name;
    public $last_name;
    public $email;
    public $user_name;
    public $Pass;
    public $confirm_password;
    
    protected $inputFilter;
     
    public function exchangeArray($data)
    {
        $this->user_type_id  = (isset($data['user_type_id']))  ? $data['user_type_id']     : null; 
        $this->first_name  = (isset($data['first_name']))  ? $data['first_name']     : null; 
        $this->last_name  = (isset($data['last_name']))  ? $data['last_name']     : null; 
        $this->email  = (isset($data['email']))  ? $data['email']     : null; 
        $this->user_name  = (isset($data['user_name']))  ? $data['user_name']     : null; 
        $this->Pass  = (isset($data['Pass']))  ? $data['Pass']     : null; 
        $this->confirm_password  = (isset($data['confirm_password']))  ? $data['confirm_password']     : null; 
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
                                'break_chain_on_failure' => true
                            ),
                        ),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'user_name',
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
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Username is required',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'first_name',
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
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'First name is required',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'last_name',
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
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Last name is required',
                                ),
                                'break_chain_on_failure' => true,
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
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'confirm_email',
                    //'required' => true,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Confirm email is required',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                        array(
                            'name'    => 'Identical',
                            'options' => array(
                                'token' => 'email',
                                'messages' => array(
                                    \Zend\Validator\Identical::NOT_SAME => 'Confirm email does not match',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                    ),
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'Pass',
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
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Password is required',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                        array(
                                'name' => 'Regex',
                                'options' => array(
                                    'pattern' => '/^.*(?=.{6,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).*$/',
                                    'messages' => array(
                                        \Zend\Validator\Regex::NOT_MATCH => 'Password must be at least 6 characters and must contain at least one lower case letter, one upper case letter, one digit and one special character.',
                                    ),
                                ),
                                'break_chain_on_failure' => true
                            ),
                        ),
                
                ))
            );
            
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'confirm_password',
                    //'required' => true,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    \Zend\Validator\NotEmpty::IS_EMPTY => 'Confirm password is required',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                        array(
                            'name'    => 'Identical',
                            'options' => array(
                                'token' => 'Pass',
                                'messages' => array(
                                    \Zend\Validator\Identical::NOT_SAME => 'Confirm password does not match',
                                ),
                                'break_chain_on_failure' => true,
                            ),
                        ),
                    ),
                ))
            );
            
            
            
            /* For file upload
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'fileupload',
                    'required' => true,
                ))
            );
            */
            $this->inputFilter = $inputFilter;
        }
         
        return $this->inputFilter;
    }
    
  
}
