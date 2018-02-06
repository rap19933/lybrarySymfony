<?php

namespace LibraryBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use LibraryBundle\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;

class LibrarySubscriber implements EventSubscriber
{
    private $coverDirectory;
    private $bookDirectory;
    private $cacheBooksId;
    private $fs;
    private $cacheBooksService;

    public function __construct($coverDirectory, $bookDirectory, $cacheBooksId, $cacheBooksService)
    {
        $this->bookDirectory = $bookDirectory;
        $this->coverDirectory = $coverDirectory;
        $this->cacheBooksId = $cacheBooksId;
        $this->fs = new Filesystem();
        $this->cacheBooksService = $cacheBooksService;
    }

    public function getSubscribedEvents()
    {
        return array(
            'preRemove',
            'prePersist',
            'preUpdate'
        );
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Book) {
            if ($entity->getCover()) {
                $this->fs->remove($this->coverDirectory . $entity->getCover());
            }
            if ($entity->getBookFile()) {
                $this->fs->remove($this->bookDirectory . $entity->getBookFile());
            }
            $this->deleteCache();
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Book) {
            $directory = date("Y/m/d/");
            $file = $entity->getCover();
            if ($file) {
                $fileName = $directory . md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->coverDirectory . $directory, $fileName);
                $entity->setCover($fileName);
            }
            $file = $entity->getBookFile();
            if ($file) {
                $fileName = $directory . md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->bookDirectory . $directory, $fileName);
                $entity->setBookFile($fileName);
            }
            $this->deleteCache();
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $request = Request::createFromGlobals();
        $entity = $args->getEntity();

        if ($entity instanceof Book) {
            $directory = date("Y/m/d/");
            $changes = $args->getEntityChangeSet();
            if (!empty($changes["cover"])) {
                if (!empty($request->query->get("img"))) {
                    //удалить обложку
                    $this->fs->remove($this->coverDirectory.$changes["cover"][0]);
                    $entity->setCover(null);
                } elseif (!$changes["cover"][1]) {
                    //не обновлять обложку
                    $entity->setCover($changes["cover"][0]);
                } else {
                    //добавить обложку
                    if (!empty($changes["cover"][0])) {
                        //удалить обложку старую
                        $this->fs->remove($this->coverDirectory.$changes["cover"][0]);
                    }

                    $file = $entity->getCover();
                    $fileName = $directory.md5(uniqid()).'.'.$file->guessExtension();
                    $file->move($this->coverDirectory.$directory, $fileName);
                    $entity->setCover($fileName);
                }
            }

            if (!empty($changes["bookFile"])) {
                if (!empty($request->query->get("book"))) {
                    $this->fs->remove($this->bookDirectory.$changes["bookFile"][0]);
                    $entity->setBookFile(null);
                } elseif (!$changes["bookFile"][1]) {
                    $entity->setBookFile($changes["bookFile"][0]);
                } else {
                    if (!empty($changes["bookFile"][0])) {
                        $this->fs->remove($this->bookDirectory.$changes["bookFile"][0]);
                    }
                    $file = $entity->getBookFile();
                    $fileName = $directory.md5(uniqid()).'.'.$file->guessExtension();
                    $file->move($this->bookDirectory.$directory, $fileName);
                    $entity->setBookFile($fileName);
                }
            }
            $this->deleteCache();
        }
    }
    public function deleteCache()
    {
        $this->cacheBooksService->delete($this->cacheBooksId);
    }
}
