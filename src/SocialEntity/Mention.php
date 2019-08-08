<?php

namespace App\SocialEntity;

class Mention
{
    /** @var string */
    private $url;
    /** @var string */
    private $username;
    /** @var string */
    private $acct;
    /** @var string */
    private $id;


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getAcct(): string
    {
        return $this->acct;
    }

    /**
     * @param string $acct
     */
    public function setAcct(string $acct): void
    {
        $this->acct = $acct;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }


}
