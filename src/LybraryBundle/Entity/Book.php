<?php

namespace LybraryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="book")
 * @ORM\Entity(repositoryClass="LybraryBundle\Repository\BookRepository")
 */
class Book
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="cover", type="string", length=255, nullable=true, unique=true)
     */
    private $cover;

    /**
     * @var string
     *
     * @ORM\Column(name="book_file", type="string", length=255, unique=true)
     */
    private $bookFile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_read", type="datetime", nullable=true)
     */
    private $dateRead;

    /**
     * @var bool
     *
     * @ORM\Column(name="allow_download", type="boolean", nullable=true)
     */
    private $allowDownload;

    /**
     * @ORM\ManyToOne(targetEntity="LybraryBundle\Entity\User", inversedBy="book")
     */
    private $user;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Book
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Book
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set cover
     *
     * @param string $cover
     *
     * @return Book
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set bookFile
     *
     * @param string $bookFile
     *
     * @return Book
     */
    public function setBookFile($bookFile)
    {
        $this->bookFile = $bookFile;

        return $this;
    }

    /**
     * Get bookFile
     *
     * @return string
     */
    public function getBookFile()
    {
        return $this->bookFile;
    }

    /**
     * Set dateRead
     *
     * @param \DateTime $dateRead
     *
     * @return Book
     */
    public function setDateRead($dateRead)
    {
        $this->dateRead = $dateRead;

        return $this;
    }

    /**
     * Get dateRead
     *
     * @return \DateTime
     */
    public function getDateRead()
    {
        return $this->dateRead;
    }

    /**
     * Set allowDownload
     *
     * @param boolean $allowDownload
     *
     * @return Book
     */
    public function setAllowDownload($allowDownload)
    {
        $this->allowDownload = $allowDownload;

        return $this;
    }

    /**
     * Get allowDownload
     *
     * @return bool
     */
    public function getAllowDownload()
    {
        return $this->allowDownload;
    }

    /**
     * Set user
     *
     * @param \LybraryBundle\Entity\User $user
     *
     * @return Book
     */
    public function setUser(\LybraryBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \LybraryBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
