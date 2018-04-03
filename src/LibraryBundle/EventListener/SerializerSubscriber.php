<?php
namespace LibraryBundle\EventListener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use LibraryBundle\Entity\Book;

class SerializerSubscriber implements EventSubscriberInterface
{
    private $coverDirectory;
    private $bookDirectory;
    private $siteManager;

    public function __construct($coverDirectory, $bookDirectory, SiteManager $siteManager)
    {
        $this->bookDirectory = $bookDirectory;
        $this->coverDirectory = $coverDirectory;
        $this->siteManager = $siteManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => 'serializer.pre_serialize',
                'method' => 'onPreSerialize',
                'class'  => Book::class,
                'format' => 'json',
            ),
        );
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        $book = $event->getObject();

        if ($book->getCover()) {
            $book->setCover($this->siteManager->getCurrentSite() . $this->coverDirectory . $book->getCover());
        }
        if ($book->getBookFile()) {
            $book->setBookFile($this->siteManager->getCurrentSite() . $this->bookDirectory . $book->getBookFile());
        }
    }
}
