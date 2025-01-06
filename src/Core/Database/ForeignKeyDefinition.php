<?php

namespace HumoGen\Core\Database;

class ForeignKeyDefinition
{
    protected string $table;
    protected string $column;
    protected string $references;
    protected string $on;
    protected string $onDelete = '';
    protected string $onUpdate = '';

    /**
     * Escape a database identifier (table or column name).
     */
    protected function escapeIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    public function __construct(string $table, string $column)
    {
        $this->table = $table;
        $this->column = $column;
    }

    /**
     * Set the referenced column.
     */
    public function references(string $column): self
    {
        $this->references = $column;
        return $this;
    }

    /**
     * Set the referenced table.
     */
    public function on(string $table): self
    {
        $this->on = $table;
        return $this;
    }

    /**
     * Set the ON DELETE action.
     */
    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }

    /**
     * Set the ON UPDATE action.
     */
    public function onUpdate(string $action): self
    {
        $this->onUpdate = strtoupper($action);
        return $this;
    }

    /**
     * Execute the foreign key definition.
     */
    public function execute($db): void
    {
        $escapedTable = $this->escapeIdentifier($this->table);
        $escapedColumn = $this->escapeIdentifier($this->column);
        $escapedReferencedTable = $this->escapeIdentifier($this->on);
        $escapedReferencedColumn = $this->escapeIdentifier($this->references);
        $escapedConstraint = $this->escapeIdentifier("fk_{$this->table}_{$this->column}");
        
        $sql = "ALTER TABLE {$escapedTable} ADD CONSTRAINT {$escapedConstraint} ";
        $sql .= "FOREIGN KEY ({$escapedColumn}) REFERENCES {$escapedReferencedTable}({$escapedReferencedColumn})";
        
        if ($this->onDelete) {
            $sql .= " ON DELETE " . $db->quote($this->onDelete);
        }
        
        if ($this->onUpdate) {
            $sql .= " ON UPDATE " . $db->quote($this->onUpdate);
        }

        $db->query($sql);
    }
} 