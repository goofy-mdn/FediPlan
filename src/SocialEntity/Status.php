<?php

namespace App\SocialEntity;



use App\Entity\Emoji;
use App\Entity\MastodonAccount;

class Status
{
    /** @var string */
    private $id;
    /** @var string */
    private $uri;
    /** @var string */
    private $url;
    /** @var MastodonAccount */
    private $account;
    /** @var string */
    private $in_reply_to_id;
    /** @var string */
    private $in_reply_to_account_id;
    /** @var string */
    private $content;
    /** @var \DateTime */
    private $created_at;
    /** @var Emoji[] */
    private $emojis = [];
    /** @var int */
    private $replies_count;
    /** @var int */
    private $reblogs_count;
    /** @var int */
    private $favourites_count;
    /** @var boolean */
    private $reblogged;
    /** @var boolean */
    private $favourited;
    /** @var boolean */
    private $muted;
    /** @var boolean */
    private $sensitive_;
    /** @var string */
    private $spoiler_text;
    /** @var string */
    private $visibility;
    /** @var Attachment[] */
    private $media_attachments = [];
    /** @var Mention[] */
    private $mentions = [];
    /** @var Tag[] */
    private $tags = [];
    /** @var Card */
    private $card;
    /** @var Application */
    private $application;
    /** @var string */
    private $language;
    /** @var boolean */
    private $pinned;
    /** @var Status */
    private $reblog;

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
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
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
     * @return string
     */
    public function getInReplyToId(): string
    {
        return $this->in_reply_to_id;
    }

    /**
     * @param string $in_reply_to_id
     */
    public function setInReplyToId(?string $in_reply_to_id ): void
    {
        $this->in_reply_to_id = $in_reply_to_id;
    }

    /**
     * @return string
     */
    public function getInReplyToAccountId(): string
    {
        return $this->in_reply_to_account_id;
    }

    /**
     * @param string $in_reply_to_account_id
     */
    public function setInReplyToAccountId(?string $in_reply_to_account_id): void
    {
        $this->in_reply_to_account_id = $in_reply_to_account_id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
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
     * @return Emoji[]
     */
    public function getEmojis(): array
    {
        return $this->emojis;
    }

    /**
     * @param Emoji $emojis
     */
    public function setEmojis(array $emojis): void
    {
        $this->emojis = $emojis;
    }

    /**
     * @return int
     */
    public function getRepliesCount(): int
    {
        return $this->replies_count;
    }

    /**
     * @param int $replies_count
     */
    public function setRepliesCount(int $replies_count): void
    {
        $this->replies_count = $replies_count;
    }

    /**
     * @return int
     */
    public function getReblogsCount(): int
    {
        return $this->reblogs_count;
    }

    /**
     * @param int $reblogs_count
     */
    public function setReblogsCount(int $reblogs_count): void
    {
        $this->reblogs_count = $reblogs_count;
    }

    /**
     * @return int
     */
    public function getFavouritesCount(): int
    {
        return $this->favourites_count;
    }

    /**
     * @param int $favourites_count
     */
    public function setFavouritesCount(int $favourites_count): void
    {
        $this->favourites_count = $favourites_count;
    }

    /**
     * @return bool
     */
    public function isReblogged(): bool
    {
        return $this->reblogged;
    }

    /**
     * @param bool $reblogged
     */
    public function setReblogged(bool $reblogged): void
    {
        $this->reblogged = $reblogged;
    }

    /**
     * @return bool
     */
    public function isFavourited(): bool
    {
        return $this->favourited;
    }

    /**
     * @param bool $favourited
     */
    public function setFavourited(bool $favourited): void
    {
        $this->favourited = $favourited;
    }

    /**
     * @return bool
     */
    public function isMuted(): bool
    {
        return $this->muted;
    }

    /**
     * @param bool $muted
     */
    public function setMuted(bool $muted): void
    {
        $this->muted = $muted;
    }

    /**
     * @return bool
     */
    public function isSensitive(): bool
    {
        return $this->sensitive_;
    }

    /**
     * @param bool $sensitive_
     */
    public function setSensitive(bool $sensitive_): void
    {
        $this->sensitive_ = $sensitive_;
    }

    /**
     * @return string
     */
    public function getSpoilerText(): string
    {
        return $this->spoiler_text;
    }

    /**
     * @param string $spoiler_text
     */
    public function setSpoilerText(string $spoiler_text): void
    {
        $this->spoiler_text = $spoiler_text;
    }

    /**
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @return Attachment[]
     */
    public function getMediaAttachments(): array
    {
        return $this->media_attachments;
    }

    /**
     * @param Attachment[] $media_attachments
     */
    public function setMediaAttachments(array $media_attachments): void
    {
        $this->media_attachments = $media_attachments;
    }

    /**
     * @return Mention[]
     */
    public function getMentions(): array
    {
        return $this->mentions;
    }

    /**
     * @param Mention[] $mentions
     */
    public function setMentions(array $mentions): void
    {
        $this->mentions = $mentions;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param Tag[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return Card
     */
    public function getCard(): Card
    {
        return $this->card;
    }

    /**
     * @param Card $card
     */
    public function setCard(Card $card): void
    {
        $this->card = $card;
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @param Application $application
     */
    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return bool
     */
    public function isPinned(): bool
    {
        return $this->pinned;
    }

    /**
     * @param bool $pinned
     */
    public function setPinned(bool $pinned): void
    {
        $this->pinned = $pinned;
    }

    /**
     * @return Status
     */
    public function getReblog(): Status
    {
        return $this->reblog;
    }

    /**
     * @param Status $reblog
     */
    public function setReblog(Status $reblog): void
    {
        $this->reblog = $reblog;
    }



}
