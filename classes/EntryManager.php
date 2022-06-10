<?php

class EntryManager implements IManager
{
    private DBConnection $connection;

    public function __construct(DBConnection $pConnection)
    {
        $this->connection = $pConnection;
        $this->initTable();
    }

    public function initTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Entries (
            EntryId int NOT NULL AUTO_INCREMENT,
            authorId int(6) UNSIGNED NOT NULL,
            title varchar(128) NOT NULL,
            message varchar(1024) NOT NULL,
            creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (EntryId),
            FOREIGN KEY (authorId) REFERENCES users(id) ON DELETE CASCADE
        );";
        $this->connection->query($sql);
    }

    public function getNumberOfEntries(): int
    {
        $sql = "SELECT COUNT(*) count FROM `Entries`";
        $result = $this->connection->query($sql);
        $fetchedResult = $result->fetch_assoc();
        return (int)$fetchedResult["count"];
    }

    public function getEntriesToShow(int $pStarting = 0): array
    {
        $sql = "SELECT EntryId FROM `entries` ORDER BY EntryId DESC LIMIT " . $pStarting . ", " . Config::$PageCount . ";";
        $result = $this->connection->query($sql);

        $entries = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $entries[] = $this->getEntry($row["EntryId"]);
            }
        }

        return $entries;
    }

    private function getEntry(int $pId): ?Entry
    {
        $sql = "SELECT 
	            EntryId, authorId, title, message, UNIX_TIMESTAMP(creation) timestamp
                FROM `entries`
                WHERE EntryId = " . $pId . ";";
        $result = $this->connection->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $author = $this->connection->getUserManager()->getUserById($row["authorId"]);
            return new Entry($author, $row["title"], $row["message"], $row["EntryId"], $row["timestamp"]);
        }
        return null;
    }

    public function createEntry(Entry $pEntry)
    {
        $sql = "INSERT INTO Entries (authorId, title, message) VALUES (?, ?, ?);";
        $statement = $this->connection->prepare($sql);
        $authorId = $pEntry->getAuthor()->getId();
        $title = $pEntry->getTitle();
        $message = $pEntry->getMessage();

        $statement->bind_param("iss", $authorId, $title, $message);
        $statement->execute();

        $last_id = $this->connection->getLastId();
        $pEntry->setId($last_id);
    }
}