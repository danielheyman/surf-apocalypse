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

        DB::table('item_types')->delete();
        DB::table('users')->delete();
        DB::table('teams')->delete();
        DB::table('pm_groups')->delete();

        DB::statement('ALTER SEQUENCE item_types_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE items_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE teams_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE users_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE websites_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE pms_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE pm_groups_id_seq RESTART;');

        $user = App\User::create([
            'name' => 'Daniel Heyman',
            'email' => 'daniel.heyman@gmail.com',
            'password' => 'Daniel',
            'confirmation_code' => null
        ]);

        $team = App\Team::create([
            'name' => 'Team Awesome',
            'description' => '',
            'user_count' => 2,
            'owner_id' => $user->id
        ]);

        $user->team()->associate($team);
        $user->save();

        $user2 = App\User::create([
            'name' => 'Test Dude',
            'email' => 'heymandan@gmail.com',
            'password' => 'test',
            'confirmation_code' => null,
            'team_id' => $team->id ?: null,
        ]);

        $website = $user->websites()->create([
            'name' => 'My Blank website',
            'url' => 'http://www.this-page-intentionally-left-blank.org/',
        ]);

        App\ItemType::create([
            'name' => 'coin',
            'icon' => 0,
            'character_type' => App\CharacterTypes::ZOMBIE_AND_HUMAN,
            'find_chance' => 50,
            'find_min' => 1,
            'find_max' => 2,
            'item_type' => App\ItemTypes::COIN
        ]);

        $itemType = App\ItemType::create([
            'name' => 'brick',
            'icon' => 101,
            'character_type' => App\CharacterTypes::HUMAN,
            'find_chance' => 50,
            'find_min' => 1,
            'find_max' => 1,
            'item_type' => App\ItemTypes::MATERIAL
        ]);

        $item = $itemType->items()->create([
            'count' => 1,
            'user_id' => $user->id,
        ]);

        Model::reguard();
    }
}
