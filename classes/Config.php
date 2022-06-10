<?php

/**
 * Config for whole application
 */
class Config
{
    /**
     * @var string Name of the application
     */
    public static string $AppName = "Blog";
    /**
     * @var string Host of mySQL server
     */
    public static string $DB_Host = "localhost";
    /**
     * @var string Username of mySQL user
     */
    public static string $DB_User = "root";
    /**
     * @var string Password of mySQL user
     */
    public static string $DB_Password = "";
    /**
     * @var string Database name for application
     */
    public static string $DB_Database = "blog";

    /**
     * @var int How many entries should be displayed on one page
     */
    public static int $PageCount = 9;
}