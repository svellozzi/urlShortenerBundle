<?php

namespace Vellozzi\UrlShortenerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Vellozzi\UrlShortenerBundle\Model\UrlShortener;

use Symfony\Component\HttpFoundation\JsonResponse;
class AdminWebServiceController extends Controller
{
    protected $messagesToUser;

    public function addAction()
    {
        $data = array(
            'status' => 'ok',
        );
        $statusCode = 200;
        if ($this->isValidRequestForAdd() === true) {
            $urlShortener = new UrlShortener();
            $url = $this->getRequest()->get('url');
            $tag = $this->getRequest()->get('tag');
            $maxAllowedUse = (int) $this->getRequest()->get('maxAllowedUse');
            $expireAt =$this->getRequest()->get('expireAt');
            if (!empty($expireAt)) {
                $expireAt = $expireAt.' 23:59:59';
                $expireAt = new \DateTime($expireAt);
                $urlShortener->setExpireAt($expireAt);           
            }
            $urlShortener->setUrl($url);
            $urlShortener->setShortTag($tag);
            if ($maxAllowedUse>0) {
                $urlShortener->setMaxAllowedUse($maxAllowedUse);
            }
            $manager = $this->get('vellozzi_urlshortener.urlshortener_manager');
            try {
              $manager->save($urlShortener);
              $data = array(
                  'status' => 'OK',
                  'message' => 'url has been save'
              );
            } catch (Exception $e) {
               $statusCode = 500;
               $data = array(
                  'status' => 'ko',
               );
            }
        } else {
            $data = array(
                'status' => 'ko',
                'message' => implode(',',$this->messagesToUser)
            );
            $statusCode = 400;
        }
        $response = new JsonResponse();
        $response->setData($data);
        $response->setStatusCode($statusCode);

        return $response;
     }

    protected function isValidRequestForAdd()
    {
        $url = $this->getRequest()->get('url');
        $tag = $this->getRequest()->get('tag');
        $maxAllowedUse = (int) $this->getRequest()->get('maxAllowedUse');
        $expireAt = $this->getRequest()->get('expireAt');
        $flag = true;
        if (empty($url)) {
            $this->messagesToUser[] = "missing web site";
            $flag = false;
        }
        if (empty($tag)) {
            $this->messagesToUser[] = "missing tag";
            $flag = false;
        }
        $manager = $this->get('vellozzi_urlshortener.urlshortener_manager');
        if ($manager->isValidTag($tag) === false) {
            $this->messagesToUser[] = "tag $tag is  already used";
            $flag = false;
        }

        return $flag;
    }
    public function deleteAction()
    {
        $items = explode(",",$this->getRequest()->get('items'));

        if (count($items) > 0) {
            $manager = $this->get('vellozzi_urlshortener.urlshortener_manager');
            foreach($items as $id) {
                $id = (int) $id;
                if ($id > 0) {
                    $manager->removeFromId($id);
                }
            }
            $data = array(
                'status' => 'ok',
             );
            $statusCode = 200;
        } else {
            $data = array(
                'status' => 'ko',
                'message' => 'no id founded'
            );
            $statusCode = 400;
        }
        $response = new JsonResponse();
        $response->setData($data);
        $response->setStatusCode($statusCode);

        return $response;
    }

}
