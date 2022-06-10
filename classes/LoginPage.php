<?php

/**
 *
 */
class LoginPage
{
    /**
     * @var DBConnection
     */
    private DBConnection $connection;

    /**
     * @param DBConnection $connection
     */
    public function __construct(DBConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return void
     */
    public static function handleLogout(): void
    {
        session_destroy();
        self::redirectToHome();
    }

    /**
     * @return void
     */
    private static function redirectToHome(): void
    {
        echo "<script>location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    }

    /**
     * @return void
     */
    public function show(): void
    {
        if (isset($_POST["submit"])) { //check if form was submitted
            $this->handleLogin();
        }

        echo '<form method="post">
            <div class="container d-flex justify-content-center">
                <div>
                    <h1 class="h3 mb-3">Bitte melden Sie sich an</h1>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="mail"  name="mail" placeholder="user@jam-software.com">
                        <label for="floatingInput">E-Mail Adresse</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password"  name="password" placeholder="Passwort">
                        <label for="floatingPassword">Passwort</label>
                    </div>
                    
                    <button class="w-100 btn btn-lg btn-primary mb-3" name="submit" type="submit">Anmelden</button>
                    <a href="?page=register" class="w-100 btn btn-lg btn-outline-secondary">Registrieren</a>
                </div>
            </div>
        </form>';
    }

    /**
     * @return void
     */
    private function handleLogin(): void
    {
        $mail = $_POST["mail"];
        $password = $_POST["password"]; //get input text

        $user = $this->connection->getUserManager()->getUserByMail($mail);

        if ($user != null && isset($mail) && isset($password)) {
            $loggedIn = $user->validatePassword($password);

            if ($loggedIn) {
                $this->connection->getUserManager()->userLoggedIn($user);
                $_SESSION['userid'] = $user->getId();
                $_SESSION['last_login'] = $this->connection->getUserManager()->getLastLoginOfUser($user);
                $this->redirectToHome();
            } else {
                echo '<div class="alert alert-danger" role="alert">
                  Nutzer mit dieser Mail nicht vorhanden oder Passwort falsch!
                </div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">
                Mail Adresse oder Passwort wurden nicht angegeben!
                </div>';
        }
    }

    public function showRegister(): void
    {
        if (isset($_POST["submit"])) { //check if form was submitted
            $this->handleRegister();
        }

        echo '<form method="post">
            <div class="container d-flex justify-content-center">
                <div>
                    <h1 class="h3 mb-3">Bitte registrieren Sie sich</h1>
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name"  name="name" placeholder="Vorname Nachname">
                        <label for="floatingInput">Name</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="mail"  name="mail" placeholder="user@jam-software.com">
                        <label for="floatingInput">E-Mail Adresse</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password"  name="password" placeholder="Passwort">
                        <label for="floatingPassword">Passwort</label>
                    </div>
                    
                    <button class="w-100 btn btn-lg btn-primary" name="submit" type="submit">Anmelden</button>
                    
                </div>
            </div>
        </form>';
    }

    private function handleRegister(): void
    {
        $name = $_POST["name"];
        $mail = $_POST["mail"];
        $password = $_POST["password"]; //get input text

        $user = new User($mail, $name);
        $user->setPassword($password);

        $createdUser = $this->connection->getUserManager()->createUser($user);

        if ($createdUser != null && $createdUser->validatePassword($password)) {
            $this->connection->getUserManager()->userLoggedIn($createdUser);
            $_SESSION['userid'] = $createdUser->getId();
            $_SESSION['last_login'] = $this->connection->getUserManager()->getLastLoginOfUser($user);
            $this->redirectToHome();
        } else {
            echo '<div class="alert alert-danger" role="alert">
                  Benutzer mit dieser E-Mail Adresse kann nicht angelegt werden!<br />
                  Wollten Sie sich <a href="?page=login">anmelden</a>?
                </div>';
        }

    }
}