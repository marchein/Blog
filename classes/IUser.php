<?php

interface IUser {
    public function getId(): int;
    public function setId(int $id): void;
    public function getName(): string;
    public function setName(?string $name): void;
    public function getMail(): string;
    public function setMail(string $mail): void;
    public function getPasswordHash(): string;
    public function setPassword(string $password): void;
    public function validatePassword(string $password): bool;
    public function getPermission(): Permission;
    public function setPermission(Permission $pPermission): void;
}