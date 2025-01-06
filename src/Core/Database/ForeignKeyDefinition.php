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
        $constraintName = "fk_{$this->table}_{$this->column}";
        
        $sql = "ALTER TABLE {$this->table} ADD CONSTRAINT {$constraintName} ";
        $sql .= "FOREIGN KEY ({$this->column}) REFERENCES {$this->on}({$this->references})";
        
        if ($this->onDelete) {
            $sql .= " ON DELETE {$this->onDelete}";
        }
        
        if ($this->onUpdate) {
            $sql .= " ON UPDATE {$this->onUpdate}";
        }

        $db->query($sql);
    }
} 