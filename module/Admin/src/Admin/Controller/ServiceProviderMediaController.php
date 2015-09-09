<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\ServiceProviderMedia;
use Admin\Form\ServiceProviderMediaForm;
use Admin\Form\ServiceProviderMediaFilterForm;
use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use \Zend\Validator\File\IsImage;
use Zend\Session\Container;
use Zend\Http\Request;
use Zend\ImageS3;

class ServiceProviderMediaController extends AbstractActionController
{

    private $getServiceProviderMediaTable;

    private function getSPMTable()
    {
        if (!$this->getServiceProviderMediaTable) {
            $this->getServiceProviderMediaTable = $this->getServiceLocator()->get('Admin\Model\ServiceProviderMediaTable');
        }

        return $this->getServiceProviderMediaTable;
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

        $form = new ServiceProviderMediaFilterForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $request = $this->getRequest();
        $postedData = array();
        $getData = (array) $request->getQuery();
        unset($getData['page']);

        if ($request->isPost()) {
            $postedData = $request->getPost();
            $form->bind($postedData);
            $filter = array(
                'name' => trim($postedData['username']),
                'title' => trim($postedData['title']),
                'media_type' => trim($postedData['media_type']),
                'from_date' => ($postedData['from'] != "") ? date("Y-m-d", strtotime($postedData['from'])) : "",
                'to_date' => ($postedData['to'] != "") ? date("Y-m-d", strtotime($postedData['to'])) : "",
                'status_id' => $postedData['status_id'],
            );
            $paginator = $this->getSPMTable()->fetchAll($user_id, true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else if (count($getData) > 0) {
            $filter = array();
            $form->bind($request->getQuery());
            $postedData = $getData;

            isset($getData['username']) ? $filter['name'] = $getData['username'] : "";
            isset($getData['title']) ? $filter['title'] = $getData['title'] : "";
            isset($getData['media_type']) ? $filter['media_type'] = $getData['media_type'] : "";
            (isset($getData['from']) && $getData['from'] != "") ? $filter['from_date'] = date("Y-m-d", strtotime($getData['from'])) : "";
            (isset($getData['to']) && $getData['to'] != "") ? $filter['to_date'] = date("Y-m-d", strtotime($getData['to'])) : "";
            isset($getData['status_id']) ? $filter['status_id'] = trim($getData['status_id']) : "";

            $paginator = $this->getSPMTable()->fetchAll($user_id, true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else {
            $paginator = $this->getSPMTable()->fetchAll($user_id);
        }

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'serviceprovidermedia' => $paginator,
            'form' => $form,
            'user_id' => $user_id,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(9, 5, 10)),
            'postedData' => array_filter((array) $postedData),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
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

        $form = new ServiceProviderMediaForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('media_url');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('media_url' => $File['name'])
            );

            $spm = new ServiceProviderMedia();
            $form->setInputFilter($spm->getInputFilter());
            $form->setData($post);

            if ($form->isValid()) {

                $formData = array();
                $formData = $form->getData();

                $adapter = new \Zend\File\Transfer\Adapter\Http();

                if ($post['media_type'] == '1') {

                    $validator = new IsImage();
                    if ($validator->isValid($File['tmp_name'])) {

                        /* Image uploading code starts */
                        $size = new Size(array('min' => 200, 'max' => 20000000));

                        $adapter->setValidators(array($size), $File['name']);

                        if (!$adapter->isValid()) {

                            $dataError = $adapter->getMessages();

                            return array('form' => $form, 'file_errors' => $dataError, 'user_id' => $user_id);
                        } else {

                            $S3 = new ImageS3;
                            $data = $S3->uploadFiles($_FILES['media_url'], "Media", array(), array('Media' => 100, 'Media_thumb' => 20));

                            if (is_array($data) && count($data) > 0) {
                                $formData['media_url'] = $data['Media'];
                            }
                        }
                        /* Image uploading code ends */
                    } else {
                        return array('form' => $form, 'file_errors' => $validator->getMessages(), 'user_id' => $user_id);
                    }
                } else if ($post['media_type'] == '2') {

                    $subscriptionDetails = $this->getSPMTable()->getVideoUploadLimit($user_id);
                    $videoUploaded = $this->getSPMTable()->fetchAll($user_id, false, array('media_type' => 2));
                    if ($subscriptionDetails != false && $subscriptionDetails->limit > 0) {

                        if ($videoUploaded->count() < $subscriptionDetails->limit) {
                            $renameUpload = new \Zend\Filter\File\RenameUpload(array('target' => "./public/uploads/", 'randomize' => true, 'use_upload_name' => true));
                            if ($fileDetails = $renameUpload->filter($_FILES['media_url'])) {
                                $filePath = $fileDetails['tmp_name'];
                                // check video orientation and rotate if needed
                                /*exec("mediainfo " . $fileDetails['tmp_name'] . " | grep Rotation", $mediaInfo);
                                
                                if (is_array($mediaInfo) && count($mediaInfo) > 0) {
                                    $tempPath = explode("/", $fileDetails['tmp_name']);
                                    $filePath = "./public/uploads/new_" . end($tempPath);
                                    exec('ffmpeg -i ' . $fileDetails['tmp_name'] . ' -vf "transpose=1" -strict -2 ' . $filePath, $output, $response);
                                    ($response == '0') ? @unlink($fileDetails['tmp_name']) : '';
                                }*/

                                $session = new Container('vimeo');
                                $formData['media_url'] = $session->file = $filePath;
                                $session->mode = 'write';
                                $session->returnUrl = $this->url()->fromRoute('admin/serviceprovidermedia', array('user_id' => $user_id));

                                $spm->exchangeArray($formData);
                                $session->media_id = $this->getSPMTable()->saveServiceProviderMedia($spm);
                                if ($formData['status_id'] == 9) {
                                    return $this->redirect()->toRoute('admin/media', array('action' => 'vimeologin'));
                                }
                            } else {
                                return array('form' => $form, 'file_errors' => $adaptor->getMessages(), 'user_id' => $user_id);
                            }
                        } else {
                            $this->flashMessenger()->addErrorMessage('You have already uploaded maximum number of videos allowed in your subscription..!!');
                            return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
                        }
                    } else {
                        $this->flashMessenger()->addErrorMessage('Subscribed subscription don\'t have permission to upload videos or you have not subscribed any subscription..!!');
                        return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
                    }
                }

                $spm->exchangeArray($formData);
                $this->getSPMTable()->saveServiceProviderMedia($spm);
                $this->flashMessenger()->addSuccessMessage('Service Provider Media added successfully..!!');

                // Redirect to list of certifications
                return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
            } else {
                $this->errors = $form->getMessages();
            }
        }
        return array('form' => $form, 'errors' => $this->errors, 'user_id' => $user_id);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $user_id = (int) $this->params()->fromRoute('user_id', 0);

        $sp = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProvider($user_id);

        if ($sp == false) {
            $this->flashMessenger()->addErrorMessage('User not found..!!');
            return $this->redirect()->toRoute('admin/users');
        }

        if (!$id) {
            return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
        }

        $spMedia = $this->getSPMTable()->getMedia($id);

        if ($spMedia == false) {
            $this->flashMessenger()->addErrorMessage('Service provider media not found..!!');
            return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
        }

        $form = new ServiceProviderMediaForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $old_media = $spMedia->media_url;
        $form->bind($spMedia);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('media_url');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('media_url' => $File['name'])
            );

            $form->setInputFilter($spMedia->getInputFilter());
            $form->setData($post);
            $adapter = new \Zend\File\Transfer\Adapter\Http();

            if ($form->isValid()) {

                $S3 = new ImageS3;
                $formData = $form->getData();

                if ($post['media_type'] == '1') {

                    if ($File['name'] != "") {

                        $validator = new IsImage();
                        if ($validator->isValid($File['tmp_name'])) {

                            /* Image uploading code starts */
                            $size = new Size(array('min' => 200, 'max' => 20000000));

                            $adapter->setValidators(array($size), $File['name']);

                            if (!$adapter->isValid()) {

                                $dataError = $adapter->getMessages();

                                return array('form' => $form, 'file_errors' => $dataError);
                            } else {

                                $data = $S3->uploadFiles($_FILES['media_url'], "Media", array(), array('Media' => 100, 'Media_thumb' => 20));

                                if (is_array($data) && count($data) > 0) {
                                    $formData->media_url = $data['Media'];

                                    // deleting old image
                                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $old_image));
                                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', str_replace('Media', 'Media_thumb', $old_image)));
                                }
                            }
                            /* Image uploading code ends */
                        } else {
                            return array('form' => $form, 'file_errors' => $validator->getMessages());
                        }
                    } else {
                        $formData->media_url = $old_media;
                    }
                } else if ($post['media_type'] == '2') {

                    $subscriptionDetails = $this->getSPMTable()->getVideoUploadLimit($user_id);
                    $videoUploaded = $this->getSPMTable()->fetchAll($user_id, false, array('media_type' => 2), array(), 'id != ' . $post['id']);
                    if ($subscriptionDetails != false && $subscriptionDetails->limit > 0) {

                        if ($videoUploaded->count() < $subscriptionDetails->limit) {
                            if ($File['name'] != "") {
                                $renameUpload = new \Zend\Filter\File\RenameUpload(array('target' => "./public/uploads/", 'randomize' => true, 'use_upload_name' => true));
                                if ($fileDetails = $renameUpload->filter($_FILES['media_url'])) {

                                    $filePath = $fileDetails['tmp_name'];
                                    // check video orientation and rotate if needed
                                    /*exec("mediainfo " . $fileDetails['tmp_name'] . " | grep Rotation", $mediaInfo);

                                    if (is_array($mediaInfo) && count($mediaInfo) > 0) {
                                        $tempPath = explode("/", $fileDetails['tmp_name']);
                                        $filePath = "./public/uploads/new_" . end($tempPath);
                                        exec('ffmpeg -i ' . $fileDetails['tmp_name'] . ' -vf "transpose=1" -strict -2 ' . $filePath, $output, $response);
                                        ($response == '0') ? @unlink($fileDetails['tmp_name']) : '';
                                    }*/

                                    $session = new Container('vimeo');
                                    $formData->media_url = $session->file = $filePath;
                                    $session->mode = 'write';
                                    $session->returnUrl = $this->url()->fromRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
                                    $session->old_video = $old_media;

                                    $session->media_id = $this->getSPMTable()->saveServiceProviderMedia($form->getData());
                                    if ($formData->status_id == 9) {
                                        return $this->redirect()->toRoute('admin/media', array('action' => 'vimeologin'));
                                    }
                                } else {
                                    return array('form' => $form, 'file_errors' => $adaptor->getMessages());
                                }
                            } else {
                                $formData->media_url = $old_media;
                            }
                        } else {
                            $this->flashMessenger()->addErrorMessage('You have already uploaded maximum number of videos allowed in your subscription..!!');
                            return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
                        }
                    } else {
                        $this->flashMessenger()->addErrorMessage('Subscribed subscription don\'t have permission to upload videos or you have not subscribed any subscription..!!');
                        return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
                    }
                }

