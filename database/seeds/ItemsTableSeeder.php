<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	for ($i=0; $i < 100; $i++) { 
    		DB::table('items')->insert([
	            'name' => str_random(20),
	            'description' => str_random(40),
	            'running_balance' => rand(0, 1000),
	            'reorder_point' => rand(0, 100),
	            'running_balance' => rand(0, 1000),
	            'cat_id' => rand(1,2),
	            'unit_id' => rand(1,4),
	            'created_by' => 1,
	            'created_at' => '2018-09-19 03:35:42',
	            'updated_at' => '2018-09-19 03:35:42'
	        ]);
    	}
        
    }
}
