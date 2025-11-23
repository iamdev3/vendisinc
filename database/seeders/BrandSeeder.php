<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Commenting out factory usage for now
        // Brand::factory()->count(5)->create();

        // Adding manual data for 5 records
        $brands = Brand::insert([
            [
                'id' => 1,
                'name' => '{"en":"Bhargav Snacks"}',
                'slug' => 'bhargav-snacks',
                'logo' => 'brands/01K15FYWNYZZ4EA8MEWGVM83ZY.jpeg',
                'address' => '{"en":"GIDC Navsari."}',
                'city' => '{"en":"Navsari"}',
                'pincode' => '1396445',
                'phone' => '840141248',
                'email' => 'bhargavsnacks@gmail.com',
                'website' => 'https://www.bhargavsnacks.com',
                'additional_info' => '{"en":null}',
                'description' => '{"en":null}',
                'is_active' => 1,
                'created_at' => '2025-07-05 14:21:50',
                'updated_at' => '2025-11-23 07:07:00'
            ],
            [
                'id' => 2,
                'name' => '{"en":"Tasty Treats"}',
                'slug' => 'tasty-treats',
                'logo' => 'brands/02K15FYWNYZZ4EA8MEWGVM83ZY.jpeg',
                'address' => '{"en":"Main Street 123"}',
                'city' => '{"en":"Surat"}',
                'pincode' => '1396446',
                'phone' => '840141249',
                'email' => 'tastytreats@gmail.com',
                'website' => 'https://www.tastytreats.com',
                'additional_info' => '{"en":"Premium quality snacks"}',
                'description' => '{"en":"Delicious and healthy snacks"}',
                'is_active' => 1,
                'created_at' => '2025-07-06 10:15:30',
                'updated_at' => '2025-11-23 08:15:45'
            ],
            [
                'id' => 3,
                'name' => '{"en":"Healthy Bites"}',
                'slug' => 'healthy-bites',
                'logo' => 'brands/03K15FYWNYZZ4EA8MEWGVM83ZY.jpeg',
                'address' => '{"en":"Health Road 456"}',
                'city' => '{"en":"Ahmedabad"}',
                'pincode' => '1396447',
                'phone' => '840141250',
                'email' => 'healthybites@gmail.com',
                'website' => 'https://www.healthybites.com',
                'additional_info' => '{"en":"Organic and natural products"}',
                'description' => '{"en":"Organic snacks for health conscious"}',
                'is_active' => 1,
                'created_at' => '2025-07-07 11:20:45',
                'updated_at' => '2025-11-23 09:20:30'
            ],
            [
                'id' => 4,
                'name' => '{"en":"Sweet Delights"}',
                'slug' => 'sweet-delights',
                'logo' => 'brands/04K15FYWNYZZ4EA8MEWGVM83ZY.jpeg',
                'address' => '{"en":"Sweet Street 789"}',
                'city' => '{"en":"Vadodara"}',
                'pincode' => '1396448',
                'phone' => '840141251',
                'email' => 'sweetdelights@gmail.com',
                'website' => 'https://www.sweetdelights.com',
                'additional_info' => '{"en":"Traditional sweets and desserts"}',
                'description' => '{"en":"Authentic traditional sweets"}',
                'is_active' => 1,
                'created_at' => '2025-07-08 12:25:15',
                'updated_at' => '2025-11-23 10:25:20'
            ],
            [
                'id' => 5,
                'name' => '{"en":"Savory Snacks"}',
                'slug' => 'savory-snacks',
                'logo' => 'brands/05K15FYWNYZZ4EA8MEWGVM83ZY.jpeg',
                'address' => '{"en":"Savory Avenue 321"}',
                'city' => '{"en":"Rajkot"}',
                'pincode' => '1396449',
                'phone' => '840141252',
                'email' => 'savorysnacks@gmail.com',
                'website' => 'https://www.savorysnacks.com',
                'additional_info' => '{"en":"Spicy and savory treats"}',
                'description' => '{"en":"Spicy snacks for flavor lovers"}',
                'is_active' => 1,
                'created_at' => '2025-07-09 13:30:30',
                'updated_at' => '2025-11-23 11:30:45'
            ]
        ]);

        // Adding products for each brand (at least 3 products per brand)
        if ($brands) {
            Product::insert([
                // Products for Bhargav Snacks (brand_id: 1)
                [
                    'id' => 1,
                    'name' => '{"en":"Potato Wafer"}',
                    'slug' => 'potato_wafer',
                    'brand_id' => 1,
                    'category_id' => 1,
                    'description' => '{"en":"Crispy and delicious potato wafers"}',
                    'image' => null,
                    'base_price' => 3,
                    'sell_price' => 5,
                    'quantity' => 20,
                    'additional_info' => '{"en":null}',
                    'is_featured' => 0,
                    'is_popular' => 0,
                    'is_new' => 0,
                    'is_active' => 1,
                    'created_at' => '2025-07-05 14:40:14',
                    'updated_at' => '2025-09-14 15:52:55'
                ],
                [
                    'id' => 2,
                    'name' => '{"en":"Cheese Balls"}',
                    'slug' => 'cheese-balls',
                    'brand_id' => 1,
                    'category_id' => 1,
                    'description' => '{"en":"Cheesy and flavorful snack balls"}',
                    'image' => null,
                    'base_price' => 4,
                    'sell_price' => 6,
                    'quantity' => 15,
                    'additional_info' => '{"en":"Made with real cheese"}',
                    'is_featured' => 1,
                    'is_popular' => 1,
                    'is_new' => 0,
                    'is_active' => 1,
                    'created_at' => '2025-07-06 11:20:30',
                    'updated_at' => '2025-11-23 09:15:45'
                ],
                [
                    'id' => 3,
                    'name' => '{"en":"Masala Chips"}',
                    'slug' => 'masala-chips',
                    'brand_id' => 1,
                    'category_id' => 1,
                    'description' => '{"en":"Spicy masala flavored chips"}',
                    'image' => null,
                    'base_price' => 2,
                    'sell_price' => 4,
                    'quantity' => 25,
                    'additional_info' => '{"en":"Perfect for movie nights"}',
                    'is_featured' => 0,
                    'is_popular' => 1,
                    'is_new' => 1,
                    'is_active' => 1,
                    'created_at' => '2025-07-07 12:30:45',
                    'updated_at' => '2025-11-23 10:20:30'
                ],

                // Products for Tasty Treats (brand_id: 2)
                [
                    'id' => 4,
                    'name' => '{"en":"Chocolate Cookies"}',
                    'slug' => 'chocolate-cookies',
                    'brand_id' => 2,
                    'category_id' => 2,
                    'description' => '{"en":"Rich chocolate chip cookies"}',
                    'image' => null,
                    'base_price' => 5,
                    'sell_price' => 7,
                    'quantity' => 30,
                    'additional_info' => '{"en":"Freshly baked daily"}',
                    'is_featured' => 1,
                    'is_popular' => 1,
                    'is_new' => 0,
                    'is_active' => 1,
                    'created_at' => '2025-07-08 13:40:15',
                    'updated_at' => '2025-11-23 11:25:20'
                ],
                [
                    'id' => 5,
                    'name' => '{"en":"Fruit Biscuits"}',
                    'slug' => 'fruit-biscuits',
                    'brand_id' => 2,
                    'category_id' => 2,
                    'description' => '{"en":"Healthy biscuits with dried fruits"}',
                    'image' => null,
                    'base_price' => 4,
                    'sell_price' => 6,
                    'quantity' => 20,
                    'additional_info' => '{"en":"No added preservatives"}',
                    'is_featured' => 0,
                    'is_popular' => 0,
                    'is_new' => 1,
                    'is_active' => 1,
                    'created_at' => '2025-07-09 14:50:30',
                    'updated_at' => '2025-11-23 12:30:45'
                ],
                [
                    'id' => 6,
                    'name' => '{"en":"Oatmeal Bars"}',
                    'slug' => 'oatmeal-bars',
                    'brand_id' => 2,
                    'category_id' => 2,
                    'description' => '{"en":"Nutritious oatmeal energy bars"}',
                    'image' => null,
                    'base_price' => 6,
                    'sell_price' => 8,
                    'quantity' => 15,
                    'additional_info' => '{"en":"Perfect for breakfast"}',
                    'is_featured' => 0,
                    'is_popular' => 1,
                    'is_new' => 0,
                    'is_active' => 1,
                    'created_at' => '2025-07-10 15:10:45',
                    'updated_at' => '2025-11-23 13:15:30'
                ],

                // Products for Healthy Bites (brand_id: 3)
                [
                    'id' => 7,
                    'name' => '{"en":"Veggie Sticks"}',
                    'slug' => 'veggie-sticks',
                    'brand_id' => 3,
                    'category_id' => 3,
                    'description' => '{"en":"Baked vegetable sticks"}',
                    'image' => null,
                    'base_price' => 4,
                    'sell_price' => 6,
                    'quantity' => 25,
                    'additional_info' => '{"en":"Gluten-free and organic"}',
                    'is_featured' => 1,
                    'is_popular' => 1,
                    'is_new' => 1,
                    'is_active' => 1,
                    'created_at' => '2025-07-11 16:20:15',
                    'updated_at' => '2025-11-23 14:20:45'
                ],
                [
                    'id' => 8,
                    'name' => '{"en":"Protein Chips"}',
                    'slug' => 'protein-chips',
                    'brand_id' => 3,
                    'category_id' => 3,
                    'description' => '{"en":"High protein healthy chips"}',
                    'image' => null,
                    'base_price' => 5,
                    'sell_price' => 7,
                    'quantity' => 20,
                    'additional_info' => '{"en":"20g protein per serving"}',
                    'is_featured' => 0,
                    'is_popular' => 1,
                    'is_new' => 0,
                    'is_active' => 1,
                    'created_at' => '2025-07-12 17:30:30',
                    'updated_at' => '2025-11-23 15:25:20'
                ],
                [
                    'id' => 9,
                    'name' => '{"en":"Kale Crisps"}',
                    'slug' => 'kale-crisps',
                    'brand_id' => 3,
                    'category_id' => 3,
                    'description' => '{"en":"Baked kale chips"}',
                    'image' => null,
                    'base_price' => 6,
                    'sell_price' => 8,
                    'quantity' => 15,
                    'additional_info' => '{"en":"Keto-friendly snack"}',
                    'is_featured' => 0,
                    'is_popular' => 0,
                    'is_new' => 1,
                    'is_active' => 1,
                    'created_at' => '2025-07-13 18:40:45',
                    'updated_at' => '2025-11-23 16:30:30'
                ],

                // Products for Sweet Delights (brand_id: 4)
                [
                    'id' => 10,
                    'name' => '{"en":"Gulab Jamun"}',
                    'slug' => 'gulab-jamun',
                    'brand_id' => 4,
                    'category_id' => 4,
                    'description' => '{"en":"Traditional milk-based sweet"}',
                    'image' => null,
                    'base_price' => 3,
                    'sell_price' => 5,
                    'quantity' => 30,
                    'additional_info' => '{"en":"Made with khoya"}',
                    'is_featured' => 1,
                    'is_popular' => 1,
                    'is_new' => 0,
                    'is_active' => 1,
                    'created_at' => '2025-07-14 19:50:15',
                    'updated_at' => '2025-11-23 17:35:20'
                ],
                [
                    'id' => 11,
                    'name' => '{"en":"Rasgulla"}',
                    'slug' => 'rasgulla',
                    'brand_id' => 4,
                    'category_id' => 4,
                    'description' => '{"en":"Spongy cottage cheese balls"}',
                    'image' => null,
                    'base_price' => 4,
                    'sell_price' => 6,
                    'quantity' => 25,
                    'additional_info' => '{"en":"Soaked in sugar syrup"}',
                    'is_featured' => 0,
                    'is_popular' => 1,
                    'is_new' => 0,
                    'is_active' => 1,
                    'created_at' => '2025-07-15 20:10:30',
                    'updated_at' => '2025-11-23 18:40:45'
                ],
                [
                    'id' => 12,
                    'name' => '{"en":"Kaju Katli"}',
                    'slug' => 'kaju-katli',
                    'brand_id' => 4,
                    'category_id' => 4,
                    'description' => '{"en":"Cashew fudge slices"}',
                    'image' => null,
                    'base_price' => 8,
                    'sell_price' => 10,
                    'quantity' => 15,
                    'additional_info' => '{"en":"Premium quality cashews"}',
                    'is_featured' => 0,
                    'is_popular' => 0,
                    'is_new' => 1,
                    'is_active' => 1,
                    'created_at' => '2025-07-16 21:20:45',
                    'updated_at' => '2025-11-23 19:45:30'
                ],

                // Products for Savory Snacks (brand_id: 5)
                [
                    'id' => 13,
                    'name' => '{"en":"Pani Puri"}',
                    'slug' => 'pani-puri',
                    'brand_id' => 5,
                    'category_id' => 5,
                    'description' => '{"en":"Crispy hollow puris"}',
                    'image' => null,
                    'base_price' => 2,
                    'sell_price' => 4,
                    'quantity' => 40,
                    'additional_info' => '{"en":"Authentic street food"}',
                    'is_featured' => 1,
                    'is_popular' => 1,
                    'is_new' => 1,
                    'is_active' => 1,
                    'created_at' => '2025-07-17 22:30:15',
                    'updated_at' => '2025-11-23 20:50:20'
                ],
                [
                    'id' => 14,
                    'name' => '{"en":"Sev Stick"}',
                    'slug' => 'sev-stick',
                    'brand_id' => 5,
                    'category_id' => 5,
                    'description' => '{"en":"Crunchy chickpea flour sticks"}',
                    'image' => null,
                    'base_price' => 3,
                    'sell_price' => 5,
                    'quantity' => 35,
                    'additional_info' => '{"en":"Perfect for chaat"}',
                    'is_featured' => 0,
                    'is_popular' => 1,
                    'is_new' => 0,
                    'is_active' => 1,
                    'created_at' => '2025-07-18 23:40:30',
                    'updated_at' => '2025-11-23 21:55:45'
                ],
                [
                    'id' => 15,
                    'name' => '{"en":"Masala Peanuts"}',
                    'slug' => 'masala-peanuts',
                    'brand_id' => 5,
                    'category_id' => 5,
                    'description' => '{"en":"Spicy roasted peanuts"}',
                    'image' => null,
                    'base_price' => 4,
                    'sell_price' => 6,
                    'quantity' => 30,
                    'additional_info' => '{"en":"Party pack available"}',
                    'is_featured' => 0,
                    'is_popular' => 0,
                    'is_new' => 1,
                    'is_active' => 1,
                    'created_at' => '2025-07-19 23:50:45',
                    'updated_at' => '2025-11-23 22:10:30'
                ]
            ]);
        }
    }
}