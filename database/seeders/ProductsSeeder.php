<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'eios' => [
                'name' => 'Eios',
                'plans' => [
                    ['name' => 'Eios Basic', 'billing_cycle' => 'monthly', 'price' => 490000],
                    ['name' => 'Eios Pro', 'billing_cycle' => 'monthly', 'price' => 990000],
                    ['name' => 'Eios Pro', 'billing_cycle' => 'yearly', 'price' => 9900000],
                ],
            ],
            'onepaper' => [
                'name' => 'OnePaper',
                'plans' => [
                    ['name' => 'OnePaper Basic', 'billing_cycle' => 'monthly', 'price' => 390000],
                    ['name' => 'OnePaper Pro', 'billing_cycle' => 'monthly', 'price' => 790000],
                    ['name' => 'OnePaper Pro', 'billing_cycle' => 'yearly', 'price' => 7900000],
                ],
            ],
        ];

        foreach ($products as $code => $data) {
            $product = Product::firstOrCreate(['code' => $code], ['name' => $data['name']]);

            foreach ($data['plans'] as $plan) {
                Plan::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'name' => $plan['name'],
                        'billing_cycle' => $plan['billing_cycle'],
                    ],
                    ['price' => $plan['price']]
                );
            }
        }
    }
}
