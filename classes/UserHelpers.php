<?php

class UserHelpers
{
    public static function formatUser(IUser $pUser): string
    {
        if ($pUser instanceof IEmployee) {
            $resultString = "Employee";
        } else {
            $resultString = "User";
        }

        $resultString = $resultString . "(ID: " . $pUser->getId() . ", Name: " . $pUser->getName();

        if ($pUser instanceof IEmployee) {
            $resultString = $resultString . ", Mail: " . $pUser->getMail();
        }

        return $resultString . ")";
    }

    public static function IsUserLoggedIn(): bool
    {
        return isset($_SESSION['userid']);
    }

    public static function GetLoggedInUser(DBConnection $pDBConnection): ?IUser
    {
        if (isset($_SESSION['userid'])) {
            //Abfrage der Nutzer ID vom Login
            $userid = $_SESSION['userid'];
            if (!$pDBConnection->getUserManager()->doesUserExist($userid)) {
                LoginPage::handleLogout();
                return null;
            }
            return $pDBConnection->getUserManager()->getUserById($userid);
        }
        return null;
    }
}