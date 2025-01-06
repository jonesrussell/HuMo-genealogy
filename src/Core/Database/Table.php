<?php

namespace HumoGen\Core\Database;

class Table
{
    protected string $table;
    protected bool $exists;
    protected array $commands = [];
    public string $charset = '';
    public string $collation = '';

    /**
     * Escape a database identifier (table or column name).
     */
    protected function escapeIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    public function __construct(string $table, bool $exists = false)
    {
        $this->table = $table;
        $this->exists = $exists;
    }

    /**
     * Add a foreign key constraint.
     */
    public function foreign(string $column): ForeignKeyDefinition
    {
        $command = new ForeignKeyDefinition($this->table, $column);
        $this->commands[] = $command;
        return $command;
    }

    /**
     * Drop a foreign key constraint.
     */
    public function dropForeign(array $columns): void
    {
        foreach ($columns as $column) {
            $this->commands[] = [
                'type' => 'dropForeign',
                'column' => $column,
                'table' => $this->table
            ];
        }
    }

    /**
     * Drop the table.
     */
    public function drop(): void
    {
        $this->commands[] = [
            'type' => 'dropTable',
            'table' => $this->table
        ];
    }

    /**
     * Build and execute the schema changes.
     */
    public function build(): void
    {
        $db = app()->getService('db');
        
        foreach ($this->commands as $command) {
            if ($command instanceof ForeignKeyDefinition) {
                $command->execute($db);
            } else {
                $this->executeCommand($command, $db);
            }
        }

        // Update charset/collation if set
        if ($this->charset || $this->collation) {
            $escapedTable = $this->escapeIdentifier($this->table);
            $sql = "ALTER TABLE {$escapedTable}";
            if ($this->charset) {
                $sql .= " CHARACTER SET = " . $db->quote($this->charset);
            }
            if ($this->collation) {
                $sql .= " COLLATE = " . $db->quote($this->collation);
            }
            $db->query($sql);
        }
    }

    /**
     * Execute a schema command.
     */
    protected function executeCommand(array $command, $db): void
    {
        switch ($command['type']) {
            case 'dropForeign':
                $escapedTable = $this->escapeIdentifier($command['table']);
                $escapedConstraint = $this->escapeIdentifier("fk_{$command['table']}_{$command['column']}");
                $sql = "ALTER TABLE {$escapedTable} DROP FOREIGN KEY {$escapedConstraint}";
                $db->query($sql);
                break;
            case 'dropTable':
                $escapedTable = $this->escapeIdentifier($command['table']);
                $sql = "DROP TABLE IF EXISTS {$escapedTable}";
                $db->query($sql);
                break;
        }
    }
} 