CREATE INDEX idx_sale_care_src_created ON sale_care (created_at, src_id, id);
CREATE INDEX idx_orders_sale_care ON orders (sale_care, id, total, qty);
ANALYZE TABLE sale_care;
ANALYZE TABLE orders;
OPTIMIZE TABLE sale_care;
OPTIMIZE TABLE orders;



        $sql = 
            "SELECT 
                src.name,
                COUNT( sc.id) AS contact,
                COUNT( o.id) AS count_order,
                ROUND(COUNT(DISTINCT o.id) * 100.0 / NULLIF(COUNT(DISTINCT sc.id), 0), 2) AS rate,
                SUM(o.total) AS total,
                SUM(o.qty) AS product,
                ROUND(SUM(o.total) / NULLIF(COUNT(DISTINCT o.id), 0), 2) AS avg
            FROM src_page src
            JOIN sale_care sc on sc.src_id = src.id
            LEFT JOIN orders o on o.sale_care = sc.id
            $str
            GROUP BY src.name
            ORDER BY total DESC";