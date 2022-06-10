<?php

/**
 *
 */
class Entry
{
    /**
     * @var int
     */
    private int $id;
    /**
     * @var IUser
     */
    private IUser $author;
    /**
     * @var string
     */
    private string $title;
    /**
     * @var string
     */
    private string $message;
    /**
     * @var int
     */
    private int $timestamp;

    /**
     * @param int $id
     * @param IUser $author
     * @param string $title
     * @param string $message
     */
    public function __construct(IUser $author, string $title, string $message, int $id = -1,int $timestamp = -1)
    {
        $this->id = $id;
        $this->author = $author;
        $this->title = $title;
        $this->message = $message;
        $this->timestamp = $timestamp == -1 ? time() : $timestamp;
    }

    /**
     * @return IUser
     */
    public function getAuthor(): IUser
    {
        return $this->author;
    }

    /**
     * @param IUser $author
     */
    public function setAuthor(IUser $author): void
    {
        $this->author = $author;
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
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return date("d.m.Y - H:i", $this->timestamp);
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}