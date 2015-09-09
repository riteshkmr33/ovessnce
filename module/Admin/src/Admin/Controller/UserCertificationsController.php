<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\UserCertifications;
use Admin\Form\UserCertificationForm;
use Zend\Validator\File\Size;
use Zend\ImageS3;
use \PHPExcel;

class UserCertificationsController extends AbstractActionController
{

    protected $UserCertificationsTable;

    public function indexAction()
    {

        $paginator = $this->getUserCertificationsTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'certifications' => $paginator,
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {

        $form = new UserCertificationForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('logo');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('logo' => $File['name'])
            );

            $certification = new UserCertifications();
            $form->setInputFilter($certification->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {

                $formData = array();
                $formData = $form->getData();

                $size = new Size(array('min' => 200, 'max' => 2000000));

                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size), $File['name']);

                if (!$adapter->isValid()) {

                    $dataError = $adapter->getMessages();

                    $error = array();
                    foreach ($dataError as $key => $row) {
                        $error[] = $row;
                    }
                    $form->setMessages(array('logo' => $error));
                    return array('form' => $form);
                } else {

                    $S3 = new ImageS3;
                    $data = $S3->uploadFiles($_FILES['logo'], "Logo", array('Small' => 25, 'Medium' => 50, 'Large' => 75, 'Original' => 100));
                    if (is_array($data) && count($data) > 0) {
                        $formData['logo'] = $data['Original'];
                    }
                }

                $certification->exchangeArray($formData);
                $this->getUserCertificationsTable()->saveCertification($certification);
                $this->flashMessenger()->addSuccessMessage('User certification added successfully..!!');

                // Redirect to list of certifications
                return $this->redirect()->toRoute('admin/certifications');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/certifications', array(
                        'action' => 'add'
            ));
        }
        $certification = $this->getUserCertificationsTable()->getCertification($id);
        if ($certification == false) {
            $this->flashMessenger()->addErrorMessage('Certification not found..!!');
            return $this->redirect()->toRoute('admin/certifications');
        }

        $form = new UserCertificationForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $old_logo = $certification->logo;
        $form->bind($certification);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('logo');
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('logo' => $File['name'])
            );

            $form->setInputFilter($certification->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {

                $formData = $form->getData();

                $size = new Size(array('min' => 200, 'max' => 2000000));

                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size), $File['name']);

                if (!$adapter->isValid()) {

                    $dataError = $adapter->getMessages();

                    if (!$dataError['fileUploadErrorNoFile']) {

                        $error = array();
                        foreach ($dataError as $key => $row) {
                            $error[] = $row;
                        }
                        $form->setMessages(array('logo' => $error));
                        return array(
                            'id' => $id,
                            'form' => $form,
                        );
                    }
                } else {

                    if (isset($old_logo) && !empty($old_logo)) {

                        $AllImages = array();
                        $S3 = new ImageS3;
                        $S3Path = "https://ovessence.s3.amazonaws.com/Logo/Original/";

                        $AllImages[] = str_replace($S3Path, 'Logo/Small/', $old_logo);
                        $AllImages[] = str_replace($S3Path, 'Logo/Medium/', $old_logo);
                        $AllImages[] = str_replace($S3Path, 'Logo/Large/', $old_logo);
                        $AllImages[] = str_replace($S3Path, 'Logo/Original/', $old_logo);

                        if (count($AllImages) > 0) {
                            foreach ($AllImages as $image) {
                                $S3->deleteFile($image);
                            }
                        }
                    }

                    $S3 = new ImageS3;
                    $data = $S3->uploadFiles($_FILES['logo'], "Logo", array('Small' => 25, 'Medium' => 50, 'Large' => 75, 'Original' => 100));

                    if (is_array($data) && count($data) > 0) {

                        $formData->logo = $data['Original'];
                    }
                }

                $this->getUserCertificationsTable()->saveCertification($form->getData());
                $this->flashMessenger()->addSuccessMessage('User certification updated successfully..!!');

                // Redirect to list of consumers
                return $this->redirect()->toRoute('admin/certifications');
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
            return $this->redirect()->toRoute('admin/certifications');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');

                /* Deleting Images from amazon - starts here */
                $certification = $this->getUserCertificationsTable()->getCertification($id);
                if ($certification->logo) {

                    $AllImages = array();
                    $S3 = new ImageS3;
                    $S3Path = "https://ovessence.s3.amazonaws.com/Logo/Original/";

                    $AllImages[] = str_replace($S3Path, 'Logo/Small/', $certification->logo);
                    $AllImages[] = str_replace($S3Path, 'Logo/Medium/', $certification->logo);
                    $AllImages[] = str_replace($S3Path, 'Logo/Large/', $certification->logo);
                    $AllImages[] = str_replace($S3Path, 'Logo/Original/', $certification->logo);

                    if (count($AllImages) > 0) {
                        foreach ($AllImages as $image) {
                            $S3->deleteFile($image);
                        }
                    }
                }
                /* Deleting Images from amazon - ends here */

                $this->getUserCertificationsTable()->deleteCertification($id); // Delete recode from database
                $this->flashMessenger()->addSuccessMessage('User certification delete successfully..!!');
            }

            // Redirect to list of certifications
            return $this->redirect()->toRoute('admin/certifications');
        }

        return array(
            'id' => $id,
            'certifications' => $this->getUserCertificationsTable()->getCertification($id)
        );
    }

    public function getUserCertificationsTable()
    {

        if (!$this->UserCertificationsTable) {
            $sm = $this->getServiceLocator();
            $this->UserCertificationsTable = $sm->get('Admin\Model\UserCertificationsTable');
        }

        return $this->UserCertificationsTable;
    }

    public function exportAction()
    {
        $request = $this->getRequest();
        $getData = (array) $request->getQuery();

        if (count($getData) > 0) {
            $filter = array();
            $postedData = $getData;

            // Filter fields goes here

            $results = $this->getUserCertificationsTable()->ExportAll($filter);
        } else {
            $results = $this->getUserCertificationsTable()->ExportAll();
        }

        //echo '<pre>'.count($results)."<br />"; print_r($results->current()); exit;

        if (count($results) > 0) {
            $row = 1;

            $xls = new PHPExcel();
            $xls->getProperties()->setCreator("Ovessence")
                    ->setLastModifiedBy("Ovessence Admin")
                    ->setTitle("Ovessence User Certifications")
                    ->setSubject("Ovessence User Certifications")
                    ->setDescription("")
                    ->setKeywords("ovessence User Certifications")
                    ->setCategory("Sale report file");
            $xls->getActiveSheet()->setTitle("Ovessence User Certifications");

            /* Styling code starts here */
            $xls->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
            $xls->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /* Styling code ends here */

            $xls->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, 'S.no.')
                    ->setCellValue('B' . $row, 'User')
                    ->setCellValue('C' . $row, 'Title')
                    ->setCellValue('D' . $row, 'Organization Name')
                    ->setCellValue('E' . $row, "Certification Date")
                    ->setCellValue('F' . $row, 'Expiration Date');
            foreach ($results as $result) {

                $row++;
                $xls->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, ($row - 1))
                        ->setCellValue('B' . $row, (isset($result->user_name) && $result->user_name != "") ? $result->user_name : 'NA')
                        ->setCellValue('C' . $row, (isset($result->title) && $result->title != "") ? $result->title : 'NA')
                        ->setCellValue('D' . $row, (isset($result->organization_name) && $result->organization_name != "") ? $result->organization_name : 'NA')
                        ->setCellValue('E' . $row, (isset($result->certification_date) && $result->certification_date != "") ? $result->certification_date : 'NA')
                        ->setCellValue('F' . $row, (isset($result->validity) && $result->validity != "") ? $result->validity : 'NA');
            }

            require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';

            $objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');

            // If you want to output e.g. a PDF file, simply do:
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save('./public/uploads/MyExcel.xlsx');
            header("Content-disposition: attachment; filename=Ovessence_user_certifications(" . date('d-M-Y') . ").xlsx");
            header("Content-type: application/vnd.ms-excel");
            readfile("./public/uploads/MyExcel.xlsx");
            unlink("./public/MyExcel.xlsx");
            exit;
        } else {
            $this->flashMessenger()->addErrorMessage('No records found to export..!!');
            return $this->redirect()->toRoute('admin/certifications');
        }
    }

}
