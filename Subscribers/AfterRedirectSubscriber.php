<?php

namespace Vellozzi\UrlShortenerBundle\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use Vellozzi\UrlShortenerBundle\Events\VellozziUrlShortenerBundleEvents,
    Vellozzi\UrlShortenerBundle\Events\AfterRedirectEvent;

class AfterRedirectSubscriber implements EventSubscriberInterface 
{
    protected $vellozziUrlshortenerManager;
    
    public function __construct()
    {
    }
    public function getVellozziUrlshortenerManager() {
        return $this->vellozziUrlshortenerManager;
    }

    public function setVellozziUrlshortenerManager($vellozziUrlshortenerManager) {
        $this->vellozziUrlshortenerManager = $vellozziUrlshortenerManager;
    }

        public static function getSubscribedEvents()
    {
        // Liste des évènements écoutés et méthodes à appeler
        return array(
            VellozziUrlShortenerBundleEvents::AFTER_REDIRECT_EVENT => 'incrNbUsed',
            KernelEvents::TERMINATE => 'OnKernelTerminate'
        );
    }

    public function incrNbUsed(AfterRedirectEvent $event)
    {
        $urlShortener = $event->getUrlShortener();
        $urlShortener->incrNbUsed();
        $manager = $this->getVellozziUrlshortenerManager();
        $manager->save($urlShortener);
    }
    public function OnKernelTerminate(PostResponseEvent $event) {
        //file_put_contents('./sebastien.txt', print_r($event,1)); 
    }
}
