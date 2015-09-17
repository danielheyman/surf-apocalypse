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

        // DB::raw("SELECT setval('house_items_id_seq', 1);");
        // DB::raw("SELECT setval('houses_id_seq', 1);");
        // DB::raw("SELECT setval('item_types_id_seq', 1);");
        // DB::raw("SELECT setval('items_id_seq', 1);");
        // DB::raw("SELECT setval('teams_id_seq', 1);");
        // DB::raw("SELECT setval('users_id_seq', 1);");
        // DB::raw("SELECT setval('websites_id_seq', 1);");

        DB::statement('ALTER SEQUENCE houses_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE item_types_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE items_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE teams_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE users_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE websites_id_seq RESTART;');

        $team = App\Team::create([
            'name' => 'Team Awesome',
        ]);

        $user = App\User::create([
            'name' => 'Daniel Heyman',
            'email' => 'daniel.heyman@gmail.com',
            'password' => 'Daniel',
            'human' => true,
            'confirmation_code' => null,
            'team_id' => $team->id ?: null,
        ]);

        App\User::create([
            'name' => 'Test Dude',
            'email' => 'test@dude.com',
            'password' => 'test',
            'human' => true,
            'confirmation_code' => null,
            'team_id' => $team->id ?: null,
        ]);

        $website = $user->websites()->create([
            'name' => 'My first website',
            'url' => 'http://clicktrackprofit.com',
        ]);

        App\ItemType::create([
            'name' => 'coin',
            'icon' => Image::make(base_path('resources/assets/item_images/coin.png'))->encode('data-url'),
            'human' => true,
            'find_chance' => 50,
            'find_min' => 1,
            'find_max' => 2,
            'item_type' => App\ItemTypes::COIN
        ]);

        $itemType = App\ItemType::create([
            'name' => 'brick',
            'icon' => Image::make(base_path('resources/assets/item_images/brick.jpg'))->encode('data-url'),
            'human' => true,
            'find_chance' => 50,
            'find_min' => 1,
            'find_max' => 1,
            'item_type' => App\ItemTypes::HOUSE,
            'protection_value' => 1
        ]);

        $item = $itemType->items()->create([
            'count' => 1,
            'owner_id' => $team ?: $user,
        ]);

        $house = App\House::create([
            'owner_id' => $team ?: $user,
        ]);

        $house->items()->create([
            'loc_x' => 0,
            'loc_y' => 0,
            'item_type_id' => $itemType->id,
        ]);

        Model::reguard();
    }
}
