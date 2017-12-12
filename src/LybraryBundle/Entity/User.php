<?php

namespace LybraryBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="LybraryBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="LybraryBundle\Entity\Book", mappedBy="user")
     */
    private $book;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add book
     *
     * @param \LybraryBundle\Entity\Book $book
     *
     * @return User
     */
    public function addBook(\LybraryBundle\Entity\Book $book)
    {
        $this->book[] = $book;

        return $this;
    }

    /**
     * Remove book
     *
     * @param \LybraryBundle\Entity\Book $book
     */
    public function removeBook(\LybraryBundle\Entity\Book $book)
    {
        $this->book->removeElement($book);
    }

    /**
     * Get book
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBook()
    {
        return $this->book;
    }
}
