<?php

namespace Vellozzi\UrlShortenerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Vellozzi\UrlShortenerBundle\Entity\UrlToTag as UrlToTagEntity;
use Vellozzi\UrlShortenerBundle\Models\TagGenerator;
use Vellozzi\UrlShortenerBundle\Models\UrlShortener;

class FrontController extends Controller
{
     public function redirectAction($tag)
    {
      $urlShortener = new UrlShortener($this->getDoctrine()->getEntityManager());
      $urlShortener->setShortTag($tag);
      if ($urlShortener->hydrate()) {
        if ($urlShortener->hasExpired()) {
          return $this->render("VellozziUrlShortenerBundle:Front:notFound.html.twig");
        } else { 
          $urlShortener->incrNbUsed();
          $urlShortener->save();
          return $this->redirect($urlShortener->getUrl());
        }
      } else {
        return $this->render("VellozziUrlShortenerBundle:Front:notFound.html.twig");
      }
    }

}
