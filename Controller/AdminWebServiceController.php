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
        $this->initTagGenerator();

        if ($this->isValidRequestForAdd() === true) {
            $urlShortener = new UrlShortener();
            $url = $this->getRequest()->get('url');
            $tag = $this->getRequest()->get('tag');
            $maxAllowedUse = (int) $this->getRequest()->get('maxAllowedUse');
            $expireAt = $this->getRequest()->get('expireAt');
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
            $manager = $this->get('vellozzi_urlshortener.manager');
            try {
                $manager->save($urlShortener);
                $data = array(
                    'status' => 'OK',
                    'message' => 'url has been save'
                );
                $response = $this->createResponseOk($data);
            } catch (Exception $e) {
               $data = array(
                  'status' => 'ko',
               );
               $response = $this->createResponseInternalServerError($data);
            }
        } else {
            $data = array(
                'status' => 'ko',
                'message' => implode(',',$this->messagesToUser)
            );
            $response = $this->createResponseBadRequest($data);
        }

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
        $manager = $this->get('vellozzi_urlshortener.manager');
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
            $manager = $this->get('vellozzi_urlshortener.manager');
            foreach($items as $id) {
                $id = (int) $id;
                if ($id > 0) {
                    $manager->removeFromId($id);
                }
            }
            $data = array(
                'status' => 'ok',
             );
            $response = $this->createResponseOk($data);
        } else {
            $data = array(
                'status' => 'ko',
                'message' => 'no id founded'
            );
            $response = $this->createResponseBadRequest($data); 
        }

        return $response;
    }
    public function getTagAction()
    {
        $this->initTagGenerator();
        $tag = $this->get('vellozzi_urlshortener.manager')->generateTag();
        $data = array(
            'status' => 'ko',
            'message' => 'unable to generate tag'
        );
     
        if (false === $tag) {
           $response = $this->createResponseBadRequest($data); 
        } else {
           $data = array(
                'status' => 'ok',
                'tag' => $tag
            );
           $response = $this->createResponseOk($data); 
        }
        
        return $response;
    }
    public function isValidTagAction()
    {
        $tag = $this->getRequest()->get('tag');
        $manager = $this->get('vellozzi_urlshortener.manager');
        if ($manager->isValidTag($tag) === true) {
            $data = array(
                'status' => 'ok',
            );
            $response = $this->createResponseOk($data);
        } else {
            $data = array(
                'status' => 'ko',
            ); 
            $response = $this->createResponseOk($data);
        }

        return $response;
    }
    
    protected function createResponseOk($data)
    {
        $response = new JsonResponse();
        $response->setData($data);
        $response->setStatusCode(200);
        
        return $response;
    }
    
    protected function createResponseBadRequest($data)
    {
        $response = new JsonResponse();
        $response->setData($data);
        $response->setStatusCode(400);
        
        return $response;
    }
    
    protected function createResponseInternalServerError($data)
    {
        $response = new JsonResponse();
        $response->setData($data);
        $response->setStatusCode(500);
        
        return $response;
    }


    protected function initTagGenerator()
    {
       $tmp = $this->container->getParameter('vellozzi_url_shortener');
       $this->get('logger')->debug($tmp['allowedChar']);
       $this->get('logger')->debug($tmp['tag_size']);
       $this->get('vellozzi_urlshortener.taggenerator')->setAllowedCharForTag($tmp['allowedChar']);
       $this->get('vellozzi_urlshortener.taggenerator')->setSize($tmp['tag_size']);
    }
}
