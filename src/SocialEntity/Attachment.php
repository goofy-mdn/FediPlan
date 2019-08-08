<?php

namespace App\SocialEntity;


class Attachment
{
    /** @var string */
    private $id;
    /** @var string */
    private $type;
    /** @var string */
    private $url;
    /** @var string */
    private $remote_url;
    /** @var string */
    private $preview_url;
    /** @var string */
    private $text_url;
   /** @var string */
    private $meta;
    /** @var string */
    private $description;

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
    public function getRemoteUrl(): string
    {
        return $this->remote_url;
    }

    /**
     * @param string $remote_url
     */
    public function setRemoteUrl(string $remote_url): void
    {
        $this->remote_url = $remote_url;
    }

    /**
     * @return string
     */
    public function getPreviewUrl(): string
    {
        return $this->preview_url;
    }

    /**
     * @param string $preview_url
     */
    public function setPreviewUrl(string $preview_url): void
    {
        $this->preview_url = $preview_url;
    }

    /**
     * @return string
     */
    public function getTextUrl(): string
    {
        return $this->text_url;
    }

    /**
     * @param string $text_url
     */
    public function setTextUrl(string $text_url): void
    {
        $this->text_url = $text_url;
    }

    /**
     * @return string
     */
    public function getMeta(): string
    {
        return $this->meta;
    }

    /**
     * @param string $meta
     */
    public function setMeta(string $meta): void
    {
        $this->meta = $meta;
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


}
