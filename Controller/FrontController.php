<?php

namespace Vellozzi\UrlShortenerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Vellozzi\UrlShortenerBundle\Model\UrlShortener;
use Vellozzi\UrlShortenerBundle\Exception\NotFoundException;
use Vellozzi\UrlShortenerBundle\Events\VellozziUrlShortenerBundleEvents,
    Vellozzi\UrlShortenerBundle\Events\AfterRedirectEvent;

class FrontController extends Controller
{
    public function redirectAction($tag)
    {
        $manager = $this->get('vellozzi_urlshortener.manager');
        try {
            $urlShortener = $manager->loadFromTag($tag);
            if ($urlShortener instanceof urlShortener) {
                if ($urlShortener->hasExpired()) {
                    return $this->render("VellozziUrlShortenerBundle:Front:notFound.html.twig");
                } else {
//                    $urlShortener->incrNbUsed();
//                    $manager->save($urlShortener);
                    $event = new AfterRedirectEvent();
                    $event->setUrlShortener($urlShortener);
                    $this->get("event_dispatcher")->dispatch(
                        VellozziUrlShortenerBundleEvents::AFTER_REDIRECT_EVENT, $event
                    );
                    
                    return $this->redirect($urlShortener->getUrl());
                }
            }
        } catch (NotFoundException $ex) {
            return $this->render("VellozziUrlShortenerBundle:Front:notFound.html.twig");
        }
    }

}
