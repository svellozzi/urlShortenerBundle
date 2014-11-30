<?php
namespace Vellozzi\UrlShortenerBundle\Events;
use Symfony\Component\EventDispatcher\Event;
use Vellozzi\UrlShortenerBundle\Model\UrlShortener;

class AfterRedirectEvent extends Event {
    /**
     *
     * @var UrlShortener 
     */
    protected  $urlShortener;
    
    public function getUrlShortener() {
        return $this->urlShortener;
    }

    public function setUrlShortener(UrlShortener $urlShortener) {
        
        $this->urlShortener = $urlShortener;
    }            
}
