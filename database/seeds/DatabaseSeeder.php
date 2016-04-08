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
        DB::table('users')->delete();
        DB::table('teams')->delete();
        DB::table('pm_groups')->delete();

        DB::statement('ALTER SEQUENCE items_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE teams_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE users_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE websites_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE pms_id_seq RESTART;');
        DB::statement('ALTER SEQUENCE pm_groups_id_seq RESTART;');

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
            'description' => 'The team description.',
            'user_count' => 2,
            'owner_id' => $user->id
        ]);

        $user->team()->associate($team);
        $user->save();

        $user2->team()->associate($team);
        $user2->save();

        // Create websites
        $website = $user->websites()->create([
            'name' => 'My Cool Website Name',
            'url' => 'http://www.this-page-intentionally-left-blank.org/',
        ]);      

        Model::reguard();
    }
}
