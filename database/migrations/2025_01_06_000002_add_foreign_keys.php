<?php

use HumoGen\Core\Database\Migration;
use HumoGen\Core\Database\Schema;
use HumoGen\Core\Database\Table;

class AddForeignKeys extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::table('humo_persons', function (Table $table) {
            $table->foreign('pers_tree_id')
                ->references('tree_id')
                ->on('humo_trees')
                ->onDelete('cascade');
        });

        Schema::table('humo_families', function (Table $table) {
            $table->foreign('fam_tree_id')
                ->references('tree_id')
                ->on('humo_trees')
                ->onDelete('cascade');

            // Optional: Add relationship constraints if data is clean
            // $table->foreign('fam_man')->references('pers_gedcomnumber')->on('humo_persons');
            // $table->foreign('fam_woman')->references('pers_gedcomnumber')->on('humo_persons');
        });

        Schema::table('humo_events', function (Table $table) {
            $table->foreign('event_tree_id')
                ->references('tree_id')
                ->on('humo_trees')
                ->onDelete('cascade');
        });

        Schema::table('humo_addresses', function (Table $table) {
            $table->foreign('address_tree_id')
                ->references('tree_id')
                ->on('humo_trees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('humo_persons', function (Table $table) {
            $table->dropForeign(['pers_tree_id']);
        });

        Schema::table('humo_families', function (Table $table) {
            $table->dropForeign(['fam_tree_id']);
            // $table->dropForeign(['fam_man']);
            // $table->dropForeign(['fam_woman']);
        });

        Schema::table('humo_events', function (Table $table) {
            $table->dropForeign(['event_tree_id']);
        });

        Schema::table('humo_addresses', function (Table $table) {
            $table->dropForeign(['address_tree_id']);
        });
    }
} 