<?php

namespace App\Services\Database;

use App\Services\Config\Config;
use App\Services\Pagination\Pagination;
use PDO;
use PDOException;

class DB
{
    private static $instance = null;

    private $connection;

    private $host;
    
    private $user;
    
    private $pass;
    
    private $dbName;
    
    private $options = [];

    public function __construct()
    {
        $this->host     = Config::get('database.host');
        $this->user     = Config::get('database.user');
        $this->pass     = Config::get('database.pass');
        $this->dbName   = Config::get('database.db_name');
        $this->options  = Config::get('database.options');
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbName}",
                $this->user,
                $this->pass,
                $this->options
            );
        } catch (PDOException $e) {
            die("Connection Failed: ".$e->getMessage());
        }
    }

    /**
     * Implement singleton pattern for DB
     *
     * @return static
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function beginTransaction()
    {
        self::getInstance()->connection->beginTransaction();
    }

    public function rollback()
    {
        self::getInstance()->connection->rollBack();
    }

    public function commit()
    {
        self::getInstance()->connection->commit();
    }

    /**
     * Find the first matched result from the given value
     *
     * @param $table
     * @param $column
     * @param $value
     * @return mixed
     */
    public function findByColumnValue($table, $column, $value)
    {
        $sql = "SELECT * FROM {$table} WHERE {$column} = :{$column} LIMIT 1";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$column => $value]);

        return $stmt->fetch();
    }

    public function insert($table, array $data): int
    {
        $columnsString = join(", ", array_keys($data));
        $columnValues = collect($data)->map(function ($value, $key) {
            return ":{$key}";
        })->values()->join(", ");

        $sql = "INSERT INTO {$table} ({$columnsString}) VALUES ({$columnValues})";

        $this->connection->prepare($sql)->execute($data);

        return $this->connection->lastInsertId();
    }

    public function update(string $table, int $id, array $data)
    {
        $updateString = collect($data)->map(function ($value, $key) {
            return "{$key} = :{$key}";
        })->join(', ');

        $sql = "UPDATE {$table} SET {$updateString} WHERE id = :id";

        $this->connection->prepare($sql)->execute(
            array_merge($data, ['id' => $id])
        );
    }

    public function paginate(string $table, $page, $perPage, $orderBy): Pagination
    {
        $offset = ($page - 1) * $perPage;
        $order = '';
        $totalRecordsSql = "SELECT COUNT(*) FROM {$table}";

        $stmt = $this->connection->prepare($totalRecordsSql);
        $stmt->execute();

        $totalRecords = $stmt->fetchColumn();
        $totalPages = ceil($totalRecords / $perPage);
        
        if (count($orderBy) > 0){
            $order = 'ORDER BY ' ;
            foreach ($orderBy as $key => $value) {
                $order .= $key . ' ' .   $value . ', ';
            }
            $order = rtrim($order, ", ");
        }
        $sql = "SELECT * from {$table} {$order} LIMIT {$offset}, $perPage";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll();

        $hasResults = count($results) > 0;

        return (new Pagination(
            $totalRecords,
            $perPage,
            $page,
            $totalPages,
            $hasResults ? $offset + 1 : 0,
            $hasResults ? $offset + count($results) : 0,
            $results
        ));
    }

    public function deleteByColumnValue(string $table, string $column, $value)
    {
        $sql = "DELETE FROM {$table} WHERE {$column} = :{$column}";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam($column, $value);
        $stmt->execute();
    }

    /**
     * Find all matched result from the given value
     *
     * @param $table
     * @param $data
     * @return mixed
     */
    public function findAllByColumnValue($table, $data)
    {
        $where = '';
        foreach ($data as $key => $value) {
            $where .= " $key = :$key";
        }
        $sql = "SELECT * FROM {$table} WHERE {$where}";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchAll();
    }
}