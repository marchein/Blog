<?php

class UserPage
{
    private DBConnection $DBConnection;

    /**
     * @param DBConnection $DBConnection
     */
    public function __construct(DBConnection $DBConnection)
    {
        $this->DBConnection = $DBConnection;
    }

    public function show(): void
    {
        $action = "list";
        if (isset($_GET["action"])) {
            $action = $_GET["action"];
        }

        switch ($action) {
            case "add":
                $this->showUserForm();
                break;
            case "delete":
                $this->deleteUser(isset($_GET["id"]) ? $_GET["id"] : -1);
                break;
            case "edit":
                $this->showUserForm(isset($_GET["id"]) ? $_GET["id"] : -1);
                break;
            case "list":
            default:
                $this->listUsers();
                break;
        }
    }

    private function showUserForm(int $pUserId = -1): void
    {
        if ($pUserId >= 0) {
            echo '<h2>Benutzer bearbeiten</h2>';
            $user = $this->DBConnection->getUserManager()->getUserById($pUserId);
        } else {
            echo '<h2>Benutzer anlegen</h2>';
            $user = null;
        }

        if (isset($_POST["submit"])) {
            $this->handleSubmit($user);
        }

        echo '<div class="mb-3">
            <form method="post" class="border rounded p-3">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" placeholder="Max Mustermann" value="' . (isset($user) ? $user->getName() : null) . '" name="name">
            </div>
            <div class="mb-3">
              <label for="mail" class="form-label">E-Mail Adresse</label>
              <input type="email" class="form-control" id="mail" placeholder="name@example.com" value="' . (isset($user) ? $user->getMail() : null) . '" name="mail">
            </div>';
        if ($user instanceof IEmployee) {
            echo '<div class="mb-3">
              <label for="memberno" class="form-label">Mitarbeiter Nummer</label>
              <input type="number" class="form-control" id="memberno" placeholder="42" value="' . (isset($user) ? $user->getMemberNo() : null) . '" name="memberno">
            </div>';
        }

        echo '
      <label for="permission" class="form-label">Berechtigungen</label>
        <select class="form-select" id="permission" name="permission">';
        foreach (Permission::cases() as $permission) {
            $isSelectedValue = "";
            if ((isset($user) ? $user->getPermission() : Permission::User) == $permission) {
                $isSelectedValue = 'selected';
            }

            echo '<option value="' . $permission->value . '" ' . $isSelectedValue . '>' . $permission->name . '</option>';
        }
        echo '</select>
        <div class="mb-3">
            <label for="password" class="form-label">Passwort</label>
            <input type="password" class="form-control" id="password" placeholder="Leer lassen, wenn das Passwort nicht geändert werden soll" value="" name="password">
        </div>
        <div class="col-12">
            <button class="btn btn-primary" name="submit" type="submit">Absenden</button>
          </div>
        </form>
        </div>
        ';
    }

    private function handleSubmit(?IUser $pUser): void
    {
        $name = $_POST["name"];
        $mail = $_POST["mail"];
        if (isset($_POST["memberno"])) {
            $memberno = $_POST["memberno"];
        }
        $permission = $_POST["permission"];
        $password = $_POST["password"];

        if (!isset($pUser)) {
            $pUser = new User($mail, $name, Permission::from($permission));
            $pUser->setPassword($password);
            $this->DBConnection->getUserManager()->createUser($pUser);
            echo '<div class="alert alert-success" role="alert">
                  Benutzer wurde angelegt!
                </div>';
        } else {

            if (isset($name) && isset($mail) && isset($permission)) {
                $pUser->setName($name);
                $pUser->setMail($mail);
                $pUser->setPermission(Permission::from($permission));
                if (isset($memberno) && $pUser instanceof IEmployee) {
                    $pUser->setMemberNo($memberno);
                }

                if (isset($password) && strlen($password) > 4) {
                    $pUser->setPassword($password);
                }

                $this->DBConnection->getUserManager()->editUser($pUser);

                echo '<div class="alert alert-success" role="alert">
                  Benutzer wurde aktualisiert!
                </div>';
            }
        }
    }

    private function deleteUser(int $pUserId): void
    {
        echo '<h2>Benutzer löschen</h2>';
        $user = $this->DBConnection->getUserManager()->getUserById($pUserId);

        if (isset($_POST["submit"])) {
            $this->handleDeleteSubmit($user);
        } else {

            echo '<div class="mb-3">
            <form method="post" class="border rounded p-3">
            Möchten Sie den Nutzer wirklich löschen?<br />
        <div class="col-12">
             <input name="id" value="' . $user->getId() . '" hidden>
            <button class="btn btn-primary" name="submit" type="submit">Absenden</button>
          </div>
        </form>
        </div>
        ';
        }
    }

    private function handleDeleteSubmit(IUser $pUser): void
    {
        $result = $this->DBConnection->getUserManager()->deleteUser($pUser);
        if ($result) {
            echo '<div class="alert alert-success" role="alert">
                  Benutzer wurde gelöscht!
                </div>';
        }
    }

    private function listUsers(): void
    {
        echo '<h2>Rechteverwaltung</h2>';
        echo '<div class="d-flex justify-content-between">
            <a href="?page=users&action=add" type="button" class="btn btn-success" role="button">Neuen Benutzer anlegen</a>
        </div>';
        echo '<table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>E-Mail Adresse</th>
                    <th>Berechtigungen</th>
                    <th>Mitarbeiternummer</th>
                    <th>Aktionen</th>
                </tr>
            </thead>';
        $allUsers = $this->DBConnection->getUserManager()->getAllUsers();
        foreach ($allUsers as $user) {
            echo '<tr>';
            if ($user instanceof IUser) {
                echo '<td>' . $user->getName() . '</td>';
                echo '<td>' . $user->getMail() . '</td>';
                echo '<td>' . $user->getPermission()->name . '</td>';
                if ($user instanceof IEmployee) {
                    echo '<td>' . $user->getMemberNo() . '</td>';
                } else {
                    echo '<td>-</td>';
                }
                echo '<td>
                    <a href="?page=users&action=edit&id=' . $user->getId() . '" class="bi-pencil text-secondary"></a>';
                if ($user->getId() != UserHelpers::GetLoggedInUser($this->DBConnection)->getId()) {
                    echo '<a href="?page=users&action=delete&id=' . $user->getId() . '" class="bi-trash text-danger"></a>';
                }
                echo '</td>';
            }

            echo "</tr>";
        }
        echo '</table>';
    }
}