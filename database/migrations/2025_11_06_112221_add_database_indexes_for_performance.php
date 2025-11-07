<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cache để lưu trữ index đã load
     */
    private static array $indexCache = [];

    /**
     * Run the migrations.
     * Thêm index để tối ưu hiệu suất truy vấn database
     */
    public function up(): void
    {
        // Batch load tất cả index của các table một lần để tối ưu
        $this->loadTableIndexes(['sale_care', 'orders', 'shipping_order', 'src_page']);
        // Index cho bảng sale_care
        Schema::table('sale_care', function (Blueprint $table) {
            // Index đơn cho các cột thường dùng trong WHERE
            if (!$this->indexExists('sale_care', 'idx_sale_care_phone')) {
                $table->index('phone', 'idx_sale_care_phone');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_src_id')) {
                $table->index('src_id', 'idx_sale_care_src_id');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_group_id')) {
                $table->index('group_id', 'idx_sale_care_group_id');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_assign_user')) {
                $table->index('assign_user', 'idx_sale_care_assign_user');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_old_customer')) {
                $table->index('old_customer', 'idx_sale_care_old_customer');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_created_at')) {
                $table->index('created_at', 'idx_sale_care_created_at');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_type_tn')) {
                $table->index('type_TN', 'idx_sale_care_type_tn');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_has_tn')) {
                $table->index('has_TN', 'idx_sale_care_has_tn');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_result_call')) {
                $table->index('result_call', 'idx_sale_care_result_call');
            }
            if (!$this->indexExists('sale_care', 'idx_sale_care_id_order_new')) {
                $table->index('id_order_new', 'idx_sale_care_id_order_new');
            }
            // full_name có thể là TEXT/VARCHAR dài, sẽ tạo bằng DB::statement với key length
        });

        // Composite indexes cho sale_care (tối ưu cho truy vấn phức tạp)
        if (!$this->indexExists('sale_care', 'idx_sale_care_src_created')) {
            DB::statement('CREATE INDEX idx_sale_care_src_created ON sale_care (created_at, src_id, id)');
        }
        if (!$this->indexExists('sale_care', 'idx_sale_care_old_customer_created')) {
            DB::statement('CREATE INDEX idx_sale_care_old_customer_created ON sale_care (old_customer, created_at)');
        }
        if (!$this->indexExists('sale_care', 'idx_sale_care_assign_created')) {
            DB::statement('CREATE INDEX idx_sale_care_assign_created ON sale_care (assign_user, created_at)');
        }
        if (!$this->indexExists('sale_care', 'idx_sale_care_group_created')) {
            DB::statement('CREATE INDEX idx_sale_care_group_created ON sale_care (group_id, created_at)');
        }

        // Index cho bảng orders
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->indexExists('orders', 'idx_orders_sale_care')) {
                $table->index('sale_care', 'idx_orders_sale_care');
            }
            if (!$this->indexExists('orders', 'idx_orders_status')) {
                $table->index('status', 'idx_orders_status');
            }
            if (!$this->indexExists('orders', 'idx_orders_created_at')) {
                $table->index('created_at', 'idx_orders_created_at');
            }
        });

        // Composite index cho orders
        if (!$this->indexExists('orders', 'idx_orders_sale_care_composite')) {
            DB::statement('CREATE INDEX idx_orders_sale_care_composite ON orders (sale_care, id, total, qty)');
        }
        if (!$this->indexExists('orders', 'idx_orders_status_created')) {
            DB::statement('CREATE INDEX idx_orders_status_created ON orders (status, created_at)');
        }

        // Index cho bảng shipping_order
        Schema::table('shipping_order', function (Blueprint $table) {
            if (!$this->indexExists('shipping_order', 'idx_shipping_order_id')) {
                $table->index('order_id', 'idx_shipping_order_id');
            }
            if (!$this->indexExists('shipping_order', 'idx_shipping_order_vendor')) {
                $table->index('vendor_ship', 'idx_shipping_order_vendor');
            }
            if (!$this->indexExists('shipping_order', 'idx_shipping_order_print_status')) {
                $table->index('print_status', 'idx_shipping_order_print_status');
            }
            if (!$this->indexExists('shipping_order', 'idx_shipping_order_check_cron')) {
                $table->index('check_cron', 'idx_shipping_order_check_cron');
            }
            if (!$this->indexExists('shipping_order', 'idx_shipping_order_code')) {
                $table->index('order_code', 'idx_shipping_order_code');
            }
        });

        // Composite index cho shipping_order
        if (!$this->indexExists('shipping_order', 'idx_shipping_order_composite')) {
            DB::statement('CREATE INDEX idx_shipping_order_composite ON shipping_order (order_id, vendor_ship, print_status)');
        }
        if (!$this->indexExists('shipping_order', 'idx_shipping_order_vendor_status')) {
            DB::statement('CREATE INDEX idx_shipping_order_vendor_status ON shipping_order (vendor_ship, print_status, check_cron)');
        }

        // Index cho bảng src_page
        Schema::table('src_page', function (Blueprint $table) {
            if (!$this->indexExists('src_page', 'idx_src_page_user_digital')) {
                $table->index('user_digital', 'idx_src_page_user_digital');
            }
            if (!$this->indexExists('src_page', 'idx_src_page_status')) {
                $table->index('status', 'idx_src_page_status');
            }
            // type có thể là TEXT/VARCHAR dài, sẽ tạo bằng DB::statement với key length
        });

        // Index cho id_page và type với key length (vì có thể là TEXT/VARCHAR dài)
        if (!$this->indexExists('src_page', 'idx_src_page_id_page')) {
            DB::statement('CREATE INDEX idx_src_page_id_page ON src_page (id_page(191))');
        }
        if (!$this->indexExists('src_page', 'idx_src_page_type')) {
            DB::statement('CREATE INDEX idx_src_page_type ON src_page (type(191))');
        }

        // Index cho page_id và full_name với key length (vì có thể là TEXT/VARCHAR dài)
        if (!$this->indexExists('sale_care', 'idx_sale_care_page_id')) {
            DB::statement('CREATE INDEX idx_sale_care_page_id ON sale_care (page_id(191))');
        }
        if (!$this->indexExists('sale_care', 'idx_sale_care_full_name')) {
            DB::statement('CREATE INDEX idx_sale_care_full_name ON sale_care (full_name(191))');
        }

        // Composite index cho src_page (chỉ index user_digital và status vì type có thể quá dài)
        if (!$this->indexExists('src_page', 'idx_src_page_user_status')) {
            DB::statement('CREATE INDEX idx_src_page_user_status ON src_page (user_digital, status)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa index cho sale_care
        Schema::table('sale_care', function (Blueprint $table) {
            $table->dropIndex('idx_sale_care_phone');
            $table->dropIndex('idx_sale_care_src_id');
            $table->dropIndex('idx_sale_care_group_id');
            $table->dropIndex('idx_sale_care_assign_user');
            $table->dropIndex('idx_sale_care_old_customer');
            $table->dropIndex('idx_sale_care_created_at');
            $table->dropIndex('idx_sale_care_type_tn');
            $table->dropIndex('idx_sale_care_has_tn');
            $table->dropIndex('idx_sale_care_result_call');
            $table->dropIndex('idx_sale_care_id_order_new');
        });

        DB::statement('DROP INDEX IF EXISTS idx_sale_care_src_created ON sale_care');
        DB::statement('DROP INDEX IF EXISTS idx_sale_care_old_customer_created ON sale_care');
        DB::statement('DROP INDEX IF EXISTS idx_sale_care_assign_created ON sale_care');
        DB::statement('DROP INDEX IF EXISTS idx_sale_care_group_created ON sale_care');

        // Xóa index cho orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_sale_care');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
        });

        DB::statement('DROP INDEX IF EXISTS idx_orders_sale_care_composite ON orders');
        DB::statement('DROP INDEX IF EXISTS idx_orders_status_created ON orders');

        // Xóa index cho shipping_order
        Schema::table('shipping_order', function (Blueprint $table) {
            $table->dropIndex('idx_shipping_order_id');
            $table->dropIndex('idx_shipping_order_vendor');
            $table->dropIndex('idx_shipping_order_print_status');
            $table->dropIndex('idx_shipping_order_check_cron');
            $table->dropIndex('idx_shipping_order_code');
        });

        DB::statement('DROP INDEX IF EXISTS idx_shipping_order_composite ON shipping_order');
        DB::statement('DROP INDEX IF EXISTS idx_shipping_order_vendor_status ON shipping_order');

        // Xóa index cho src_page
        Schema::table('src_page', function (Blueprint $table) {
            $table->dropIndex('idx_src_page_user_digital');
            $table->dropIndex('idx_src_page_status');
        });

        DB::statement('DROP INDEX IF EXISTS idx_sale_care_page_id ON sale_care');
        DB::statement('DROP INDEX IF EXISTS idx_sale_care_full_name ON sale_care');
        DB::statement('DROP INDEX IF EXISTS idx_src_page_id_page ON src_page');
        DB::statement('DROP INDEX IF EXISTS idx_src_page_type ON src_page');
        DB::statement('DROP INDEX IF EXISTS idx_src_page_user_status ON src_page');
    }

    /**
     * Kiểm tra xem index đã tồn tại chưa
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $database = DB::connection()->getDatabaseName();
            $result = DB::select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$database, $table, $indexName]
            );
            
            return isset($result[0]) && $result[0]->count > 0;
        } catch (\Exception $e) {
            // Nếu có lỗi, giả sử index chưa tồn tại
            return false;
        }
    }
};
