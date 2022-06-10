<?php

/**
 *
 */
class DBConnection
{
    /**
     * @var string
     */
    private string $server;
    /**
     * @var string
     */
    private string $username;
    /**
     * @var string
     */
    private string $password;
    /**
     * @var mysqli
     */
    private mysqli $connection;

    /**
     * @var EntryManager
     */
    private EntryManager $entryManager;
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @param string $server
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct(string $server, string $username, string $password, string $database)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;

        $this->connect($database);

        $this->setupManagers($this);
    }

    /**
     * @param string $pDatabase
     * @return bool
     */
    public function connect(string $pDatabase): bool
    {
        $database = $pDatabase;

        // Create connection
        $this->connection = new mysqli($this->server, $this->username, $this->password);

        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }

        $sql = "CREATE DATABASE IF NOT EXISTS " . $database . ";";
        $this->connection->query($sql);
        return $this->connection->select_db($database);
    }

    /**
     * @param string $pQuery
     * @return mysqli_result|bool
     */
    public function query(string $pQuery): mysqli_result|bool
    {
        return $this->connection->query($pQuery);
    }

    /**
     * @param DBConnection $pConnection
     * @return void
     */
    private function setupManagers(DBConnection $pConnection): void
    {
        $this->userManager = new UserManager($pConnection);
        $this->entryManager = new EntryManager($pConnection);
    }

    public function prepare(string $pQuery): mysqli_stmt|false
    {
        return $this->connection->prepare($pQuery);
    }

    public function getLastId(): int|string
    {
        return $this->connection->insert_id;
    }

    /**
     * Disconnects current mySQL session
     * @return bool true if successful
     */
    public function disconnect(): bool
    {
        return $this->connection->close();
    }

    /**
     * @return EntryManager
     */
    public function getEntryManager(): EntryManager
    {
        return $this->entryManager;
    }

    /**
     * @return UserManager
     */
    public function getUserManager(): UserManager
    {
        return $this->userManager;
    }
}