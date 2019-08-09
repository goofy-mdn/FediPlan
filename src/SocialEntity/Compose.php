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

    private $attachments;

    private $created_at;

    private $scheduled_at;

    private $scheduled;

    private $sent_at;

    private $sensitive;

    private $social_account;

    private $in_reply_to_id;

    private $isSensitive;




    public function getTotalMedia(){
        return count($this->getAttachments());
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

    /**
     * @return Collection|Media[]
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Media $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
        }

        return $this;
    }

    public function removeAttachment(Media $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
        }

        return $this;
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

    public function getScheduledAt(): ?\DateTimeInterface
    {
        return $this->scheduled_at;
    }

    public function setScheduledAt(?\DateTimeInterface $scheduled_at): self
    {
        $this->scheduled_at = $scheduled_at;

        return $this;
    }
    

    public function getScheduled(): ?bool
    {
        return $this->scheduled;
    }

    public function setScheduled(bool $scheduled): self
    {
        $this->scheduled = $scheduled;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSentAt(?\DateTimeInterface $sent_at): self
    {
        $this->sent_at = $sent_at;

        return $this;
    }

    /**
     * @return Collection|MastodonAccount[]
     */
    public function getAccount(): Collection
    {
        return $this->social_account;
    }

    public function addAccount(MastodonAccount $social_account): self
    {
        if (!$this->social_account->contains($social_account)) {
            $this->social_account[] = $social_account;
            $social_account->setMessage($this);
        }

        return $this;
    }

    public function removeAccount(MastodonAccount $social_account): self
    {
        if ($this->social_account->contains($social_account)) {
            $this->social_account->removeElement($social_account);
            // set the owning side to null (unless already changed)
            if ($social_account->getMessage() === $this) {
                $social_account->setMessage(null);
            }
        }

        return $this;
    }

    public function getSocialAccount(): ?MastodonAccount
    {
        return $this->social_account;
    }

    public function setSocialAccount(?MastodonAccount $social_account): self
    {
        $this->social_account = $social_account;

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

    public function getIsSensitive(): ?bool
    {
        return $this->isSensitive;
    }

    public function setIsSensitive(?bool $isSensitive): self
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }


}
