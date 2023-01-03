<?php

namespace App\Core;

use PDO;

/**
 * @phpstan-consistent-constructor
 */
class Database extends Singleton
{
    public const PDO_FETCH_MULTI = 'multi';
    public const PDO_FETCH_SINGLE = 'single';
    public const PDO_FETCH_VALUE = 'value';
    public const PDO_EXEC = 'exec';

    private PDO $pdo;

    private array $executeParams = [];
    private string $fetchType;
    private string $sql;
    private string $tableName;
    private array $where = [];

    protected function __construct()
    {
        parent::__construct();
        $config = Config::getInstance();

        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $dsn = 'mysql:host=' . $config->host . ';dbname=' . $config->dbname . ';charset=utf8';
        $this->pdo = new PDO($dsn, $config->username, $config->password, $opt);
    }

    /**
     * Initialize class variables.
     *
     * Set class variables to defaults, so they are ready for a new query to be setup and ran.
     *
     * @return void
     */
    private function initialize(): void
    {
        $this->sql = '';
        $this->tableName = '';
        $this->executeParams = [];
        $this->where = [];
        $this->fetchType = PDO::FETCH_ASSOC;
    }

    /**
     * Set table name.
     *
     * Set the tableName class var.
     *
     * @param string $tableName Database table to use with the query.
     * @return self
     */
    public function table(string $tableName): self
    {
        $this->tableName = $this->escapeMysqlIdentifier($tableName);
        return $this;
    }

    /**
     * Set fetch.
     *
     * Set the fetch type the pdo result with use.
     *
     * @param string $fetchType Accepts 'multi', or 'single'.
     * @return self
     */
    public function fetch(string $fetchType): self
    {
        $this->fetchType = $fetchType;

        return $this;
    }

    /**
     * Build the where part of sql.
     *
     * Loop over and add the where column/values to the sql query.
     *
     * @return void
     */
    private function buildWhere(): void
    {
        if ($this->where) {
            $this->sql .= ' WHERE';

            foreach ($this->where as $where) {
                $columnVariableName = ':' . str_replace('.', '', $where['column']);
                $escapedColumnName = $this->escapeMysqlIdentifier($where['column']);
                $this->sql .= ' ' . $where['type'] . ' ' . $escapedColumnName . ' = ' . $columnVariableName;
                $this->executeParams[$columnVariableName] = $where['value'];
            }
        }
    }

    private function escapeMysqlIdentifier($field): string
    {
        return "`" . str_replace("`", "``", $field) . "`";
    }

    private function prepareSelect(array|string $columns): string
    {
        if (is_array($columns)) {
            return implode(', ', array_map(fn($column) => $this->escapeMysqlIdentifier($column), $columns));
        }

        return implode(', ', array_map(
            fn($column) => $column === '*' ? $column : $this->escapeMysqlIdentifier($column),
            explode(',', $columns)
        ));
    }

    /**
     * Run a query.
     *
     * Run the query on the database and return the result.
     *
     * @return array|int $result Data from the database query.
     */
    private function runQuery(array $params = []): array|int
    {
        $stmt = $this->pdo->prepare($this->sql);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($params as $param => $value) {
            //todo: need to use different types
            $stmt->bindParam(':' . $param, $value);
        }

        $stmt->execute(array_merge($params, $this->executeParams));

        $result = match ($this->fetchType) {
            self::PDO_FETCH_SINGLE => $stmt->fetch(),
            self::PDO_FETCH_MULTI => $stmt->fetchAll(),
            self::PDO_FETCH_VALUE => $stmt->fetchColumn(),
            self::PDO_EXEC => $stmt->rowCount(),
            default => $this->pdo->lastInsertId(),
        };

        $this->initialize();

        return $result;
    }

    /**
     * Set where.
     *
     * Set a where clause for use when running the query.
     *
     * @param string $column Name of the column in the where clause.
     * @param string $value Value for the column in the where clause.
     * @param string $type Type of where clause. Accepts 'AND', or 'OR'. Default empty;
     * @return self
     */
    public function where(string $column, mixed $value, string $type = ''): self
    {
        $this->where[] = [
            'column' => $column,
            'value' => $value,
            'type' => $type,
        ];

        return $this;
    }

    /**
     * Run select query.
     *
     * Run a select query on the database.
     *
     * @param array|string $columns Column names to select.
     * @return array|int Data returned from the database.
     */
    public function runSelectQuery(array|string $columns = '*'): array|int
    {
        $this->sql = "SELECT {$this->prepareSelect($columns)} FROM $this->tableName";

        $this->buildWhere();

        return $this->runQuery();
    }

    /**
     * Run update query.
     *
     * Run an update query on the database.
     *
     * @param array $data
     * @return int the number of rows that were modified
     */
    public function runUpdateQuery(array $data): int
    {
        $this->sql = "UPDATE $this->tableName SET ";
        foreach (array_keys($data) as $i => $field) {
            $escField = $this->escapeMysqlIdentifier($field);
            $this->sql .= ($i) ? ", " : "";
            $this->sql .= "$escField = :$field";
        }
        $this->fetch(self::PDO_EXEC);
        $this->buildWhere();

        return $this->runQuery($data);
    }

    public function runSelectForUpdateQuery(array|string $columns = '*'): array|int
    {
        $this->sql = "SELECT {$this->prepareSelect($columns)} FROM $this->tableName ";

        $this->buildWhere();

        $this->sql .= ' FOR UPDATE';

        return $this->runQuery();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
}
