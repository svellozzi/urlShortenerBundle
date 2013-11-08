<?php

namespace Vellozzi\UrlShortenerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Vellozzi\UrlShortenerBundle\Entity\UrlToTag as UrlToTagEntity;
use Vellozzi\UrlShortenerBundle\Model\UrlShortener;
use Vellozzi\UrlShortenerBundle\Manager\UrlToTagManager;
class FrontController extends Controller
{
     public function redirectAction($tag)
    {
      $manager = $this->get('vellozzi_urlshortener.urlshortener_manager');
      $urlShortener = $manager->loadFromTag($tag);
      if ($urlShortener instanceof urlShortener) {
        
        if ($urlShortener->hasExpired()) {
          return $this->render("VellozziUrlShortenerBundle:Front:notFound.html.twig");
        } else { 
          $urlShortener->incrNbUsed();
          $manager->save($urlShortener);
          return $this->redirect($urlShortener->getUrl());
        }
      } else {
        return $this->render("VellozziUrlShortenerBundle:Front:notFound.html.twig");
      }
    }

}
