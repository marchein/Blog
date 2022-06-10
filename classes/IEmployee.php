<?php
interface IEmployee extends IUser {
    public function getMemberNo(): string;
    public function setMemberNo(string $memberNo): void;
}