<?php

namespace Vellozzi\UrlShortenerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManager;
use Vellozzi\UrlShortenerBundle\Entity\UrlToTag as UrlToTagEntity;
use Vellozzi\UrlShortenerBundle\Models\TagGenerator;
use Vellozzi\UrlShortenerBundle\Models\UrlShortener;
use Vellozzi\UrlShortenerBundle\Form\Type\UrlShortenerType;


class AdminController extends Controller
{

    public function addAction()
    {
      $urlShortener  = $this->get('vellozzi_urlshortener.default');
      //$urlShortener = new UrlShortener($this->getDoctrine()->getEntityManager());
      $form = $this->createForm(new UrlShortenerType(), $urlShortener);  
      $tmp = $this->getRequest()->get('form');
      if ($this->getRequest()->isMethod('POST')) {
        $form->bind($this->getRequest());
        if ($form->isValid()) {
          $urlShortener->save();
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
      $urlsShortened = $this->getListShortUrls();
      return $this->render(
        "VellozziUrlShortenerBundle:Admin:listShortUrls.html.twig",
        array(
          'urlsShortened' => $urlsShortened,
      ));
       
    }
    
    public function getListShortUrls() {
      $em = $this->getDoctrine()->getEntityManager();
      
      $qb = $em->createQueryBuilder();
    
      $qb->select('urlsShortened')
         ->from('VellozziUrlShortenerBundle:UrlToTag', 'urlsShortened');
 
      $query = $qb->getQuery();
      $urlsShortened = $query->getResult();
      if (is_array($urlsShortened) == true
          && count($urlsShortened) > 0)
      {
        return $urlsShortened;
      } else {
        return false;
      }
      
    }
}
