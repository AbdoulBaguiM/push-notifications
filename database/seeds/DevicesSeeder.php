<?php


use Phinx\Seed\AbstractSeed;

class DevicesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     * @throws Exception
     */
    public function run(): void
    {
        $stmt = $this->query('SELECT id FROM users');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $devices = [];

        if ($users) {
            foreach ($users as $user) {
                for ($i = 0; $i < random_int(1, 3); $i++) {
                    $devices[] = [
                        'user_id' => $user['id'],
                        'token' => uniqid('', false),
                        'expired' => random_int(1, 10) > 9,
                    ];
                }
            }

            if ($devices) {
                $this->table('devices')
                    ->insert($devices)
                    ->saveData();
            }
        }
    }
}
