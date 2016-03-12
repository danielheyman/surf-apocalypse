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

        // Clear database
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

        // Create equipments
        app("EquipType")->createFromName("torso/shirts/brown_longsleeve", "brown longsleeve");
        app("EquipType")->createFromName("head/caps/leather_cap", "leather cap");
        app("EquipType")->createFromName("hair/plain/blonde", "blonde hair");
        app("EquipType")->createFromName("legs/pants/teal_pants", "teal pants");
        app("EquipType")->createFromName("feet/shoes/brown_shoes", "brown shoes");
        app("EquipType")->createFromName("body/light", "light body");

        // Create users
        $user = App\User::create([
            'name' => 'Daniel Heyman',
            'email' => 'daniel.heyman@gmail.com',
            'password' => 'Daniel',
            'confirmation_code' => null
        ]);
        
        $user2 = App\User::create([
            'name' => 'Test Dude',
            'email' => 'heymandan@gmail.com',
            'password' => 'test',
            'confirmation_code' => null,
            'team_id' => null,
        ]);

        // Create team
        $team = App\Team::create([
            'name' => 'Team Awesome',
            'description' => '',
            'user_count' => 2,
            'owner_id' => $user->id
        ]);

        $user->team()->associate($team);
        $user->save();

        $user2->team()->associate($team);
        $user2->save();

        // Create websites
        $website = $user->websites()->create([
            'name' => 'My Blank website',
            'url' => 'http://www.this-page-intentionally-left-blank.org/',
        ]);

        // Create items
        App\ItemType::create([
            'name' => 'coin',
            'icon' => '0000',
            'users_allowed' => App\UsersAllowed::ZOMBIE_AND_HUMAN,
            'find_chance' => 50,
            'find_decimal' => true,
            'find_min' => 1,
            'find_max' => 2,
            'item_type' => App\ItemTypes::COIN
        ]);

        $brick = App\ItemType::create([
            'name' => 'brick',
            'icon' => '0011',
            'users_allowed' => App\UsersAllowed::HUMAN,
            'find_chance' => 50,
            'find_min' => 1,
            'find_max' => 1,
            'item_type' => App\ItemTypes::MATERIAL
        ]);        
        
        // Give user a brick
        $user->giveItem($brick, 1);

        Model::reguard();
    }
}
