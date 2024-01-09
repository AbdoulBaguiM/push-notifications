<?php


use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $countries = [
            1 => 'Jordan' ,
            2 => 'USA',
            3 => 'Egypt',
            4 => 'Saudi Arabia',
        ];
        $users = [];

        for ($i = 1; $i < 10001; $i++) {
            $users[] = [
                'name' => "User $i",
                'country_id' => array_rand($countries, 1),
            ];
        }

        $this->table('users')
            ->insert($users)
            ->saveData();
    }
}
