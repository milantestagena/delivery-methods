<?php

class DB {

    private $host = "localhost";
    private $port = "9200";
    private $database = "delivery";
    private $user = "root";
    private $password = "root";
    
    public $db;

    public function __construct() {
        $this->db = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->database", $this->user, $this->password);
    }

}
