<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Users extends AbstractMigration
{
    public function up(): void
    {
        $this->table('users')
            ->addColumn('name', 'string', ['limit' => 40])
            ->addColumn('country_id', 'integer')
            ->addIndex(['country_id'])
            ->addForeignKey(
                'country_id',
                'countries',
                'id',
                [
                    'delete'=> 'NO_ACTION',
                    'update'=> 'NO_ACTION',
                    'constraint' => 'users_country_id',
                ]
            )
            ->create();
    }

    public function down(): void
    {
        $this->table('users')
            ->drop();
    }
}
