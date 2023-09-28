<?php

namespace Plugse\Dynf;

use Exception;
use PDO;

class Model
{
    private PDO $connection;
    private $tableName;

    public function __construct()
    {
        $dsn = 'mysql:host=localhost;dbname=teste';
        $this->connection = new PDO($dsn, 'root', '');
        $this->tableName = 'user';
    }

    public function __call($name, $arguments)
    {
        $functionStart = 'findBy';
        $unionFields = 'And';

        $findBy = str_starts_with($name, $functionStart);

        if (!$findBy) {
            throw new Exception("The function name must starts with {$functionStart}");
        }

        $fields = substr($name, strlen($functionStart));
        $fields = array_map(
            function ($field) {
                return lcfirst($field);
            },
            explode($unionFields, $fields)
        );

        $values = array_combine($fields, $arguments);
        if (!$values) {
            throw new Exception('The number of fields and values must be the same');
        }

        return $this->find('*', $values);
    }

    public function find(string $fields = '*', array $whereClauses = []): array
    {
        $query = "SELECT {$fields} FROM {$this->tableName} {$this->getWhere($whereClauses)}";

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($whereClauses);

            return $stmt->fetchAll(PDO::FETCH_CLASS);

            return $stmt->fetchObject();
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    private function getWhere(array $whereClauses): string
    {
        if (empty($whereClauses)) {
            return 'WHERE 1';
        }

        $clauses = [];
        foreach (array_keys($whereClauses) as $field) {
            array_push($clauses, "{$field}=:{$field}");
        }

        return 'WHERE ' . implode(' AND ', $clauses);
    }
}
