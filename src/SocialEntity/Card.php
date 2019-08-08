<?php

namespace App\SocialEntity;



class Card
{
    /** @var string */
    private $url;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var string */
    private $image;
    /** @var string */
    private $type;
    /** @var string */
    private $author_name;
    /** @var string */
    private $author_url;
    /** @var string */
    private $provider_name;
    /** @var string */
    private $provider_url;
    /** @var string */
    private $html;
    /** @var int */
    private $width;
    /** @var int */
    private $height;

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->author_name;
    }

    /**
     * @param string $author_name
     */
    public function setAuthorName(string $author_name): void
    {
        $this->author_name = $author_name;
    }

    /**
     * @return string
     */
    public function getAuthorUrl(): string
    {
        return $this->author_url;
    }

    /**
     * @param string $author_url
     */
    public function setAuthorUrl(string $author_url): void
    {
        $this->author_url = $author_url;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->provider_name;
    }

    /**
     * @param string $provider_name
     */
    public function setProviderName(string $provider_name): void
    {
        $this->provider_name = $provider_name;
    }

    /**
     * @return string
     */
    public function getProviderUrl(): string
    {
        return $this->provider_url;
    }

    /**
     * @param string $provider_url
     */
    public function setProviderUrl(string $provider_url): void
    {
        $this->provider_url = $provider_url;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }



}
