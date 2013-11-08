<?php

namespace Vellozzi\UrlShortenerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManager;
use Vellozzi\UrlShortenerBundle\Entity\UrlToTag as UrlToTagEntity;
use Vellozzi\UrlShortenerBundle\Model\TagGenerator;
use Vellozzi\UrlShortenerBundle\Model\UrlShortener;
use Vellozzi\UrlShortenerBundle\Form\Type\UrlShortenerType;


class AdminController extends Controller
{

    public function addAction()
    {
      
      $urlShortener = new UrlShortener();
      $form = $this->createForm(new UrlShortenerType(), $urlShortener);  
      $tmp = $this->getRequest()->get('form');
      if ($this->getRequest()->isMethod('POST')) {
        $form->bind($this->getRequest());
        if ($form->isValid()) {
          $manager = $this->get('vellozzi_urlshortener.urlshortener_manager');
          if ($manager->isValidTag($urlShortener->getShortTag()) === true) {
            $manager->save($urlShortener);
          }
          
        }
      }
      return $this->render(
        "VellozziUrlShortenerBundle:Admin:addShortUrls.html.twig",
        array(
          'form' => $form->createView(),
        )
      );
    }
    public function listAction() {
      $em = $this->getDoctrine()->getEntityManager();
      
      $urlsShortened = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findAllShortenedUrls();
      $nb = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findNbShortenedUrls();
      
      return $this->render(
        "VellozziUrlShortenerBundle:Admin:listShortUrls.html.twig",
        array(
          'urlsShortened' => $urlsShortened,
      ));
    }
}