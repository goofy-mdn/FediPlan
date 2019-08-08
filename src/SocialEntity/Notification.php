<?php

namespace App\SocialEntity;


class Notification
{
    /** @var string */
    private $id;
    /** @var string */
    private $type;
    /** @var \DateTime */
    private $created_at;
    /** @var MastodonAccount */
    private $account;
    /** @var Status */
    private $status;


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
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return MastodonAccount
     */
    public function getAccount(): MastodonAccount
    {
        return $this->account;
    }

    /**
     * @param MastodonAccount $account
     */
    public function setAccount(MastodonAccount $account): void
    {
        $this->account = $account;
    }

    /**
     * @return Status
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * @param Status $status
     */
    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

}
