<?php

namespace LibraryBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    public function getBooks($countBook, $cacheTTL)
    {
        $qb = $this->createQueryBuilder('book')
            ->orderBy('book.dateRead', 'DESC')
            ->getQuery()
            ->useResultCache(true, $cacheTTL, 'id');
        /*$result = $qb->getResult();
        dump($qb->execute());
        dump($qb->getResult());*/
        return $qb->getResult();
    }
}
