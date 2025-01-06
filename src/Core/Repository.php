<?php

namespace HumoGen\Core;

abstract class Repository
{
    protected Database $db;
    protected string $model;

    public function __construct()
    {
        $this->db = Application::getInstance()->getService('db');
    }

    public function find(int $id): ?object
    {
        return $this->model::find($id);
    }

    public function all(): array
    {
        return $this->model::all();
    }

    public function create(array $data): object
    {
        $model = new $this->model($data);
        $model->save();
        return $model;
    }

    public function update(int $id, array $data): ?object
    {
        $model = $this->find($id);
        if ($model) {
            $model->fill($data);
            $model->save();
        }
        return $model;
    }

    public function delete(int $id): bool
    {
        $model = $this->find($id);
        if ($model) {
            return $model->delete();
        }
        return false;
    }

    protected function query(string $sql, array $params = []): \PDOStatement
    {
        return $this->db->query($sql, $params);
    }

    protected function paginate(string $sql, array $params = [], int $page = 1, int $perPage = 20): array
    {
        $countSql = preg_replace('/SELECT .* FROM/', 'SELECT COUNT(*) as count FROM', $sql, 1);
        $countSql = preg_replace('/ORDER BY .* (DESC|ASC)/i', '', $countSql);
        
        $stmt = $this->query($countSql, $params);
        $total = (int) $stmt->fetch()['count'];
        
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT $perPage OFFSET $offset";
        
        $stmt = $this->query($sql, $params);
        $items = [];
        
        while ($row = $stmt->fetch()) {
            $items[] = new $this->model($row);
        }
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }
} 