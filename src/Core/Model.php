<?php

namespace HumoGen\Core;

abstract class Model
{
    protected static string $table;
    protected static array $fillable = [];
    protected array $attributes = [];
    protected array $original = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->original = $this->attributes;
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, static::$fillable, true)) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        if (in_array($name, static::$fillable, true)) {
            $this->attributes[$name] = $value;
        }
    }

    public function save(): bool
    {
        $db = Application::getInstance()->getService('db');
        
        if (empty($this->attributes['id'])) {
            return $this->insert($db);
        }

        return $this->update($db);
    }

    protected function insert(\HumoGen\Core\Database $db): bool
    {
        $fields = array_keys($this->attributes);
        $values = array_values($this->attributes);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::$table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        $stmt = $db->query($sql, $values);
        
        if ($stmt->rowCount() > 0) {
            $this->attributes['id'] = $db->getConnection()->lastInsertId();
            $this->original = $this->attributes;
            return true;
        }

        return false;
    }

    protected function update(\HumoGen\Core\Database $db): bool
    {
        $fields = [];
        $values = [];

        foreach ($this->attributes as $field => $value) {
            if ($field !== 'id' && $value !== ($this->original[$field] ?? null)) {
                $fields[] = "$field = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) {
            return true;
        }

        $values[] = $this->attributes['id'];
        
        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = ?',
            static::$table,
            implode(', ', $fields)
        );

        $stmt = $db->query($sql, $values);
        
        if ($stmt->rowCount() > 0) {
            $this->original = $this->attributes;
            return true;
        }

        return false;
    }

    public static function find(int $id): ?static
    {
        $db = Application::getInstance()->getService('db');
        
        $sql = sprintf('SELECT * FROM %s WHERE id = ? LIMIT 1', static::$table);
        $stmt = $db->query($sql, [$id]);
        
        if ($row = $stmt->fetch()) {
            return new static($row);
        }

        return null;
    }

    public static function all(): array
    {
        $db = Application::getInstance()->getService('db');
        
        $sql = sprintf('SELECT * FROM %s', static::$table);
        $stmt = $db->query($sql);
        
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new static($row);
        }

        return $results;
    }

    public function delete(): bool
    {
        if (empty($this->attributes['id'])) {
            return false;
        }

        $db = Application::getInstance()->getService('db');
        
        $sql = sprintf('DELETE FROM %s WHERE id = ?', static::$table);
        $stmt = $db->query($sql, [$this->attributes['id']]);
        
        return $stmt->rowCount() > 0;
    }
} 