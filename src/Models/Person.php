<?php

namespace HumoGen\Models;

use HumoGen\Core\Model;

class Person extends Model
{
    protected static string $table = 'humo_persons';

    protected static array $fillable = [
        'id',
        'database_id',
        'tree_id',
        'pers_firstname',
        'pers_lastname',
        'pers_prefix',
        'pers_patronym',
        'pers_birth_date',
        'pers_birth_place',
        'pers_death_date',
        'pers_death_place',
        'pers_gender',
        'pers_text',
        'pers_gedcom',
    ];

    public function getFullName(): string
    {
        $parts = array_filter([
            $this->pers_firstname,
            $this->pers_patronym,
            $this->pers_prefix,
            $this->pers_lastname,
        ]);

        return implode(' ', $parts);
    }

    public function getBirthDate(): ?string
    {
        return $this->pers_birth_date;
    }

    public function getBirthPlace(): ?string
    {
        return $this->pers_birth_place;
    }

    public function getDeathDate(): ?string
    {
        return $this->pers_death_date;
    }

    public function getDeathPlace(): ?string
    {
        return $this->pers_death_place;
    }

    public function getGender(): string
    {
        return match ($this->pers_gender) {
            'M' => 'Male',
            'F' => 'Female',
            default => 'Unknown',
        };
    }
} 