<?php

class User implements IUser
{
    private int $id;
    private string $name;
    private ?string $password;
    private string $mail;
    private Permission $permission;

    public function __construct(string $mail, string $name = null, Permission $pPermission = Permission::User, int $pId = -1)
    {
        $this->setId($pId);
        $this->setMail($mail);
        $this->setName($name);
        $this->permission = $pPermission;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        if ($name == null) {
            $name = "Kein Name angegeben";
        }
        $this->name = $name;
    }

    public function setPasswordHash(?string $password): void
    {
        $this->password = $password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function validatePassword(string $password): bool
    {
        return password_verify($password, $this->getPasswordHash());
    }

    public function getPasswordHash(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    public function getPermission(): Permission
    {
        return $this->permission;
    }

    public function setPermission(Permission $pPermission): void
    {
        $this->permission = $pPermission;
    }
}