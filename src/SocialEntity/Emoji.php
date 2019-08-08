<?php

namespace App\SocialEntity;



class Emoji
{
    private $id;

    private $shortcode;

    private $static_url;

    private $url;

    private $visible_in_picker;

    private $mastodonAccount;



    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortcode(): ?string
    {
        return $this->shortcode;
    }

    public function setShortcode(string $shortcode): self
    {
        $this->shortcode = $shortcode;

        return $this;
    }

    public function getStaticUrl(): ?string
    {
        return $this->static_url;
    }

    public function setStaticUrl(string $static_url): self
    {
        $this->static_url = $static_url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getVisibleInPicker(): ?bool
    {
        return $this->visible_in_picker;
    }

    public function setVisibleInPicker(?bool $visible_in_picker): self
    {
        $this->visible_in_picker = $visible_in_picker;

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

}
