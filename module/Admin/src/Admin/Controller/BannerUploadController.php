<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\BannerUploads;
use Admin\Form\BannerUploadForm;
use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use \Zend\Validator\File\IsImage;
use Zend\Session\Container;
use Zend\Http\Request;
use Zend\ImageS3;

class BannerUploadController extends AbstractActionController
{

    private $getBannerUploadTable;
    public $errors = array();

    private function getBannerUploadTable()
    {
        if (!$this->getBannerUploadTable) {
            $this->getBannerUploadTable = $this->getServiceLocator()->get('Admin\Model\BannerUploadsTable');
        }

        return $this->getBannerUploadTable;
    }

    public function indexAction()
    {
        $booking_id = (int) $this->params()->fromRoute('booking_id', 0);
        if (!$booking_id) {
            return $this->redirect()->toRoute('admin/bannerbookings');
        }

        $paginator = $this->getBannerUploadTable()->fetchAll($booking_id);
        $paginator->setCurrentPageNumber((int) $this->Params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'banneruploads' => $paginator,
            'booking_id' => $booking_id,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(1, 2)),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $booking_id = (int) $this->params()->fromRoute('booking_id', 0);
        if (!$booking_id) {
            return $this->redirect()->toRoute('admin/bannerbookings');
        }

        $bannerDetails = $this->getServiceLocator()->get('Admin\Model\AdvertisementPlanTable')->getBannerDetails($booking_id);

        $form = new BannerUploadForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('banner_content');
            $details = $this->getBannerUploadTable()->getBookingDetails($booking_id);

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('banner_content' => $File['name'], 'user_id' => $details['user_id'], 'banner_type_id' => $details['banner_type_id'])
            );

