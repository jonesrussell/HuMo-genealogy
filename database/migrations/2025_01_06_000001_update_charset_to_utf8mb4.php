<?php

use HumoGen\Core\Database\Migration;
use HumoGen\Core\Database\Schema;
use HumoGen\Core\Database\Table;

class UpdateCharsetToUtf8mb4 extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        $tables = [
            'humo_persons',
            'humo_families',
            'humo_events',
            'humo_addresses',
            'humo_sources',
            'humo_repositories',
            'humo_trees',
            'humo_users',
            'humo_groups'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Table $table) {
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        $tables = [
            'humo_persons',
            'humo_families',
            'humo_events',
            'humo_addresses',
            'humo_sources',
            'humo_repositories',
            'humo_trees',
            'humo_users',
            'humo_groups'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Table $table) {
                $table->charset = 'utf8mb3';
                $table->collation = 'utf8mb3_uca1400_ai_ci';
            });
        }
    }
} 