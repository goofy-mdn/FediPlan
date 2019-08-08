<?php

namespace App\SocialEntity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MastodonAccount
{

    private $id;

    private $account_id;

    private $username;

    private $acct;

    private $display_name;

    private $locked;

    private $created_at;

    private $followers_count;

    private $following_count;

    private $statuses_count;

    private $note;

    private $url;

    private $avatar;

    private $avatar_static;

    private $header;

    private $header_static;

    private $moved;

    private $bot;

    private $instance;

    private $client;

    private $token;

    private $Fields;

    private $Emojis;




    public function __construct()
    {
        $this->Fields = new ArrayCollection();
        $this->Emojis = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountId(): ?string
    {
        return $this->account_id;
    }

    public function setAccountId(string $account_id): self
    {
        $this->account_id = $account_id;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAcct(): ?string
    {
        return $this->acct;
    }

    public function setAcct(string $acct): self
    {
        $this->acct = $acct;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->display_name;
    }

    public function setDisplayName(?string $display_name): self
    {
        $this->display_name = $display_name;

        return $this;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

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

    public function getFollowersCount(): ?int
    {
        return $this->followers_count;
    }

    public function setFollowersCount(int $followers_count): self
    {
        $this->followers_count = $followers_count;

        return $this;
    }

    public function getFollowingCount(): ?int
    {
        return $this->following_count;
    }

    public function setFollowingCount(int $following_count): self
    {
        $this->following_count = $following_count;

        return $this;
    }

    public function getStatusesCount(): ?int
    {
        return $this->statuses_count;
    }

    public function setStatusesCount(int $statuses_count): self
    {
        $this->statuses_count = $statuses_count;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatarStatic(): ?string
    {
        return $this->avatar_static;
    }

    public function setAvatarStatic(?string $avatar_static): self
    {
        $this->avatar_static = $avatar_static;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(?string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getHeaderStatic(): ?string
    {
        return $this->header_static;
    }

    public function setHeaderStatic(?string $header_static): self
    {
        $this->header_static = $header_static;

        return $this;
    }

    public function getMoved(): ?self
    {
        return $this->moved;
    }

    public function setMoved(?self $moved): self
    {
        $this->moved = $moved;

        return $this;
    }

    public function getBot(): ?bool
    {
        return $this->bot;
    }

    public function setBot(?bool $bot): self
    {
        $this->bot = $bot;

        return $this;
    }

    public function getInstance(): ?string
    {
        return $this->instance;
    }

    public function setInstance(string $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        // set (or unset) the owning side of the relation if necessary
        $newAccount = $client === null ? null : $this;
        if ($newAccount !== $client->getAccount()) {
            $client->setAccount($newAccount);
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }



    public function __toString()
    {
        return $this->getAcct()."@".$this->getInstance();
    }


    /**
     * @return Collection|CustomField[]
     */
    public function getFields(): Collection
    {
        return $this->Fields;
    }

    public function addField(CustomField $field): self
    {
        if (!$this->Fields->contains($field)) {
            $this->Fields[] = $field;
            $field->setMastodonAccount($this);
        }

        return $this;
    }

    public function removeField(CustomField $field): self
    {
        if ($this->Fields->contains($field)) {
            $this->Fields->removeElement($field);
            // set the owning side to null (unless already changed)
            if ($field->getMastodonAccount() === $this) {
                $field->setMastodonAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Emoji[]
     */
    public function getEmojis(): Collection
    {
        return $this->Emojis;
    }

    public function addEmoji(Emoji $emoji): self
    {
        if (!$this->Emojis->contains($emoji)) {
            $this->Emojis[] = $emoji;
            $emoji->setMastodonAccount($this);
        }

        return $this;
    }

    public function removeEmoji(Emoji $emoji): self
    {
        if ($this->Emojis->contains($emoji)) {
            $this->Emojis->removeElement($emoji);
            // set the owning side to null (unless already changed)
            if ($emoji->getMastodonAccount() === $this) {
                $emoji->setMastodonAccount(null);
            }
        }

        return $this;
    }

    
}
