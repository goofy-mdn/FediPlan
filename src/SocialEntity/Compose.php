<?php

namespace App\SocialEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


class Compose
{

    private $id;

    private $content_warning;

    private $content;

    private $visibility;

    private $created_at;

    private $scheduled_at;

    private $sent_at;

    private $sensitive;

    private $in_reply_to_id;

    private $timeZone;

    /**
     * @return mixed
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @param mixed $timeZone
     */
    public function setTimeZone($timeZone): void
    {
        $this->timeZone = $timeZone;
    }



    public function getTotalMedia(){
    }


    public function getSent(){
        return ($this->sent_at != null);
    }

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentWarning(): ?string
    {
        return $this->content_warning;
    }

    public function setContentWarning(?string $content_warning): self
    {
        $this->content_warning = $content_warning;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }



    /**
     * @return boolean
     */
    public function getSensitive()
    {
        return $this->sensitive;
    }

    /**
     * @param mixed $sensitive
     */
    public function setSensitive(bool $sensitive): void
    {
        $this->sensitive = $sensitive;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getScheduledAt(): ?\DateTime
    {
        return $this->scheduled_at;
    }

    public function setScheduledAt(?\DateTime $scheduled_at): self
    {
        $this->scheduled_at = $scheduled_at;

        return $this;
    }

    public function getInReplyToId(): ?string
    {
        return $this->in_reply_to_id;
    }

    public function setInReplyToId(?string $in_reply_to_id): self
    {
        $this->in_reply_to_id = $in_reply_to_id;

        return $this;
    }



}
