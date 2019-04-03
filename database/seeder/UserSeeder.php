<?php

class UserSeeder extends \Illuminate\Database\Seeder {


    public $users = [
        'drkwolf' => [
            'club_admin'    => ['counter' => 0, 'all' => false],
            'team_admin'    => ['counter' => 0, 'all' => false],
            'coach'         => ['counter' => 0, 'all' => false],
            'player'        => ['counter' => 0, 'all' => false],
            'tutor'         => ['counter' => 0, 'all' => false],
            'issue_manager' => ['counter' => 0, 'all' => false],
        ],
        'julien' => [
            'club_admin'    => ['counter' => 0, 'all' => false],
            'coach'         => ['counter' => 0, 'all' => false],
            'player'        => ['counter' => 0, 'all' => false],
            'issue_manager' => ['counter' => 0, 'all' => false],
        ],
        'pedge' => [
            'club_admin'    => ['counter' => 0, 'all' => false],
            'player'        => ['counter' => 0, 'all' => false],
            'issue_manager' => ['counter' => 0, 'all' => false],
        ],
    ];


    /*
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->makeFakeUsers();
        $this->makeTestUsers();
    }


    public function makeUsers($users, $club_id, $team_id = null) {

        foreach ($users as $user => $roles) {
            $testUser = null;
            if (\App\Packages\User\Entities\User::where('username', $user)->exists()) {
                $testUser =  \App\Packages\User\Entities\User::where('username', $user)->firstOrFail();
            } else {
                $testUser = DatabaseSeeder::createUser($user);
                $this->addPicture($testUser);
            }

            foreach ($roles as $role) {
                $testUser->attachClubTeam($role, $club_id, $team_id);
            }
        }
    }
    /**
     * user that has club roles
     * @param $club_id
     * @param null $team_id
     */
    public function makeClubRoleUsers($club_id)
    {
        $users = [
            'drkwolf' => ['club_admin', 'team_admin', 'coach', 'player', 'tutor', 'issue_manager'],
            'julien' => ['club_admin', 'coach', 'player', 'issue_manager'],
            'pedge' => ['club_admin', 'player', 'issue_manager'],
        ];

        $this->makeUsers($users, $club_id, null);

    }

    public function makeTestTeamUsers($team) {
        $users = [
            'drkwolf' => ['coach', 'player'],
            'julien' => ['coach', 'player'],
            'pedge' => ['player']
        ];
        $this->makeUsers($users, $team->club_id, $team->id);
    }

    public function makeTestUsers() {

        foreach(\App\Packages\Club\Entities\Club::all() as $club) {
            $this->makeClubRoleUsers($club->id);

            foreach ($club->categoryTeams()->get() as $team) {
                $this->makeTestTeamUsers($team);
            }

//            if($j == 1) {
//                      $testUser->attachClubTeam('player', $club->id, $team->id);
//                      $testUser->attachMinor($player->id);
//                      $testUser->attachTutor($tutor->id);
//            }
        }
    }

    public function makeFakeUsers() {

        // create SuperAdmin
        $player_counter = 0;
        $tutor_counter = 0;
        $coach_counter = 0;
        foreach(\App\Packages\Club\Entities\Club::all() as $club) {
            $admin_counter = 0;

            foreach($club->categoryTeams()->get() as $team) {
                $admin_counter++;

                $user = DatabaseSeeder::createUser("admin{$admin_counter}_{$club->id}");
                $this->addPicture($user);

                // coaches
                for($j=1; $j < DatabaseSeeder::COACH_BY_TEAM; $j++) {
                    $prov = DatabaseSeeder::createUser('coach' . $coach_counter++ , DatabaseSeeder::USERPASS, 'coach');
                    $this->addPicture($prov);

                    $prov->attachClubTeam('coach', $team->club->id, null);        // attach to club
                    $prov->attachClubTeam('coach', $team->club->id, $team->id);   // attach to Team
                }

                // create player/tutor + attach player tutor to club and team
                for($j=1; $j < DatabaseSeeder::PLAYER_NBR; $j++) {
                    $tutor = DatabaseSeeder::createUser('tutor'. $tutor_counter++, DatabaseSeeder::USERPASS, 'tutor');
                    $this->addPicture($tutor);

                    $player = DatabaseSeeder::createUser('player'.$player_counter++, DatabaseSeeder::USERPASS, 'player');
                    $this->addPicture($player);
                    $player->attachTutor($tutor->id);

                    $player->attachClubTeam('player', $club->id, null);  // attach to club
                    $player->attachClubTeam('player', $club->id, $team->id); // attach to club/team

                    $tutor->attachClubTeam('tutor', $club->id, null); // club role
                    $tutor->attachClubTeam('tutor', $club->id, $team->id); // team role
                }
            }
        }
    }


    public function addPicture(\App\Packages\User\Entities\User $user) {
        $storage_faker_imgs_path = 'app/faker/pics';
        $src_dir = storage_path($storage_faker_imgs_path);
        $src_path =  $src_dir .'/'. random_int(1,10) . '.jpg';
        $user->copyMedia($src_path)
             ->toMediaCollection('pictures');
    }
}
