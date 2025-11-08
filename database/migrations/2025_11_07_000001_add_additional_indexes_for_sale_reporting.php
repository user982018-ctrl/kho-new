<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->addSaleCareIndexes();
        $this->addSaleCareHistoryIndexes();
        $this->addOrdersIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropSaleCareIndexes();
        $this->dropSaleCareHistoryIndexes();
        $this->dropOrdersIndexes();
    }

    private function addSaleCareIndexes(): void
    {
        Schema::table('sale_care', function (Blueprint $table) {
            if (!$this->indexExists('sale_care', 'idx_sale_care_result_assign')) {
                $table->index(['result_call', 'assign_user'], 'idx_sale_care_result_assign');
            }

            if (!$this->indexExists('sale_care', 'idx_sale_care_group_assign_created')) {
                $table->index(['group_id', 'assign_user', 'created_at'], 'idx_sale_care_group_assign_created');
            }
        });
    }

    private function dropSaleCareIndexes(): void
    {
        Schema::table('sale_care', function (Blueprint $table) {
            if ($this->indexExists('sale_care', 'idx_sale_care_result_assign')) {
                $table->dropIndex('idx_sale_care_result_assign');
            }

            if ($this->indexExists('sale_care', 'idx_sale_care_group_assign_created')) {
                $table->dropIndex('idx_sale_care_group_assign_created');
            }
        });
    }

    private function addSaleCareHistoryIndexes(): void
    {
        Schema::table('sale_care_history_tn', function (Blueprint $table) {
            if (!$this->indexExists('sale_care_history_tn', 'idx_sc_history_sale')) {
                $table->index('sale_id', 'idx_sc_history_sale');
            }

            if (!$this->indexExists('sale_care_history_tn', 'idx_sc_history_created')) {
                $table->index('created_at', 'idx_sc_history_created');
            }

            if (!$this->indexExists('sale_care_history_tn', 'idx_sc_history_sale_created')) {
                $table->index(['sale_id', 'created_at'], 'idx_sc_history_sale_created');
            }
        });
    }

    private function dropSaleCareHistoryIndexes(): void
    {
        Schema::table('sale_care_history_tn', function (Blueprint $table) {
            if ($this->indexExists('sale_care_history_tn', 'idx_sc_history_sale_created')) {
                $table->dropIndex('idx_sc_history_sale_created');
            }

            if ($this->indexExists('sale_care_history_tn', 'idx_sc_history_created')) {
                $table->dropIndex('idx_sc_history_created');
            }

            if ($this->indexExists('sale_care_history_tn', 'idx_sc_history_sale')) {
                $table->dropIndex('idx_sc_history_sale');
            }
        });
    }

    private function addOrdersIndexes(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->indexExists('orders', 'idx_orders_assign_user')) {
                $table->index('assign_user', 'idx_orders_assign_user');
            }

            if (!$this->indexExists('orders', 'idx_orders_sale_created')) {
                $table->index(['sale_care', 'created_at'], 'idx_orders_sale_created');
            }
        });
    }

    private function dropOrdersIndexes(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if ($this->indexExists('orders', 'idx_orders_sale_created')) {
                $table->dropIndex('idx_orders_sale_created');
            }

            if ($this->indexExists('orders', 'idx_orders_assign_user')) {
                $table->dropIndex('idx_orders_assign_user');
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $database = DB::connection()->getDatabaseName();

        $result = DB::select(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $index]
        );

        return isset($result[0]) && (int) $result[0]->aggregate > 0;
    }
};

