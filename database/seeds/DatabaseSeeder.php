<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);

        DB::table('houses')->delete();
        DB::table('item_types')->delete();
        DB::table('users')->delete();
        DB::table('teams')->delete();

        $user = App\User::create([
            'name' => 'Daniel Heyman',
            'email' => 'daniel.heyman@gmail.com',
            'password' => 'Daniel',
            'human' => true,
        ]);

        $website = $user->websites()->create([
            'name' => 'My first website',
            'url' => 'http://clicktrackprofit.com',
        ]);

        $itemType = App\ItemType::create([
            'name' => 'brick',
            'icon' => Image::make(base_path('resources/assets/item_images/brick.jpg'))->encode('data-url'),
            'human' => true,
            'find_chance' => 50,
            'find_min' => 1,
            'find_max' => 1,
            'house_item' => true,
            'protection_value' => 1,
        ]);

        $item = $itemType->items()->create([
            'count' => 1,
            'owner_id' => $user,
        ]);

        $house = App\House::create([
            'owner_id' => $user,
        ]);

        $house->items()->create([
            'loc_x' => 0,
            'loc_y' => 0,
            'item_type_id' => $itemType->id,
        ]);

        Model::reguard();
    }
}
