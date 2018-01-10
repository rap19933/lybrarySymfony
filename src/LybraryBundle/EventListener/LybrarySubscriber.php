<?php

namespace LybraryBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use LybraryBundle\Entity\Book;
use LybraryBundle\ClassForFiles;
use Symfony\Component\HttpFoundation\Request;

class LybrarySubscriber implements EventSubscriber
{
    private $coverDirectory;
    private $bookDirectory;

    public function __construct($coverDirectory, $bookDirectory)
    {
        $this->bookDirectory = $bookDirectory;
        $this->coverDirectory = $coverDirectory;
    }

    public function getSubscribedEvents()
    {
        return array(
            'index',
            'preRemove',
            'prePersist',
            'preUpdate',
        );
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Book) {

            ClassForFiles::RemoveFile(
                $this->coverDirectory.$entity->getCover(),
                $this->bookDirectory.$entity->getBookFile());
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Book) {

            $file = $entity->getCover();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->coverDirectory,
                    $fileName
                );
                $entity->setCover($fileName);
            }

            $file = $entity->getBookFile();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->bookDirectory,
                $fileName
            );
            $entity->setBookFile($fileName);
        }
    }
    public function preUpdate(LifecycleEventArgs $args)
    {
        $request = Request::createFromGlobals();
        $entity = $args->getEntity();

        if($entity instanceof Book) {

            $changes = $args->getEntityChangeSet();
            if (empty($changes["dateRead"][1])){
                if(!empty($changes["cover"])) {
                    //удалить обложку
                    if(!empty($request->query->get("img"))) {
                        ClassForFiles::RemoveFile($this->coverDirectory.$changes["cover"][0],false);
                        $entity->setCover(null);
                    }
                    // не обновлять обложку
                    elseif(!$changes["cover"][1]) {
                        $entity->setCover($changes["cover"][0]);
                    }
                    //добавить обложку
                    else {
                        // обновить обложку
                        if(!empty($changes["cover"][0])) {
                            ClassForFiles::RemoveFile($this->coverDirectory.$changes["cover"][0],false);
                        }

                        $file = $entity->getCover();
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();
                        $file->move(
                            $this->coverDirectory,
                            $fileName
                        );
                        $entity->setCover($fileName);
                    }
                }

                if (empty($changes["bookFile"][1])) {
                    $entity->setBookFile($changes["bookFile"][0]);
                } else {
                    ClassForFiles::RemoveFile(false,$this->bookDirectory.$changes["bookFile"][0]);
                    $file = $entity->getBookFile();
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();
                    $file->move(
                        $this->bookDirectory,
                        $fileName
                    );
                    $entity->setBookFile($fileName);
                }
            }
        }
    }
}
