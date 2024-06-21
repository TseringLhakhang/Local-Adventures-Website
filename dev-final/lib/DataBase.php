<?php
class DataBase {
    const DB_DEBUG = false;
    public $pdo = '';

    public function __construct($dataBaseUser, $dataBaseName) {
        $this->pdo = null;

        include 'pass.php';

        $dataBasePassword = '';


        switch (substr($dataBaseUser, strpos($dataBaseUser, "_") + 1)) {
            case 'reader':
                $dataBasePassword = $reader;
                break;
            case 'writer':
                $dataBasePassword = $writer;
                break;
        }



        $query = NULL;
        $dsn =  'mysql:host=webdb.uvm.edu;dbname=' . $dataBaseName;

        if (self::DB_DEBUG) {
            print '<p>' . $dataBaseUser . '</p>';
            print '<p>' . $dataBasePassword . '</p>';
            print '<p>' . $dsn . '</p>';
        }

        try {
            $this->pdo = new PDO($dsn, $dataBaseUser, $dataBasePassword);

            if (!$this->pdo) {
                if (self::DB_DEBUG) {
                    print PHP_EOL . '<!-- NOT Connected -->' . PHP_EOL;
                }
                //$this->pdo = 0;
            } else {
                if (self::DB_DEBUG) {
                    print PHP_EOL . '<!-- Connected -->' . PHP_EOL;
                }
            }
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            if (self::DB_DEBUG) {
                print '<!-- Error Connecting: ' . $error_message . '-->' . PHP_EOL;
            }
        }
        return $this->pdo;
    } // ends constructor

    public function select($query, $values = '') {
        $statement = $this->pdo->prepare($query);
        if (is_array($values)) {
            $statement->execute($values);
        } else {
            $statement->execute();
        }
        $recordSet = $statement->fetchAll(PDO::FETCH_ASSOC);

        $statement->closeCursor();
        return $recordSet;
    }

    public function insert($query, $values = '') {
        $inserted = false;
        $statement = $this->pdo->prepare($query);
        if (is_array($values)) {
            $inserted = $statement->execute($values);
        } 
    
        $statement->closeCursor();
        return $inserted;
    }

    public function lastInsert() {
        $query = 'SELECT LAST_INSERT_ID()';
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $recordSet = $statement->fetchAll();
        $statement->closeCursor();

        if ($recordSet)
            return $recordSet[0]['LAST_INSERT_ID()'];
        return -1;
    }


    public function update($query, $values = '') {
        $updated = false;
        $statement = $this->pdo->prepare($query);
        if (is_array($values)) {
            $updated = $statement->execute($values);
        } 
        $statement->closeCursor();
        return $updated;
    }
    
    public function delete($query, $values = '') {
        $deleted = false;
        $statement = $this->pdo->prepare($query);
        if (is_array($values)) {
            $deleted = $statement->execute($values);
        } 
        $statement->closeCursor();
        return $deleted;
    }
    
    public function sqlDisplay($sqlText, $data){
        foreach ($data as $value){
        // Look for ? and replace with the value
        // look for ? replace with value
            $pos = strpos($sqlText, '?');
            if ($pos !== false) {
                $sqlText = substr_replace($sqlText, '"' . $value . '"', $pos, strlen('?'));
            }
        }
        return $sqlText;
    }
    
    public function totalRecords($query, $values = '') {
        $statement = $this->pdo->prepare($query);
        if (is_array($values)) {
            $statement->execute($values);
        } else {
            $statement->execute();
        }
        $recordSet = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $recordSet[0]['totalRecords'];
    }

} //ends class
