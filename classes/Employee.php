<?php

class Employee extends User implements IEmployee
{
    private string $memberNo;

    public function __construct(string $mail, string $name = null, Permission $pPermission = Permission::User, string $pMemberNo = null, int $id = -1)
    {
        parent::__construct($mail, $name, $pPermission, $id);
        $this->setMemberNo($pMemberNo);
    }

    public function getMemberNo(): string
    {
        return $this->memberNo;
    }

    public function setMemberNo(string $memberNo): void
    {
        $this->memberNo = $memberNo;
    }
}