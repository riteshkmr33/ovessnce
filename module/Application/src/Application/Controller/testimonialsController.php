<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Api;
use Application\Model\Common;

class TestimonialsController extends AbstractActionController
{

    public function indexAction()
    {
        $api_url = $this->getServiceLocator()->get('Config')['api_url']['value'];
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            //echo $this->getRequest()->getPost('page'); exit;
            $next = ($this->getRequest()->getPost('page') != '') ? ($this->getRequest()->getPost('page')) : '1';
            $api = new Api();
            
            $url = $api_url . "/api/testimonials/";
            $res = $api->curl($url, array('page' => $next, 'status_id' => '1'), "GET");
            if ($res->getStatusCode() == 200) {
                $content = json_decode($res->getBody(), true);

                if (count($content['results'])) {
                    $count = 0;
                    foreach ($content['results'] as $value) {
                        $currentDate = strtotime(date("Y-m-d H:i:s"));
                        $lastDate = strtotime($value['created_on']);

                        $dateTime = (($lastDate + 86400) > $currentDate) ? ('About ' . (floor(($currentDate - $lastDate) / 3600)) . ':' . floor(($currentDate - $lastDate) / (24 * 60)) . ' Hours Ago') : $value['created_on'];
                        $ts_list[$count]['id'] = $value['id'];
                        $ts_list[$count]['created_on'] = $dateTime;
                        $ts_list[$count]['text'] = $value['text'];

                        if (count($value['created_by_user']) > 0) {
                            $details = json_decode($value['created_by_user'], true);
                            foreach ($details as $user_detail) {
                                $ts_list[$count]['user_name'] = $details['first_name'] . ' ' . $details['last_name'];
                                $ts_list[$count]['user_id'] = $details['user_id'];
                                $ts_list[$count]['img_url'] = ($details['avtar_url'] != 'None') ? $details['avtar_url'] : 0;
                            }
                        }
                        $count++;
                    }

                    if ($content['next'] != '') {
                        $str = str_replace("page", "@!##", $content['next']);
                        $str_arr = explode('@!##=', $str);
                        $next = explode('&', $str_arr[1]);
                        $ts_list['next'] = $next[0];
                    } else {
                        $ts_list['next'] = '';
                    }

                    //$ts_list['error'] = false;
                    $ts_list['count'] = $count;
                    echo json_encode($ts_list);
                    exit;
                } else {
                    $ts_list = '';
                    echo json_encode($ts_list); // no data found
                    exit;
                }
            } else {
                $ts_list = array(
                    'next' => '',
                    'count' => '',
                );
            }

            echo json_encode($ts_list);
            exit;
        } else {
            // getting banners
            $common = new Common;
            $banners = $common->getBanner($api_url, 8);
            
            return new ViewModel(array('banners' => $banners));
        }
    }

}
