<?php
class CrudPDO {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        try {
            // Create a PDO instance for database connection
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

            // Set PDO to throw exceptions on errors
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function create($table, $data) {
        try {
            // Build the SQL INSERT query
            $fields = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));
            $sql = "INSERT INTO $table ($fields) VALUES ($values)";

            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters and execute the statement
            $stmt->execute($data);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function read($table) {
        try {
            // Build the SQL SELECT query
            $sql = "SELECT * FROM $table";

            // Execute the query and fetch all records
            $stmt = $this->pdo->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $e) {
            return [];
        }
    }
	
	public function readCustom($sql, $params = []) {
        try {
            // Prepare the custom SQL query
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters and execute the statement
            $stmt->execute($params);

            // Fetch all records
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function update($table, $id, $data) {
        try {
            // Build the SQL UPDATE query
            $set = implode(', ', array_map(function ($key) {
                return "$key = :$key";
            }, array_keys($data)));
            $sql = "UPDATE $table SET $set WHERE id = :id";

            // Add the id to the data array
            $data['id'] = $id;

            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters and execute the statement
            $stmt->execute($data);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($table, $id) {
        try {
            // Build the SQL DELETE query
            $sql = "DELETE FROM $table WHERE id = :id";

            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters and execute the statement
            $stmt->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>