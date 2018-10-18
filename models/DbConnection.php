<?php

class DbConnection
{
    private $connection;
    private static $instance;
    private $host = "127.0.0.1";
    private $username = "root";
    private $password = "toor";
    private $database = "guestbook";

    /**
     * @return DbConnection
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->connection = new PDO('mysql:host=' . $this->host . ';dbname=' .$this->database, $this->username, $this->password);
    }

    private function __clone()
    {
    }

    public function getConnection()
    {
        return $this->connection;
    }
}