            $bu = new BannerUploads();
            $form->setInputFilter($bu->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {

                $formData = array();
                $formData = $form->getData();

                if ($post['banner_type'] == '1') {

                    $validator = new IsImage();
                    if ($validator->isValid($File['tmp_name'])) {


                        /* Image uploading code starts */
                        $size = new Size(array('min' => 200, 'max' => 20000000));
                        $imageSize = new ImageSize(array('minWidth' => $bannerDetails->banner_width, 'minHeight' => $bannerDetails->banner_height, 'maxWidth' => ($bannerDetails->banner_width + 10), 'maxHeight' => ($bannerDetails->banner_height + 10)));

                        $adapter = new \Zend\File\Transfer\Adapter\Http();
                        $adapter->setValidators(array($size, $imageSize), $File['name']);

                        if (!$adapter->isValid()) {

                            $dataError = $adapter->getMessages();

                            return array('form' => $form, 'file_errors' => $dataError, 'booking_id' => $booking_id, 'details' => $bannerDetails);
                        } else {

                            $S3 = new ImageS3;
                            $data = $S3->uploadFiles($_FILES['banner_content'], "Banners", array());

                            if (is_array($data) && count($data) > 0) {
                                $formData['banner_content'] = $data['Original'];
                            }
                        }
                        /* Image uploading code ends */
                    } else {
                        return array('form' => $form, 'file_errors' => $validator->getMessages(), 'booking_id' => $booking_id, 'details' => $bannerDetails);
                    }
                } else if ($post['banner_type'] == '2') {

                    $config = $this->getServiceLocator()->get('Config');
                    $vimeo = new \phpVimeo('e19e9bce5bb95d7b8e0fc5ef61feb6582d3c9e19', 'cb64548284bd805d4a5286b9fa731d3c124d98dc');
                    $session = new Container('vimeo');
                    $request = new Request();
                    // Get a new request token
                    $token = $vimeo->getRequestToken();

                    // Store it in the session
                    $session->oauth_request_token = $token['oauth_token'];
                    $session->oauth_request_token_secret = $token['oauth_token_secret'];
                    $session->vimeo_state = 'start';
                    header('Location: ' . $vimeo->getAuthorizeUrl($token['oauth_token'], 'write'));
                    exit;

                    /* $vimeo = new \phpVimeo($config['Vimeossss']['clientId'], $config['Vimeossss']['clientSecrate'], $token['oauth_token'], $token['oauth_access_token_secret']);
                      $video_id = $vimeo->upload('./vendor/baby.mp4');

                      var_dump($video_id); exit; */
                } else {
                    $formData['banner_content'] = $request->getPost('banner_content');
                }

                $bu->exchangeArray($formData);
                $this->getBannerUploadTable()->saveBannerUpload($bu);
                $this->flashMessenger()->addSuccessMessage('Banner added successfully..!!');

                // Redirect to list of certifications
                return $this->redirect()->toRoute('admin/banneruploads', array('booking_id' => $booking_id));
            } else {
                $this->errors = $form->getMessages();
            }
        }
        return array('form' => $form, 'errors' => $this->errors, 'booking_id' => $booking_id, 'details' => $bannerDetails);
    }

    public function editAction()
    {
        $booking_id = (int) $this->params()->fromRoute('booking_id', 0);
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id && !$booking_id) {
            return $this->redirect()->toRoute('admin/bannerbookings');
        }

        $bu = $this->getBannerUploadTable()->getBannerUpload($id);
        $bannerDetails = $this->getServiceLocator()->get('Admin\Model\AdvertisementPlanTable')->getBannerDetails($booking_id);

        $form = new BannerUploadForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $old_image = $bu->banner_content;
        $form->bind($bu);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('banner_content');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('banner_content' => $File['name'])
            );

            $form->setInputFilter($bu->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {

                $S3 = new ImageS3;
                $formData = $form->getData();

                if ($post['banner_type'] == '1') {

                    if ($File['name'] != "") {

                        $validator = new IsImage();
                        if ($validator->isValid($File['tmp_name'])) {

                            /* Image uploading code starts */
                            $size = new Size(array('min' => 200, 'max' => 20000000));
                            $imageSize = new ImageSize(array('minWidth' => $bannerDetails->banner_width, 'minHeight' => $bannerDetails->banner_height, 'maxWidth' => ($bannerDetails->banner_width + 10), 'maxHeight' => ($bannerDetails->banner_height + 10)));

                            $adapter = new \Zend\File\Transfer\Adapter\Http();
                            $adapter->setValidators(array($size, $imageSize), $File['name']);

                            if (!$adapter->isValid()) {

                                $dataError = $adapter->getMessages();

                                return array('form' => $form, 'file_errors' => $dataError, 'booking_id' => $booking_id, 'details' => $bannerDetails);
                            } else {

                                $data = $S3->uploadFiles($_FILES['banner_content'], "Banners", array());

                                if (is_array($data) && count($data) > 0) {
                                    $formData->banner_content = $data['Original'];

                                    // deleting old image
                                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $old_image));
                                }
                            }
                            /* Image uploading code ends */
                        } else {
                            return array('form' => $form, 'file_errors' => $validator->getMessages(), 'booking_id' => $booking_id, 'details' => $bannerDetails);
                        }
                    } else {
                        $formData->banner_content = $old_image;
                    }
                } else if ($post['banner_type'] == '2') {
                    
                } else {

                    // deleting old image
                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $old_image));

                    $formData->banner_content = $request->getPost('banner_content');
                }

                $this->getBannerUploadTable()->saveBannerUpload($form->getData());
                $this->flashMessenger()->addSuccessMessage('Banner updated successfully..!!');

                // Redirect to list of banners
                return $this->redirect()->toRoute('admin/banneruploads', array('booking_id' => $booking_id));
            }
        }

        return array(
            'id' => $id,
            'bannerUpload' => $bu,
            'errors' => $this->errors,
            'booking_id' => $booking_id,
            'form' => $form,
            'details' => $bannerDetails
        );
    }

    public function deleteAction()
    {
        $booking_id = (int) $this->params()->fromRoute('booking_id', 0);
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id && !$booking_id) {
            return $this->redirect()->toRoute('admin/bannerbookings');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');

                /* Deleting Images from amazon - starts here */
                $bu = $this->getBannerUploadTable()->getBannerUpload($id);
                if (stristr($bu->banner_content, 'https://ovessence.s3.amazonaws.com') != false) {

                    $S3 = new ImageS3;
                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $bu->banner_content));
                }
                /* Deleting Images from amazon - ends here */

                $this->getBannerUploadTable()->deleteBannerUpload($id); // Delete recode from database
            }

            // Redirect to list of banners
            return $this->redirect()->toRoute('admin/banneruploads', array('booking_id' => $booking_id));
        }

        return array(
            'id' => $id,
            'booking_id' => $booking_id,
            'bannerupload' => $this->getBannerUploadTable()->getBannerUpload($id)
        );
    }

    public function vimeologinAction()
    {
        $session = new Container('vimeo');
        $vimeo = new \phpVimeo('e19e9bce5bb95d7b8e0fc5ef61feb6582d3c9e19', 'cb64548284bd805d4a5286b9fa731d3c124d98dc');
        $vimeo->setToken($session->oauth_request_token, $session->oauth_request_token_secret);
        $token = $vimeo->getAccessToken($_REQUEST['oauth_verifier']);
        $session->oauth_access_token = $token['oauth_token'];
        $session->oauth_access_token_secret = $token['oauth_token_secret'];
        $vimeo = new \phpVimeo('e19e9bce5bb95d7b8e0fc5ef61feb6582d3c9e19', 'cb64548284bd805d4a5286b9fa731d3c124d98dc', $session->oauth_access_token, $session->oauth_access_token_secret);
        $video_id = $vimeo->upload('uploads/bunny.mp4');
        var_dump($video_id);
        exit;
    }
    
    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $status = $request->getPost('status', '1');
            if ($id != null && $status != null) {
                $this->getBannerUploadTable()->changeStatus($id, $status);
                echo json_encode(array("msg" => "Status successfully changed..!!"));
            } else {
                echo json_encode(array("msg" => "Failed to change the status..!!"));
            }
            exit;
        }
    }

}
