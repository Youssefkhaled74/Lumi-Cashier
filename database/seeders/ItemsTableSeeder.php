<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('items')->truncate();

        $items = [
            // Men's Clothing (Category 1)
            ['name' => 'Classic White Shirt', 'category_id' => 1, 'sku' => 'MEN-SHT-001', 'description' => 'Premium cotton white shirt for formal occasions', 'price' => 299.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Blue Denim Jeans', 'category_id' => 1, 'sku' => 'MEN-JNS-001', 'description' => 'Classic fit blue denim jeans', 'price' => 450.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Black Suit Jacket', 'category_id' => 1, 'sku' => 'MEN-SUT-001', 'description' => 'Elegant black suit jacket for business wear', 'price' => 850.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casual Polo Shirt', 'category_id' => 1, 'sku' => 'MEN-PLO-001', 'description' => 'Comfortable cotton polo shirt', 'price' => 199.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cotton Dress Pants', 'category_id' => 1, 'sku' => 'MEN-PNT-001', 'description' => 'Formal cotton dress pants', 'price' => 399.00, 'created_at' => now(), 'updated_at' => now()],

            // Women's Clothing (Category 2)
            ['name' => 'Floral Summer Dress', 'category_id' => 2, 'sku' => 'WOM-DRS-001', 'description' => 'Beautiful floral pattern summer dress', 'price' => 599.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Elegant Evening Gown', 'category_id' => 2, 'sku' => 'WOM-GWN-001', 'description' => 'Sophisticated evening gown for special occasions', 'price' => 1200.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Silk Blouse', 'category_id' => 2, 'sku' => 'WOM-BLS-001', 'description' => 'Premium silk blouse in multiple colors', 'price' => 399.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'High Waist Jeans', 'category_id' => 2, 'sku' => 'WOM-JNS-001', 'description' => 'Trendy high waist denim jeans', 'price' => 499.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chiffon Scarf', 'category_id' => 2, 'sku' => 'WOM-SCF-001', 'description' => 'Lightweight chiffon scarf - various colors', 'price' => 149.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maxi Skirt', 'category_id' => 2, 'sku' => 'WOM-SKT-001', 'description' => 'Elegant maxi skirt for all occasions', 'price' => 349.00, 'created_at' => now(), 'updated_at' => now()],

            // Kids Clothing (Category 3)
            ['name' => 'Kids T-Shirt Set', 'category_id' => 3, 'sku' => 'KID-TSH-001', 'description' => 'Colorful t-shirt set for children (pack of 3)', 'price' => 250.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kids Denim Overalls', 'category_id' => 3, 'sku' => 'KID-OVR-001', 'description' => 'Cute denim overalls for kids', 'price' => 350.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'School Uniform Set', 'category_id' => 3, 'sku' => 'KID-UNF-001', 'description' => 'Complete school uniform set', 'price' => 499.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kids Dress', 'category_id' => 3, 'sku' => 'KID-DRS-001', 'description' => 'Pretty dress for girls', 'price' => 299.00, 'created_at' => now(), 'updated_at' => now()],

            // Shoes & Footwear (Category 4)
            ['name' => 'Men\'s Leather Shoes', 'category_id' => 4, 'sku' => 'SHO-MLT-001', 'description' => 'Classic leather formal shoes', 'price' => 699.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Women\'s High Heels', 'category_id' => 4, 'sku' => 'SHO-HEL-001', 'description' => 'Elegant high heel shoes', 'price' => 599.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casual Sneakers', 'category_id' => 4, 'sku' => 'SHO-SNK-001', 'description' => 'Comfortable casual sneakers', 'price' => 449.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kids Sports Shoes', 'category_id' => 4, 'sku' => 'SHO-KSP-001', 'description' => 'Durable sports shoes for children', 'price' => 350.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sandals', 'category_id' => 4, 'sku' => 'SHO-SND-001', 'description' => 'Comfortable summer sandals', 'price' => 249.00, 'created_at' => now(), 'updated_at' => now()],

            // Accessories (Category 5)
            ['name' => 'Leather Handbag', 'category_id' => 5, 'sku' => 'ACC-BAG-001', 'description' => 'Premium leather handbag for women', 'price' => 899.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Men\'s Leather Wallet', 'category_id' => 5, 'sku' => 'ACC-WLT-001', 'description' => 'Genuine leather wallet with multiple compartments', 'price' => 299.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Designer Sunglasses', 'category_id' => 5, 'sku' => 'ACC-SUN-001', 'description' => 'UV protection designer sunglasses', 'price' => 399.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Leather Belt', 'category_id' => 5, 'sku' => 'ACC-BLT-001', 'description' => 'Classic leather belt for men', 'price' => 199.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fashion Watch', 'category_id' => 5, 'sku' => 'ACC-WCH-001', 'description' => 'Stylish fashion watch for everyday wear', 'price' => 549.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gold Necklace', 'category_id' => 5, 'sku' => 'ACC-NKL-001', 'description' => 'Elegant gold-plated necklace', 'price' => 799.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Silver Earrings', 'category_id' => 5, 'sku' => 'ACC-EAR-001', 'description' => 'Beautiful silver earrings', 'price' => 299.00, 'created_at' => now(), 'updated_at' => now()],

            // Sportswear (Category 6)
            ['name' => 'Gym T-Shirt', 'category_id' => 6, 'sku' => 'SPT-GYM-001', 'description' => 'Breathable athletic t-shirt', 'price' => 149.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sports Leggings', 'category_id' => 6, 'sku' => 'SPT-LEG-001', 'description' => 'Flexible sports leggings for women', 'price' => 249.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Running Shorts', 'category_id' => 6, 'sku' => 'SPT-SHR-001', 'description' => 'Lightweight running shorts', 'price' => 179.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yoga Mat Bag', 'category_id' => 6, 'sku' => 'SPT-YMB-001', 'description' => 'Convenient yoga mat carrying bag', 'price' => 199.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sports Bra', 'category_id' => 6, 'sku' => 'SPT-BRA-001', 'description' => 'Supportive sports bra for workouts', 'price' => 199.00, 'created_at' => now(), 'updated_at' => now()],

            // Winter Collection (Category 7)
            ['name' => 'Wool Coat', 'category_id' => 7, 'sku' => 'WIN-COT-001', 'description' => 'Warm wool winter coat', 'price' => 1299.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Knit Sweater', 'category_id' => 7, 'sku' => 'WIN-SWT-001', 'description' => 'Cozy knit sweater for cold weather', 'price' => 399.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Winter Jacket', 'category_id' => 7, 'sku' => 'WIN-JKT-001', 'description' => 'Insulated winter jacket with hood', 'price' => 899.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wool Scarf', 'category_id' => 7, 'sku' => 'WIN-SCF-001', 'description' => 'Soft wool scarf for winter', 'price' => 179.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Winter Gloves', 'category_id' => 7, 'sku' => 'WIN-GLV-001', 'description' => 'Warm winter gloves', 'price' => 129.00, 'created_at' => now(), 'updated_at' => now()],

            // Summer Collection (Category 8)
            ['name' => 'Linen Shirt', 'category_id' => 8, 'sku' => 'SUM-LNS-001', 'description' => 'Cool and breathable linen shirt', 'price' => 349.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beach Shorts', 'category_id' => 8, 'sku' => 'SUM-BCH-001', 'description' => 'Quick-dry beach shorts', 'price' => 199.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sun Hat', 'category_id' => 8, 'sku' => 'SUM-HAT-001', 'description' => 'Wide brim sun protection hat', 'price' => 149.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cotton Tank Top', 'category_id' => 8, 'sku' => 'SUM-TNK-001', 'description' => 'Lightweight cotton tank top', 'price' => 99.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Swimsuit', 'category_id' => 8, 'sku' => 'SUM-SWM-001', 'description' => 'Stylish swimsuit for beach', 'price' => 299.00, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('items')->insert($items);
    }
}
