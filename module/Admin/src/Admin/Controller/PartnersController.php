<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Partners;
use Admin\Form\PartnersForm;
use Zend\Validator\File\Size;
use Zend\ImageS3;
use Zend\Validator\File\ImageSize;
use \PHPExcel;

class PartnersController extends AbstractActionController
{

    protected $PartnersTable;

    public function indexAction()
    {

        $paginator = $this->getPartnersTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'partners' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2, 3)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {

        $form = new PartnersForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('logo');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('logo' => $File['name'])
            );

            $partners = new Partners();
            $form->setInputFilter($partners->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {

                $formData = array();
                $formData = $form->getData();

                $size = new Size(array('min' => 200, 'max' => 2000000));
                $imageSize = new ImageSize(array('minWidth' => '205', 'minHeight' => '250', 'maxWidth' => '1000', 'maxHeight' => '1000'));

                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size, $imageSize), $File['name']);

                if (!$adapter->isValid()) {

                    $dataError = $adapter->getMessages();

                    $error = array();
                    foreach ($dataError as $key => $row) {
                        $error[] = $row;
                    }
                    $form->setMessages(array('logo' => $error));
                    return array('form' => $form);
                } else {


                    $adapter = new \Zend\File\Transfer\Adapter\Http();
                    $adapter->setDestination('./public/uploads');

                    if ($adapter->receive($File['name'])) {

                        $fileName = "./public/uploads/" . $File['name'];
                        $S3 = new ImageS3;
                        $data = $S3->uploadFile($fileName, array('PartnersLogo' => '205x235\!'));
                    }

                    if (is_array($data) && count($data) > 0) {
                        $formData['logo'] = $data['PartnersLogo'];
                    }
                }

                $partners->exchangeArray($formData);
                $this->getPartnersTable()->savePartner($partners);
                $this->flashMessenger()->addSuccessMessage('Partner added successfully..!!');

                // Redirect to list of Partners
                return $this->redirect()->toRoute('admin/partners');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/partners', array(
                        'action' => 'add'
            ));
        }
        $partner = $this->getPartnersTable()->getPartner($id);
        if ($partner == false) {
            $this->flashMessenger()->addErrorMessage('Partner not found..!!');
            return $this->redirect()->toRoute('admin/partners');
        }

        $form = new PartnersForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $old_logo = $partner->logo;
        $form->bind($partner);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('logo');
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('logo' => $File['name'])
            );

            $form->setInputFilter($partner->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {

                $formData = $form->getData();

                $size = new Size(array('min' => 200, 'max' => 2000000));
                $imageSize = new ImageSize(array('minWidth' => '205', 'minHeight' => '235', 'maxWidth' => '1000', 'maxHeight' => '1000'));

                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size, $imageSize), $File['name']);

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
                        $S3 = new ImageS3;
                        $S3->deleteFile($old_logo);
                    }

                    $S3 = new ImageS3;
                    $adapter->setDestination('./public/uploads');

                    if ($adapter->receive($File['name'])) {
                        $fileName = "./public/uploads/" . $File['name'];
                        $data = $S3->uploadFile($fileName, array('PartnersLogo' => '205x235\!'));
                    }

                    if (is_array($data) && count($data) > 0) {

                        $formData->logo = $data['PartnersLogo'];
                    }
                }

                $this->getPartnersTable()->savePartner($form->getData());
                $this->flashMessenger()->addSuccessMessage('Partner updated successfully..!!');

                // Redirect to list of consumers
                return $this->redirect()->toRoute('admin/partners');
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
            return $this->redirect()->toRoute('admin/partners');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');

                /* Deleting Images from amazon - starts here */
                $partner = $this->getPartnersTable()->getPartner($id);
                if ($partner->logo) {

                    $AllImages = array();
                    $S3 = new ImageS3;
                    $S3Path = "https://ovessence.s3.amazonaws.com/PartnersLogo/Original/";

                    $AllImages[] = str_replace($S3Path, 'Logo/Small/', $partner->logo);
                    $AllImages[] = str_replace($S3Path, 'Logo/Medium/', $partner->logo);
                    $AllImages[] = str_replace($S3Path, 'Logo/Large/', $partner->logo);
                    $AllImages[] = str_replace($S3Path, 'Logo/Original/', $partner->logo);

                    if (count($AllImages) > 0) {
                        foreach ($AllImages as $image) {
                            $S3->deleteFile($image);
                        }
                    }
                }
                /* Deleting Images from amazon - ends here */

                $this->getPartnersTable()->deletePartner($id); // Delete recode from database
                $this->flashMessenger()->addSuccessMessage('Partner deleted successfully..!!');
            }

            // Redirect to list of certifications
            return $this->redirect()->toRoute('admin/partners');
        }

        return array(
            'id' => $id,
            'partner' => $this->getPartnersTable()->getPartner($id)
        );
    }

    public function getPartnersTable()
    {

        if (!$this->PartnersTable) {
            $sm = $this->getServiceLocator();
            $this->PartnersTable = $sm->get('Admin\Model\PartnersTable');
        }

        return $this->PartnersTable;
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getPartnersTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

    public function exportAction()
    {
        $request = $this->getRequest();
        $getData = (array) $request->getQuery();

        if (count($getData) > 0) {
            $filter = array();
            $postedData = $getData;

            // Filter fields goes here

            $results = $this->getPartnersTable()->ExportAll($filter);
        } else {
            $results = $this->getPartnersTable()->ExportAll();
        }

        if (count($results) > 0) {
            $row = 1;

            $xls = new PHPExcel();
            $xls->getProperties()->setCreator("Ovessence")
                    ->setLastModifiedBy("Ovessence Admin")
                    ->setTitle("Ovessence Partners")
                    ->setSubject("Ovessence Partners")
                    ->setDescription("")
                    ->setKeywords("ovessence Partners")
                    ->setCategory("Sale report file");
            $xls->getActiveSheet()->setTitle("Ovessence Partners");

            /* Styling code starts here */
            $xls->getActiveSheet()->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
            $xls->getActiveSheet()->getStyle('A' . $row . ':D' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            /* Styling code ends here */

            $xls->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, 'S.no.')
                    ->setCellValue('B' . $row, 'Title')
                    ->setCellValue('C' . $row, 'Description')
                    ->setCellValue('D' . $row, 'Url');
            foreach ($results as $result) {

                $row++;
                $xls->setActiveSheetIndex(0)
                        ->setCellValue('A' . $row, ($row - 1))
                        ->setCellValue('B' . $row, (isset($result->title) && $result->title != "") ? $result->title : 'NA')
                        ->setCellValue('C' . $row, (isset($result->desc) && $result->desc != "") ? $result->desc : 'NA')
                        ->setCellValue('D' . $row, (isset($result->url) && $result->url != "") ? $result->url : 'NA');
            }

            require_once './vendor/phpexcel/phpexcel/Classes/PHPExcel/IOFactory.php';

            $objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');

            // If you want to output e.g. a PDF file, simply do:
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save('./public/uploads/MyExcel.xlsx');
            header("Content-disposition: attachment; filename=Ovessence_practitioner_organizations(" . date('d-M-Y') . ").xlsx");
            header("Content-type: application/vnd.ms-excel");
            readfile("./public/uploads/MyExcel.xlsx");
            unlink("./public/MyExcel.xlsx");
            exit;
        } else {
            $this->flashMessenger()->addErrorMessage('No records found to export..!!');
            return $this->redirect()->toRoute('admin/organizations');
        }
    }

}