                $this->getSPMTable()->saveServiceProviderMedia($form->getData());
                $this->flashMessenger()->addSuccessMessage('Service Provider Media updated successfully..!!');

                // Redirect to list of banners
                return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
            }
        }

        return array(
            'id' => $id,
            'spmedia' => $spMedia,
            'user_id' => $user_id,
            'errors' => $this->errors,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $user_id = (int) $this->params()->fromRoute('user_id', 0);

        $sp = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProvider($user_id);

        if ($sp == false) {
            $this->flashMessenger()->addErrorMessage('Service Provider not found..!!');
            return $this->redirect()->toRoute('admin/serviceproviders');
        }

        if (!$id) {
            return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');

                $spmedia = $this->getSPMTable()->getMedia($id);
                if (stristr($spmedia->media_url, 'https://ovessence.s3.amazonaws.com') != false) {
                    /* Deleting Images from amazon - starts here */
                    $S3 = new ImageS3;
                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $spmedia->media_url));
                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', str_replace('Media', 'Media_thumb', $spmedia->media_url)));
                    /* Deleting Images from amazon - ends here */
                } else {
                    if (strstr($spmedia->media_url, 'uploads') == false) {
                        $session = new Container('vimeo');
                        $session->files = array($spmedia->media_url);
                        $session->mode = 'delete';
                        $session->returnUrl = $this->url()->fromRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
                        $this->getSPMTable()->deleteServiceProviderMedia($id); // Delete record from database
                        return $this->redirect()->toRoute('admin/media', array('action' => 'vimeologin'));
                    } else {
                        @unlink($spmedia->media_url);
                    }
                }
                $this->flashMessenger()->addSuccessMessage('Service Provider Media deleted successfully..!!');
                $this->getSPMTable()->deleteServiceProviderMedia($id); // Delete recode from database
            }

            // Redirect to list of banners
            return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
        }

        return array(
            'id' => $id,
            'user_id' => $user_id,
            'spmedia' => $this->getSPMTable()->getMedia($id)
        );
    }

    public function changeStatusAction()
    {
        $user_id = (int) $this->params()->fromRoute('user_id', 0);

        $sp = $this->getServiceLocator()->get('Admin\Model\ServiceProviderTable')->getServiceProvider($user_id);

        if ($sp == false) {
            $this->flashMessenger()->addErrorMessage('Service Provider not found..!!');
            return $this->redirect()->toRoute('admin/serviceproviders');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('ids');
            $status = $request->getPost('status', '1');
            if ($ids != null && $status != null) {
                $videos = Array();
                foreach ($ids as $media_id) {
                    $media = $this->getSPMTable()->getMedia($media_id);
                    if ($media->media_type == 2 && $status == 9 && strstr($media->media_url, 'uploads') != false) {
                        $videos[$media_id] = $media->media_url;
                    } else {
                        $this->getSPMTable()->changeStatus($media_id, $status);
                    }
                }

                if (count($videos) > 0) {
                    $session = new Container('vimeo');
                    $session->files = $videos;
                    $session->mode = 'write';
                    $session->returnUrl = $this->url()->fromRoute('admin/serviceprovidermedia', array('action' => 'changeStatus', 'user_id' => $user_id));

                    return $this->redirect()->toRoute('admin/media', array('action' => 'vimeologin'));
                }
                $this->flashMessenger()->addSuccessMessage('Status successfully changed..!!');
                return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
            } else {
                $this->flashMessenger()->addErrorMessage('Failed to change the status..!!');
                return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
            }
            exit;
        }
        return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
        exit;
    }

    public function avtarAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $user_id = (int) $this->params()->fromRoute('user_id', 0);

        $user = $this->getServiceLocator()->get('Admin\Model\UsersTable')->getUser($user_id);

        if ($user == false) {
            $this->flashMessenger()->addErrorMessage('Service Provider not found..!!');
            return $this->redirect()->toRoute('admin/serviceproviders');
        }

        if (!$id) {
            return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
        }

        $spmedia = $this->getSPMTable()->getMedia($id);

        $file = explode('/', $spmedia->media_url);
        $fileName = "./public/uploads/" . end($file);
        file_put_contents($fileName, fopen($spmedia->media_url, 'r'));

        $S3 = new ImageS3;
        $data = $S3->uploadFile($fileName, array('Avtars' => '378x378'));
        $old_image = $this->getSPMTable()->getUserAvtar($user_id);
        ($old_image != '') ? $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $old_image)) : '';
        $this->getSPMTable()->setUserAvtar($user_id, $data['Avtars']);

        $this->flashMessenger()->addSuccessMessage('Avtar image uploaded successfully..!!');
        return $this->redirect()->toRoute('admin/serviceprovidermedia', array('user_id' => $user_id));
    }

}
