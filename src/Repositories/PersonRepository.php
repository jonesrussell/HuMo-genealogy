<?php

namespace HumoGen\Repositories;

use HumoGen\Core\Repository;
use HumoGen\Models\Person;

class PersonRepository extends Repository
{
    protected string $model = Person::class;

    public function findByName(string $name): array
    {
        $sql = "SELECT * FROM {$this->model::$table} 
                WHERE CONCAT(pers_firstname, ' ', pers_lastname) LIKE ?
                OR CONCAT(pers_firstname, ' ', pers_prefix, ' ', pers_lastname) LIKE ?";
        
        $params = ["%$name%", "%$name%"];
        $stmt = $this->query($sql, $params);
        
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new $this->model($row);
        }
        
        return $results;
    }

    public function findByDateRange(string $startDate, string $endDate): array
    {
        $sql = "SELECT * FROM {$this->model::$table} 
                WHERE (pers_birth_date BETWEEN ? AND ?)
                OR (pers_death_date BETWEEN ? AND ?)";
        
        $params = [$startDate, $endDate, $startDate, $endDate];
        $stmt = $this->query($sql, $params);
        
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new $this->model($row);
        }
        
        return $results;
    }

    public function findByPlace(string $place): array
    {
        $sql = "SELECT * FROM {$this->model::$table} 
                WHERE pers_birth_place LIKE ?
                OR pers_death_place LIKE ?";
        
        $params = ["%$place%", "%$place%"];
        $stmt = $this->query($sql, $params);
        
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new $this->model($row);
        }
        
        return $results;
    }

    public function searchPaginated(
        ?string $name = null,
        ?string $place = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $page = 1,
        int $perPage = 20
    ): array {
        $sql = "SELECT * FROM {$this->model::$table} WHERE 1=1";
        $params = [];

        if ($name) {
            $sql .= " AND (CONCAT(pers_firstname, ' ', pers_lastname) LIKE ?
                    OR CONCAT(pers_firstname, ' ', pers_prefix, ' ', pers_lastname) LIKE ?)";
            $params[] = "%$name%";
            $params[] = "%$name%";
        }

        if ($place) {
            $sql .= " AND (pers_birth_place LIKE ? OR pers_death_place LIKE ?)";
            $params[] = "%$place%";
            $params[] = "%$place%";
        }

        if ($startDate && $endDate) {
            $sql .= " AND ((pers_birth_date BETWEEN ? AND ?)
                    OR (pers_death_date BETWEEN ? AND ?))";
            $params[] = $startDate;
            $params[] = $endDate;
            $params[] = $startDate;
            $params[] = $endDate;
        }

        $sql .= " ORDER BY pers_lastname, pers_firstname";

        return $this->paginate($sql, $params, $page, $perPage);
    }
} 