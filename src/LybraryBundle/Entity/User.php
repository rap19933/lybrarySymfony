<?php

namespace LybraryBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * User
 *
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="LybraryBundle\Repository\UserRepository")
 * @JMS\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $username;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $usernameCanonical;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $email;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $emailCanonical;

    /**
     * @var bool
     * @JMS\Type("bool")
     * @JMS\Expose
     */
    protected $enabled;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $salt;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $password;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     * @JMS\Type("DateTime")
     * @JMS\Expose
     */
    protected $lastLogin;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     * @JMS\Type("DateTime")
     * @JMS\Expose
     */
    protected $passwordRequestedAt;

    /**
     * @var Collection
     * @JMS\Type("Collection")
     * @JMS\Expose
     */
    protected $groups;

    /**
     * @var array
     * @JMS\Type("array")
     * @JMS\Expose
     */
    protected $roles;


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
