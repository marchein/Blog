<?php

class HeaderContent
{
    private DBConnection $connection;

    /**
     * @param DBConnection $connection
     */
    public function __construct(DBConnection $connection)
    {
        $this->connection = $connection;
        $this->show();
    }

    private function show(): void
    {
        echo '<div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container justify-content-between">
            <a href="/Learning/Guestbook/" class="navbar-brand d-flex align-items-center">
                <strong>' . Config::$AppName . '</strong>
            </a>
            <div>';
        if (UserHelpers::IsUserLoggedIn()) {
            if (UserHelpers::GetLoggedInUser($this->connection)->getPermission() == Permission::Admin) {
                echo '<a class="btn btn-sm btn-primary me-2" type="button" href="?page=users">Rechteverwaltung</a>';
            }
            echo '<a class="btn btn-sm btn-danger" type="button" href="?page=logout">Abmelden</a>';
        } else {
            echo '<a class="btn btn-sm btn-success" type="button" href="?page=login">Anmelden</a>';
        }

        echo '</div>
        </div>
    </div>';
    }
}