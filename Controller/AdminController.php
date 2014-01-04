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
        $urlShortener = new UrlShortener();
        $form = $this->createForm(new UrlShortenerType(), $urlShortener);
        $urlsShortened = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findAllShortenedUrls();
        $nb = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findNbShortenedUrls();
        return $this->render(
            "VellozziUrlShortenerBundle:Admin:listShortUrls.html.twig",
            array(
                'urlsShortened' => $urlsShortened,
                'urlWsAdd' => $this->generateUrl('admin_ws_add_short_url'),
                'urlWsDelete' => $this->generateUrl('admin_ws_delete_short_url'),
                'form' => $form->createView(),
            )
        );
    }
}