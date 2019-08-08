<?php

namespace App\SocialEntity;



class Client
{

    private $id;

    private $host;

    private $client_id;

    private $client_secret;

    private $account;

    private $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->client_id;
    }

    public function setClientId(?string $client_id): self
    {
        $this->client_id = $client_id;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->client_secret;
    }

    public function setClientSecret(?string $client_secret): self
    {
        $this->client_secret = $client_secret;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAccount(): ?MastodonAccount
    {
        return $this->account;
    }

    public function setAccount(?MastodonAccount $account): self
    {
        $this->account = $account;

        return $this;
    }

  
}
