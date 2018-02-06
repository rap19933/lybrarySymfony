<?php
namespace LibraryBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    public function getBooks()
    {
        $qb = $this->createQueryBuilder('book')
            ->orderBy('book.dateRead', 'DESC')
            ->getQuery();
        return $qb->execute();
    }
}
