<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\ServiceProviderAvailability;
use Admin\Form\ServiceProviderAvailabilityForm;
use Zend\InputFilter\InputFilter;

class ServiceProviderAvailabilityController extends AbstractActionController
{

    private $getServiceProviderAvailabilityTable;
    public $errors = array();

    private function getSPATable()
    {
        if (!$this->getServiceProviderAvailabilityTable) {
            $this->getServiceProviderAvailabilityTable = $this->getServiceLocator()->get('Admin\Model\ServiceProviderAvailabilityTable');
        }

        return $this->getServiceProviderAvailabilityTable;
    }

    public function indexAction()
    {
        $user_id = (int) $this->params()->fromRoute('user_id', 0);
        if (!$user_id) {
            return $this->redirect()->toRoute('admin/serviceproviders');
        }

        $sp = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProvider($user_id);

        if ($sp == false) {
            $this->flashMessenger()->addErrorMessage('Service Provider not found..!!');
            return $this->redirect()->toRoute('admin/serviceproviders');
        }

        $form = new ServiceProviderAvailabilityForm();
        $spa = new ServiceProviderAvailability;
        //$form->bind($spa);
        $form->get('submit')->setAttribute('value', 'Update');
        
        $workAddress = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProviderServiceAddress($user_id);
        $address = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProviderAddress($user_id);
        
        $addresses = array();
        foreach ($workAddress as $add) {
            $addresses[$add->id] = $add->street1_address.', '.$add->city.', '.$add->state_name.' '.$add->zip_code.', '.$add->country_name;
        }
        isset($address->street1_address)?$addresses[$address->id] = $address->street1_address.', '.$address->city.', '.$address->state_name.' '.$address->zip_code.', '.$address->country_name:'';
        
        //echo '<pre>';  print_r($addresses); exit;
        //$filter = new InputFilter;
        $filter = $spa->getInputFilter();

        $h = 12;
        $m = 00;
        $i = 0;
        $timeSlots = array();

        while ($i <= 23) {
            $h = ($h < 10 && strstr($h, '0') == false) ? '0' . $h : $h;
            $m = ($m < 10 && strstr($m, '0') == false) ? '0' . $m : $m;
            $time = ($i > 11) ? $h . ':' . $m . ' PM' : $h . ':' . $m . ' AM';
            $timeSlots[$time] = $time;

            $m = $m + 15;
            if ($m == 60) {
                $h = ($h == 12) ? 1 : $h + 1;
                $m = 0;
                $i++;
            }
        }


        $days = $this->getSPATable()->getAvailabilityDays();
        foreach ($days as $day) {
            $form->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'start_time[' . $day->id . ']',
                'options' => array(
                    'label' => 'Start Time',
                    'value_options' => $timeSlots,
                    'empty_option' => 'Select'
                ),
                'attributes' => array(
                    'class' => 'form-control input-small select2',
                    'style' => 'margin-left:20px'
                )
            ));

            $form->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'end_time[' . $day->id . ']',
                'options' => array(
                    'label' => 'End Time',
                    'value_options' => $timeSlots,
                    'empty_option' => 'Select'
                ),
                'attributes' => array(
                    'class' => 'form-control input-small select2',
                    'style' => 'margin-left:20px'
                )
            ));

            $form->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'lunch_start_time[' . $day->id . ']',
                'options' => array(
                    'label' => 'Lunch Start Time',
                    'value_options' => $timeSlots,
                    'empty_option' => 'Select'
                ),
                'attributes' => array(
                    'class' => 'form-control input-small select2',
                    'style' => 'margin-left:20px'
                )
            ));

            $form->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'lunch_end_time[' . $day->id . ']',
                'options' => array(
                    'label' => 'Lunch End Time',
                    'value_options' => $timeSlots,
                    'empty_option' => 'Select'
                ),
                'attributes' => array(
                    'class' => 'form-control input-small select2',
                    'style' => 'margin-left:20px'
                )
            ));
            
            $form->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'address_id[' . $day->id . ']',
                'options' => array(
                    'label' => 'Workplace',
                    'value_options' => $addresses,
                    'empty_option' => 'Select'
                ),
                'attributes' => array(
                    'class' => 'form-control input-small select2',
                    'style' => 'margin-left:20px'
                )
            ));

            // adding validation rule at run time
            $filter->add(array('name' => 'start_time[' . $day->id . ']', 'required' => false));
            $filter->add(array('name' => 'end_time[' . $day->id . ']', 'required' => false));
            $filter->add(array('name' => 'lunch_start_time[' . $day->id . ']', 'required' => false));
            $filter->add(array('name' => 'lunch_end_time[' . $day->id . ']', 'required' => false));
            $filter->add(array('name' => 'address_id[' . $day->id . ']', 'required' => false));

            $fields[$day->id] = $day->day;
        }



        $request = $this->getRequest();
        if ($request->isPost()) {

            $post = $request->getPost();

            $form->setInputFilter($filter);
            $form->setData($post);

            if ($form->isValid()) {

                $formData = $form->getData();

                $this->getSPATable()->saveServiceProviderAvailability($user_id, $request->getPost('start_time'), $request->getPost('end_time'), $request->getPost('lunch_start_time'), $request->getPost('lunch_end_time'), $request->getPost('delay_time'), $request->getPost('address_id'));
                $this->flashMessenger()->addSuccessMessage('Service Provider Availability updated successfully..!!');

                // Redirect to list of banners
                return $this->redirect()->toRoute('admin/serviceprovideravailability', array('user_id' => $user_id));
            } else {
                $this->errors = $form->getMessages();
            }
        }

        return new ViewModel(array('form' => $form, 'user_id' => $user_id, 'errors' => $this->errors, 'fields' => $fields, 'user_id' => $user_id, 'spa' => $this->getSPATable(), 'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages()));
    }

}
