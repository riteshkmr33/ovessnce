<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Media;
use Admin\Form\MediaForm;
use Admin\Form\MediaFilterForm;
use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use \Zend\Validator\File\IsImage;
use Zend\Session\Container;
use Zend\Http\Request;
use Zend\ImageS3;

class MediaController extends AbstractActionController
{

    private $getMediaTable;

    private function getMediaTable()
    {
        if (!$this->getMediaTable) {
            $this->getMediaTable = $this->getServiceLocator()->get('Admin\Model\MediaTable');
        }

        return $this->getMediaTable;
    }

    public function indexAction()
    {
        $form = new MediaFilterForm($this->getServiceLocator()->get('Admin\Model\StatusTable'));
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
            $paginator = $this->getMediaTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
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

            $paginator = $this->getMediaTable()->fetchAll(true, $filter, array('sort_field' => $getData['sort_field'], 'sort_order' => $getData['sort_order']));
        } else {
            $paginator = $this->getMediaTable()->fetchAll();
        }

        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'media' => $paginator,
            'form' => $form,
            'status' => $this->getServiceLocator()->get('Admin\Model\StatusTable')->fetchAll(false, array(9, 5, 10)),
            'postedData' => array_filter((array) $postedData),
            'successMsgs' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'errors' => $this->flashMessenger()->getCurrentErrorMessages()
        ));
    }

    public function addAction()
    {
        $form = new MediaForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('media_url');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('media_url' => $File['name'])
            );

            $media = new Media();
            $form->setInputFilter($media->getInputFilter());
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

                            return array('form' => $form, 'file_errors' => $dataError);
                        } else {

                            $S3 = new ImageS3;
                            $data = $S3->uploadFiles($_FILES['media_url'], "Media", array(), array('Media' => 100, 'Media_thumb' => 20));

                            if (is_array($data) && count($data) > 0) {
                                $formData['media_url'] = $data['Media'];
                            }
                        }
                        /* Image uploading code ends */
                    } else {
                        return array('form' => $form, 'file_errors' => $validator->getMessages());
                    }
                } else if ($post['media_type'] == '2') {

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
                        $session->returnUrl = $this->url()->fromRoute('admin/media');

                        $media->exchangeArray($formData);
                        $session->media_id = $this->getMediaTable()->saveMedia($media);
                        if ($formData['status_id'] == 9) {
                            return $this->redirect()->toRoute('admin/media', array('action' => 'vimeologin'));
                        }
                    } else {
                        return array('form' => $form, 'file_errors' => $adaptor->getMessages());
                    }
                }

                $media->exchangeArray($formData);
                $this->getMediaTable()->saveMedia($media);
                $this->flashMessenger()->addSuccessMessage('Media added successfully..!!');

                // Redirect to list of certifications
                return $this->redirect()->toRoute('admin/media');
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
            return $this->redirect()->toRoute('admin/media');
        }

        $media = $this->getMediaTable()->getMedia($id);

        if ($media == false) {
            $this->flashMessenger()->addErrorMessage('Media not found..!!');
            return $this->redirect()->toRoute('admin/media');
        }

        $form = new MediaForm($this->getServiceLocator()->get('Admin\Model\UsersTable'), $this->getServiceLocator()->get('Admin\Model\StatusTable'));
        $old_media = $media->media_url;
        $form->bind($media);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $File = $this->params()->fromFiles('media_url');

            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), array('media_url' => $File['name'])
            );

            $form->setInputFilter($media->getInputFilter());
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
                            $session->returnUrl = $this->url()->fromRoute('admin/media');
                            $session->old_video = $old_media;

                            $session->media_id = $this->getMediaTable()->saveMedia($form->getData());
                            if ($formData->status_id == 9) {
                                return $this->redirect()->toRoute('admin/media', array('action' => 'vimeologin'));
                            }
                        } else {
                            return array('form' => $form, 'file_errors' => $adaptor->getMessages());
                        }
                    } else {
                        $formData->media_url = $old_media;
                    }
                }

                $this->getMediaTable()->saveMedia($form->getData());
                $this->flashMessenger()->addSuccessMessage('Media updated successfully..!!');

                // Redirect to list of banners
                return $this->redirect()->toRoute('admin/media');
            }
        }

        return array(
            'id' => $id,
            'media' => $media,
            'errors' => $this->errors,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('admin/media');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');

                $media = $this->getMediaTable()->getMedia($id);
                if (stristr($media->media_url, 'https://ovessence.s3.amazonaws.com') != false) {
                    /* Deleting Images from amazon - starts here */
                    $S3 = new ImageS3;
                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', $media->media_url));
                    $S3->deleteFile(str_replace('https://ovessence.s3.amazonaws.com/', '', str_replace('Media', 'Media_thumb', $media->media_url)));
                    /* Deleting Images from amazon - ends here */
                } else {

                    $session = new Container('vimeo');
                    $session->files = array($media->media_url);
                    $session->mode = 'delete';
                    $session->returnUrl = $this->url()->fromRoute('admin/media');
                    $this->getMediaTable()->deleteMedia($id); // Delete record from database
                    if ($media->status_id == 9) {
                        return $this->redirect()->toRoute('admin/media', array('action' => 'vimeologin'));
                    } else {
                        @unlink($media->media_url);
                    }
                }

                $this->getMediaTable()->deleteMedia($id); // Delete recode from database
                $this->flashMessenger()->addSuccessMessage('Media deleted successfully..!!');
            }
            // Redirect to list of banners
            return $this->redirect()->toRoute('admin/media');
        }

        return array(
            'id' => $id,
            'media' => $this->getMediaTable()->getMedia($id)
        );
    }

    public function changeStatusAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('ids');
            $status = $request->getPost('status', '1');
            if ($ids != null && $status != null) {
                $videos = Array();
                foreach ($ids as $media_id) {
                    $media = $this->getMediaTable()->getMedia($media_id);
                    if ($media->media_type == 2 && $status == 9 && strstr($media->media_url, 'uploads') != false) {
                        $videos[$media_id] = $media->media_url;
                    } else {
                        $this->getMediaTable()->changeStatus($media_id, $status);
                    }
                }

                if (count($videos) > 0) {
                    $session = new Container('vimeo');
                    $session->files = $videos;
                    $session->mode = 'write';
                    $session->returnUrl = $this->url()->fromRoute('admin/media', array('action' => 'changeStatus'));

                    return $this->redirect()->toRoute('admin/media', array('action' => 'vimeologin'));
                }
                $this->flashMessenger()->addSuccessMessage('Status successfully changed..!!');
                return $this->redirect()->toRoute('admin/media');
            } else {
                $this->flashMessenger()->addErrorMessage('Failed to change the status..!!');
                return $this->redirect()->toRoute('admin/media');
            }
            exit;
        }
        return $this->redirect()->toRoute('admin/media');
        exit;
    }

    public function vimeologinAction()
    {
        $session = new Container('vimeo');  // Getting session values

        if ($_REQUEST['clear'] == 'all') {
            $session->offsetUnset('file');
            $session->offsetUnset('mode');
            $session->offsetUnset('media_id');
            $session->offsetUnset('returnUrl');
            $session->offsetUnset('old_video');
            $session->offsetUnset('vimeo_state');
            $session->offsetUnset('oauth_access_token');
            $session->offsetUnset('oauth_access_token_secret');
            echo 'cleared';
            exit;
        }

        if (isset($session->returnUrl) && $session->returnUrl != "") {

            $config = $this->getServiceLocator()->get('Config');
            $vimeo = new \phpVimeo($config['Vimeo']['clientId'], $config['Vimeo']['clientSecrate']);
            //$vimeo->enableCache(\phpVimeo::CACHE_FILE, 'cache', 300);

            if (!isset($session->oauth_access_token) || !isset($session->oauth_access_token_secret)) {
                if (isset($session->vimeo_state) && $session->vimeo_state == 'start') {
                    $session->vimeo_state = 'returned';
                } else {
                    $session->vimeo_state = 'start';
                }

                if ($session->vimeo_state == 'start') {
                    // Get a new request token
                    $token = $vimeo->getRequestToken();

                    // Store it in the session
                    $session->oauth_request_token = $token['oauth_token'];
                    $session->oauth_request_token_secret = $token['oauth_token_secret'];
                    $session->vimeo_state = 'start';

                    $mode = (isset($session->old_video) && $session->old_video != "") ? 'delete' : $session->mode;  // setting request mode

                    header('Location: ' . $vimeo->getAuthorizeUrl($token['oauth_token'], $mode));
                    exit;
                } else if ($session->vimeo_state == 'returned') {
                    $vimeo->setToken($session->oauth_request_token, $session->oauth_request_token_secret);
                    $token = $vimeo->getAccessToken($_REQUEST['oauth_verifier']);

                    // storing oath tokens
                    $session->oauth_access_token = $token['oauth_token'];
                    $session->oauth_access_token_secret = $token['oauth_token_secret'];
                    $vimeo->setToken($session->oauth_access_token, $session->oauth_access_token_secret);
                }
            }

            if (isset($session->mode) && $session->mode == 'write') {
                $vimeo = new \phpVimeo($config['Vimeo']['clientId'], $config['Vimeo']['clientSecrate'], $session->oauth_access_token, $session->oauth_access_token_secret);
                if (isset($session->file) && file_exists($session->file)) {

                    $video_id = $vimeo->upload($session->file);  // uploading file on vimeo

                    if ($video_id != false) {
                        @unlink($session->file); // Deleting file from our server

                        if (isset($session->old_video) && $session->old_video != '' && $info = $vimeo->call('vimeo.videos.getInfo', array('video_id' => $session->old_video))) {
                            $vimeo->call('vimeo.videos.delete', array('video_id' => $session->old_video));  // deleting previous video
                        }

                        /* Updating database records */
                        $this->getMediaTable()->updateMedia('media_url', $video_id, $session->media_id);
                        (isset($session->old_video) && $session->old_video != "") ? $this->flashMessenger()->addSuccessMessage('Media updated successfully..!!') : $this->flashMessenger()->addSuccessMessage('Media added successfully..!!');

                        /* Clearing session variables */
                        $session->offsetUnset('msg');
                        $session->offsetUnset('mode');
                        $session->offsetUnset('file');
                        $session->offsetUnset('old_video');
                        $session->offsetUnset('oauth_access_token');
                        $session->offsetUnset('oauth_access_token_secret');
                    } else {
                        (isset($session->msg) && $session->msg != "") ? $this->flashMessenger()->addErrorMessage($session->msg) : $this->flashMessenger()->addErrorMessage('Video not uploaded..!!');
                    }
                } else if (isset($session->files) && count($session->files) > 0) {

                    foreach ($session->files as $media_id => $file) {
                        if (file_exists($file)) {
                            $video_id = $vimeo->upload($file);  // uploading file on vimeo
                            if ($video_id != false) {
                                @unlink($file); // Deleting file from our server

                                /* Updating database records */
                                $this->getMediaTable()->updateMedia('media_url', $video_id, $media_id);
                                $this->getMediaTable()->changeStatus($media_id, '9');

                                $this->flashMessenger()->addSuccessMessage('Status changed successfully..!!');
                            } else {
                                (isset($session->msg) && $session->msg != "") ? $this->flashMessenger()->addErrorMessage($session->msg) : $this->flashMessenger()->addErrorMessage('Video not uploaded..!!');
                            }
                        } else {
                            $this->flashMessenger()->addErrorMessage('File not found to upload..!!');
                        }
                    }
                } else {
                    $this->flashMessenger()->addErrorMessage('File not found to upload..!!');
                }
                /* Clearing session variables */
                $session->offsetUnset('msg');
                $session->offsetUnset('mode');
                $session->offsetUnset('files');
                $session->offsetUnset('old_video');
                $session->offsetUnset('oauth_access_token');
                $session->offsetUnset('oauth_access_token_secret');

                // Redirect to list of media
                return $this->redirect()->toUrl($session->returnUrl);
            } else if (isset($session->mode) && $session->mode == 'delete') {
                $vimeo = new \phpVimeo($config['Vimeo']['clientId'], $config['Vimeo']['clientSecrate'], $session->oauth_access_token, $session->oauth_access_token_secret);

                foreach ($session->files as $file) {
                    if ($info = $vimeo->call('vimeo.videos.getInfo', array('video_id' => $file))) {
                        $vimeo->call('vimeo.videos.delete', array('video_id' => $file));
                        $session->offsetUnset('msg');
                        $this->flashMessenger()->addSuccessMessage('Media deleted successfully..!!');
                    } else {
                        (isset($session->msg) && $session->msg != "") ? $this->flashMessenger()->addErrorMessage($session->msg) : $this->flashMessenger()->addErrorMessage('Video not found..!!');
                    }
                }
                /* Clearing session variables */
                $session->offsetUnset('mode');
                $session->offsetUnset('old_video');
                $session->offsetUnset('files');
                $session->offsetUnset('oauth_access_token');
                $session->offsetUnset('oauth_access_token_secret');

                // Redirect to list of media
                return $this->redirect()->toUrl($session->returnUrl);
            }
        } else {
            // Redirect to list of media
            return $this->redirect()->toRoute('admin/media');
        }

        exit;
    }

}
