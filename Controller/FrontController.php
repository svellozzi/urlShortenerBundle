<?php

namespace Vellozzi\UrlShortenerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Vellozzi\UrlShortenerBundle\Model\UrlShortener;
use Vellozzi\UrlShortenerBundle\Exception\NotFoundException;

class FrontController extends Controller
{
    public function redirectAction($tag)
    {
        $manager = $this->get('vellozzi_urlshortener.urlshortener_manager');
        try {
            $urlShortener = $manager->loadFromTag($tag);
            if ($urlShortener instanceof urlShortener) {
                if ($urlShortener->hasExpired()) {
                    return $this->render("VellozziUrlShortenerBundle:Front:notFound.html.twig");
                } else {
                    $urlShortener->incrNbUsed();
                    $manager->save($urlShortener);

                    return $this->redirect($urlShortener->getUrl());
                }
            }
        } catch (NotFoundException $ex) {
            return $this->render("VellozziUrlShortenerBundle:Front:notFound.html.twig");
        }
    }

}
