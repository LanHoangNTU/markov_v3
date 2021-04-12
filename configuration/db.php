<?php
use MongoDB\Driver\Manager as MongoManager;

class Database {
    // Singleton
    private static $instance = null;

    // Manager
    private $markov_manager;

    // Collection names
    public static $students = "markov.students";
    public static $avail_fields = "markov.available_fields";
    public static $class_laws = "markov.class_laws";
    public static $status_matrix = "markov.status_matrix";

    // connect to mongodb
    private function __construct()
    {
        $this->markov_manager = new MongoManager("mongodb://localhost:27017/markov");
    }

    public static function getInstance()
    {
        if (self::$instance == null)
        {
        self::$instance = new Database();
        }
    
        return self::$instance;
    }

    public function getDB() {
        return $this->markov_manager;
    }
}
?>