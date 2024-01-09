<?php


use Phinx\Seed\AbstractSeed;

class CountriesSeeder extends AbstractSeed
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
            [
                'name' => 'Jordan',
            ],
            [
                'name' => 'USA',
            ],
            [
                'name' => 'Egypt',
            ],
            [
                'name' => 'Saudi Arabia',
            ],
        ];

        $this->table('countries')
            ->insert($countries)
            ->saveData();
    }
}
