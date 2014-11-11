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

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $nb = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findNbShortenedUrls();

        $nbPages = (int) ceil($nb/$this->getNbItemPerPage());
        $page = $this->retrieveCurrentPage($nbPages);
        $urlsShortened = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findAllShortenedUrls($page);
  
        $query =  $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->getQuerySearch("");
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $this->getNbItemPerPage()
        );
        
        return $this->render(
            "VellozziUrlShortenerBundle:Admin:listShortUrls.html.twig",
            array(
                'pagination' => $pagination
            )
        );
    }
    
    public function searchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $nb = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findNbShortenedUrls();
        $nbPages = (int) ceil($nb/$this->getNbItemPerPage());
        $page = $this->retrieveCurrentPage($nbPages);
        $search = $this->getRequest()->get('search');
        $query =  $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->getQuerySearch($search);
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $this->getNbItemPerPage()
        );

        
        
        return $this->render(
            "VellozziUrlShortenerBundle:Admin:listShortUrls.html.twig",
            array(
                'urlWsAdd' => $this->generateUrl('admin_ws_add_short_url'),
                'urlWsDelete' => $this->generateUrl('admin_ws_delete_short_url'),
                'pagination' => $pagination
            )
        );
    }
    
    
    protected function retrieveCurrentPage($nbPages)
    {
        $page = (int) $this->getRequest()->get('page');
        if ($page <= 0  || $page >= PHP_INT_MAX || $page > $nbPages) {
            $page = 1;
        }
        
        return $page;
    }
    protected function getNbItemPerPage()
    {
       $tmp = $this->container->getParameter('vellozzi_url_shortener');
       $this->get('logger')->debug($tmp['admin']['item_per_page']);
       return $tmp['admin']['item_per_page']; 
    }
}