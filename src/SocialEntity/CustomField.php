<?php

namespace App\SocialEntity;



class CustomField
{

    private $id;

    private $name;

    private $value;

    private $verified_at;

    private $mastodonAccount;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getVerifiedAt(): ?\DateTimeInterface
    {
        return $this->verified_at;
    }

    public function setVerifiedAt(?\DateTimeInterface $verified_at): self
    {
        $this->verified_at = $verified_at;

        return $this;
    }

    public function getMastodonAccount(): ?MastodonAccount
    {
        return $this->mastodonAccount;
    }

    public function setMastodonAccount(?MastodonAccount $mastodonAccount): self
    {
        $this->mastodonAccount = $mastodonAccount;

        return $this;
    }

    public function __toString()
    {
        return $this->getName()?$this->getName():"";
    }
    
}
