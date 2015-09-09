<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\SiteBanners;
use Admin\Form\SiteBannerForm;
use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use \Zend\Validator\File\IsImage;
use Zend\Session\Container;
use Zend\Http\Request;
use Zend\ImageS3;

class SiteBannerController extends AbstractActionController
{

    private $getBannerTable;

    private function getBannerTable()
    {
        if (!$this->getBannerTable) {
            $this->getBannerTable = $this->getServiceLocator()->get('Admin\Model\SiteBannersTable');
        }

        return $this->getBannerTable;
    }

    public function indexAction()
    {
        $paginator = $this->getBannerTable()->fetchAll();
        $paginator->setCurrentPageNumber((int) $this->Params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array('sitebanners' => $paginator,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new SiteBannerForm($this->getServiceLocator()->get('Admin\Model\AdvertisementPageTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        //$form = new BannerForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $banner = new SiteBanners();

            $File = $this->params()->fromFiles('banner_url');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('banner_url' => $File['name'])
            );

            $form->setInputFilter($banner->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {

                $formData = array();
                $formData = $form->getData();

                $validator = new IsImage();
                if ($validator->isValid($File['tmp_name'])) {


                    /* Image uploading code starts */
                    $size = new Size(array('min' => 200, 'max' => 20000000));

                    $adapter = new \Zend\File\Transfer\Adapter\Http();
                    $adapter->setValidators(array($size), $File['name']);

                    if (!$adapter->isValid()) {

                        $dataError = $adapter->getMessages();

                        return array('form' => $form, 'file_errors' => $dataError);
                    } else {

                        $S3 = new ImageS3;
                        $data = $S3->uploadFiles($_FILES['banner_url'], "Banners", array());

                        if (is_array($data) && count($data) > 0) {
                            $formData['banner_url'] = $data['Original'];
                        }
                    }
                    /* Image uploading code ends */
                } else {
                    return array('form' => $form, 'file_errors' => $validator->getMessages());
                }

                $banner->exchangeArray($formData);
                $this->getBannerTable()->saveBanner($banner);
                $this->flashMessenger()->addSuccessMessage('Banner added successfully..!!');

                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/sitebanner');
            } else {
                $this->errors = $form->getMessages();
            }
        }
        return array('form' => $form, 'errors' => $this->errors);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sitebanner', array(
                        'action' => 'add'
            ));
        }
        $banner = $this->getBannerTable()->getBanner($id);

        if ($banner == false) {
            $this->flashMessenger()->addErrorMessage('Banner not found..!!');
            return $this->redirect()->toRoute('admin/sitebanner');
        }

        $form = new SiteBannerForm($this->getServiceLocator()->get('Admin\Model\AdvertisementPageTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $old_image = $banner->banner_url;
        $form->bind($banner);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('banner_url');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('banner_url' => $File['name'])
            );
            
            ($old_image!='')?$banner->getInputFilter()->get('banner_url')->setRequired(false):'';

            $form->setInputFilter($banner->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {
                
                $S3 = new ImageS3;
                $formData = $form->getData();

                if ($File['name'] != "") {

                    $validator = new IsImage();
                    if ($validator->isValid($File['tmp_name'])) {

                        /* Image uploading code starts */
                        $size = new Size(array('min' => 200, 'max' => 20000000));

                        $adapter = new \Zend\File\Transfer\Adapter\Http();
                        $adapter->setValidators(array($size), $File['name']);

                        if (!$adapter->isValid()) {

                            $dataError = $adapter->getMessages();

                            return array('form' => $form, 'file_errors' => $dataError);
                        } else {

                            $data = $S3->uploadFiles($_FILES['banner_url'], "Banners", array());

                            if (is_array($data) && count($data) > 0) {
                                $formData->banner_url = $data['Original'];

                                // deleting old image
                                $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $old_image));
                            }
                        }
                        /* Image uploading code ends */
                    } else {
                        return array('form' => $form, 'file_errors' => $validator->getMessages());
                    }
                } else {
                    $formData->banner_url = $old_image;
                }

                $this->getBannerTable()->saveBanner($formData);
                $this->flashMessenger()->addSuccessMessage('Banner updated successfully..!!');
                // Redirect to list of pages
                return $this->redirect()->toRoute('admin/sitebanner');
            } else {
                $this->errors = $form->getMessages();
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'errors' => $this->errors,
            'siteBanner' => $banner
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/sitebanner');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getBannerTable()->deleteBanner($id);
                $this->flashMessenger()->addSuccessMessage('Banner deleted successfully..!!');
            }

            // Redirect to list of pages 
            return $this->redirect()->toRoute('admin/sitebanner');
        }

        return array(
            'id' => $id,
            'sitebanner' => $this->getBannerTable()->getBanner($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getBannerTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

}
