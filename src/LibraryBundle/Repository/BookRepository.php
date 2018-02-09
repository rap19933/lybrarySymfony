<?php

namespace LibraryBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    public function getBooks($page, $limit, $cacheTTL, $knpPaginator)
    {
        $query = $this->createQueryBuilder('book')
            ->orderBy('book.dateRead', 'DESC')
            ->getQuery()
            ->useResultCache(true, $cacheTTL, "cache_id_{$page}_{$limit}");

        $pagination = $knpPaginator->paginate($query, $page, $limit);

        return $pagination;
    }
}
