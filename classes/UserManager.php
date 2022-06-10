<?php

class UserManager implements IManager
{
    private DBConnection $connection;

    public function __construct(DBConnection $pConnection)
    {
        $this->connection = $pConnection;
        $this->initTable();
    }

    public function initTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Users (
          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(128) NOT NULL,
          mail VARCHAR(128) NOT NULL UNIQUE,
          permission INT(6) UNSIGNED NOT NULL DEFAULT '0',
          password VARCHAR(128) NOT NULL,
          lastlogin VARCHAR(40) DEFAULT SHA1(CURRENT_TIMESTAMP),
          memberno VARCHAR(128) 
        );";
        $this->connection->query($sql);
    }

    public function createUser(IUSer $pUser): ?IUser
    {
        $checkForExistingUser = $this->getUserByMail($pUser->getMail());
        if ($checkForExistingUser == null) {
            if ($this->getNumberOfUsers() == 0) {
                $pUser->setPermission(Permission::Admin);
            }
            $sql = "INSERT INTO Users (name, mail, password, permission) VALUES (?, ?, ?, ?);";
            $statement = $this->connection->prepare($sql);
            $userName = $pUser->getName();
            $mail = $pUser->getMail();
            $password = $pUser->getPasswordHash();
            $permission = $pUser->getPermission()->value;
            $statement->bind_param("sssi", $userName, $mail, $password, $permission);
            $result = $statement->execute();
            if ($result) {
                $last_id = $this->connection->getLastId();
                $pUser->setId($last_id);

                if ($pUser instanceof IEmployee) {
                    $sql = "UPDATE Users SET memberno = '" . $pUser->getMemberNo() . "' WHERE id = " . $pUser->getId() . ";";
                    $this->connection->query($sql);
                }

                return $pUser;
            }
        }

        return null;
    }

    public function getUserByMail(string $pMail): ?IUser
    {
        return $this->getUser(-1, $pMail);
    }

    private function getUser(int $pId = -1, string $pMail = null): ?IUser
    {
        if ($pId == -1 && $pMail != null) {
            $sql = "SELECT * FROM `users` where mail = '" . $pMail . "';";
        } else {
            $sql = "SELECT * FROM `users` where id = " . $pId . ";";
        }

        $result = $this->connection->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $permissionValue = $row["permission"];
            $permission = Permission::from($permissionValue);
            if ($row["memberno"] == null) {
                $user = new User($row["mail"], $row["name"], $permission, $row["id"]);
            } else {
                $user = new Employee($row["mail"], $row["name"], $permission, $row["memberno"], $row["id"]);
            }
            $user->setPasswordHash($row["password"]);
            return $user;
        }
        return null;
    }

    public function getNumberOfUsers(): int
    {
        $sql = "SELECT COUNT(*) count FROM `Users`";
        $result = $this->connection->query($sql);
        $fetchedResult = $result->fetch_assoc();
        return (int)$fetchedResult["count"];
    }

    public function editUser(IUSer $pUser): void
    {
        if (!$this->doesUserExist($pUser->getId())) {
            return;
        }
        $sql = "UPDATE `users` SET `name` = ?, `mail` = ?, `password` = ?, `permission` = ? WHERE `users`.`id` = ?;";
        $statement = $this->connection->prepare($sql);
        $userName = $pUser->getName();
        $mail = $pUser->getMail();
        $password = $pUser->getPasswordHash();
        $permission = $pUser->getPermission()->value;
        $id = $pUser->getId();
        $statement->bind_param("sssii", $userName, $mail, $password, $permission, $id);
        $statement->execute();

        if ($pUser instanceof IEmployee) {
            $sql = "UPDATE Users SET memberno = '" . $pUser->getMemberNo() . "' WHERE id = " . $id . ";";
            $this->connection->query($sql);
        }
    }

    public function doesUserExist(int $pUserId): bool
    {
        $sql = "SELECT EXISTS(SELECT * FROM `users` WHERE `id` = " . $pUserId . ") doesexist";
        $result = $this->connection->query($sql);
        $row = $result->fetch_assoc();

        return $row["doesexist"];
    }

    public function userLoggedIn(IUser $pUser): void
    {
        $sql = "UPDATE Users SET lastlogin = SHA1(CURRENT_TIMESTAMP) WHERE id = " . $pUser->getId() . ";";
        $this->connection->query($sql);
    }

    public function getLastLoginOfUser(IUser $pUser): string
    {
        $sql = "SELECT lastlogin FROM `users` WHERE `id` = " . $pUser->getId() . ";";
        $result = $this->connection->query($sql);
        $row = $result->fetch_assoc();

        return $row["lastlogin"];
    }

    public function deleteUser(IUser $pUser): bool
    {
        $sql = "DELETE FROM `users` WHERE `users`.`id` = " . $pUser->getId() . "";
        $result = $this->connection->query($sql);
        return $result;
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT Id FROM `users`";
        $result = $this->connection->query($sql);

        $users = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $this->getUserById($row["Id"]);
            }
        }
        return $users;
    }

    public function getUserById(int $pId): ?IUser
    {
        return $this->getUser($pId);
    }
}