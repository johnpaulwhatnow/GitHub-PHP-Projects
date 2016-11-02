<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Repo
 */
class Repo
{
    /**
     * @var int
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var integer
     */
    private $github_id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $last_push_date;

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $stars;


    /**
     * Set github_id
     *
     * @param integer $githubId
     * @return Repo
     */
    public function setGithubId($githubId)
    {
        $this->github_id = $githubId;

        return $this;
    }

    /**
     * Get github_id
     *
     * @return integer 
     */
    public function getGithubId()
    {
        return $this->github_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Repo
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
     * Set url
     *
     * @param string $url
     * @return Repo
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Repo
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set last_push_date
     *
     * @param \DateTime $lastPushDate
     * @return Repo
     */
    public function setLastPushDate($lastPushDate)
    {
        $this->last_push_date = $lastPushDate;

        return $this;
    }

    /**
     * Get last_push_date
     *
     * @return \DateTime 
     */
    public function getLastPushDate()
    {
        return $this->last_push_date;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Repo
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set stars
     *
     * @param integer $stars
     * @return Repo
     */
    public function setStars($stars)
    {
        $this->stars = $stars;

        return $this;
    }

    /**
     * Get stars
     *
     * @return integer 
     */
    public function getStars()
    {
        return $this->stars;
    }
    public function hydrateFromDataRow($repo_row){
        //configure dates
        $created_date = \DateTime::createFromFormat(\DateTime::ISO8601, $repo_row['created_at']);
        $last_push_date = \DateTime::createFromFormat(\DateTime::ISO8601, $repo_row['pushed_at']);
        $this->setGithubId($repo_row['id']);
        $this->setName($repo_row['name']);
        $this->setUrl($repo_row['html_url']);
        $this->setCreatedAt($created_date);
        $this->setLastPushDate($last_push_date);
        $this->setDescription($repo_row['description']);
        $this->setStars($repo_row['stargazers_count']);

        return $this;
    }
}
