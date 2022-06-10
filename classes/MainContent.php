<?php

class MainContent
{
    private DBConnection $connection;

    /**
     * @param DBConnection $connection
     */
    public function __construct(DBConnection $connection)
    {
        $this->connection = $connection;
        $this->show();
        if (UserHelpers::IsUserLoggedIn() &&  // user is logged in
            !isset($_SESSION["last_login"]) || // but last login is not set
            (isset($_SESSION["last_login"]) && $_SESSION["last_login"] != $connection->getUserManager()->getLastLoginOfUser(UserHelpers::GetLoggedInUser($connection)))) { // or last login does not match session value
            LoginPage::handleLogout();
        }
    }

    public function show(): void
    {
        $page = "guestbook";
        if (isset($_GET["page"])) {
            $page = $_GET["page"];
        }

        $loginPage = new LoginPage($this->connection);
        $guestbook = new BlogPage($this->connection);

        if (UserHelpers::IsUserLoggedIn() && UserHelpers::GetLoggedInUser($this->connection)) {
            $usersPage = new UserPage($this->connection);
        }

        switch ($page) {
            case "login":
                $loginPage->show();
                break;
            case "logout":
                $loginPage->handleLogout();
                break;
            case "register":
                $loginPage->showRegister();
                break;
            case "users":
                if (UserHelpers::IsUserLoggedIn() && UserHelpers::GetLoggedInUser($this->connection)) {
                    $usersPage->show();
                    break;
                }
            case "guestbook":
            default:
                $guestbook->show();
                break;
        }

    }
}