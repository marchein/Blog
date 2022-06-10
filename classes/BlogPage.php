<?php

class BlogPage
{
    private DBConnection $connection;

    /**
     * @param DBConnection $connection
     */
    public function __construct(DBConnection $connection)
    {
        $this->connection = $connection;
    }

    public function show(): void
    {
        $this->handleLoggedInUser();
        echo '<div class="row">';
        $page = 0;
        $startCount = 0;
        if (isset($_GET["p"])) {
            $page = $_GET["p"];
        }
        if ($page > 0) {
            $startCount = $page * Config::$PageCount;
        }
        $entries = $this->connection->getEntryManager()->getEntriesToShow($startCount);

        foreach ($entries as $entry) {
            echo '<div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card">
                   <h5 class="card-header">' . $entry->getTitle() . '</h5>  
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">' . $entry->getAuthor()->getName() . '</h6>
                        <p class="card-text">' . $entry->getMessage() . '</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <span>' . $entry->getTime() . '</span>';
            if ($this->getCurrentUserPermission() == Permission::Moderator || $this->getCurrentUserPermission() == Permission::Admin) {
                echo '<div>
                            <span class="bi-pencil text-secondary"></span>
                            <span class="bi-trash text-danger"></span>
                       </div>';
            }
            echo '</div>
                </div>
            </div>';
        }

        $numberOfEntries = $this->connection->getEntryManager()->getNumberOfEntries();
        if ($numberOfEntries == 0) {
            echo '<div class="alert alert-info" role="alert">
              Bisher sind noch keine Einträge vorhanden!
            </div>';
        }
        $pageCount = ceil($numberOfEntries / Config::$PageCount);
        if ($pageCount > 1) {
            echo '<nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                <li class="page-item ';
            if ($page == 0) {
                echo "disabled";
            }
            echo '">
                    <a class="page-link" href="?p=' . ($page - 1) . '" tabindex="-1">Previous</a>
                </li>';
            for ($i = 0; $i < $pageCount; $i++) {
                echo '<li class="page-item ';
                if ($page == $i) {
                    echo "active";
                }
                echo '"><a class="page-link" href="?p=' . $i . '">' . ($i + 1) . '</a></li>';
            }

            echo '<li class="page-item ';
            if ($page == ($pageCount - 1)) {
                echo "disabled";
            }
            echo '">
                    <a class="page-link" href="?p=' . ($page + 1) . '">Next</a>
                </li>
            </ul>
        </nav>';
        }
        echo '</div>';
    }

    private function handleLoggedInUser(): void
    {
        if (UserHelpers::IsUserLoggedIn()) {
            $this->currentUser = UserHelpers::GetLoggedInUser($this->connection);
            echo '<div class="d-flex justify-content-between">
                <button type="button" class="btn btn-success mb-4" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Neuen Eintrag anlegen</button>
            </div>';

            $this->showForm();
        }
    }

    private function showForm(): void
    {
        if (isset($_POST["submit"])) {
            $this->handleSubmit($this->currentUser);
        }

        echo '<div class="collapse mb-3" id="collapseExample">
            <form method="post" class="border rounded p-3">
            <div class="mb-3">
              <label for="mail" class="form-label">E-Mail Adresse</label>
              <input type="email" class="form-control-plaintext" id="mail" placeholder="name@example.com" value="' . $this->currentUser->getMail() . '"  aria-label="readonly input" readonly>
            </div>
            <div class="mb-3">
              <label for="title" class="form-label">Titel</label>
              <input type="text" class="form-control" id="title" name="title" placeholder="Titel des Eintrages">
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Nachricht</label>
              <textarea class="form-control" id="message" name="message" rows="3"></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" name="submit" type="submit">Absenden</button>
              </div>
            </form>
        </div>';
    }

    private function handleSubmit(IUser $pUser): void
    {
        $title = $_POST["title"];
        $message = $_POST["message"];

        if (isset($title) && isset($message)) {
            $newEntry = new Entry($pUser, $title, $message);
            $this->connection->getEntryManager()->createEntry($newEntry);
        }
    }

    private function getCurrentUserPermission(): Permission
    {
        if ($this->currentUser == null) {
            return Permission::User;
        }
        return $this->currentUser->getPermission();
    }

    private function handleDeleteButton(Entry $pEntry): void
    {

    }
}