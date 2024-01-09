<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class Devices extends AbstractMigration
{
    public function up(): void
    {
        $this->table('devices')
            ->addColumn('user_id', 'integer')
            ->addColumn('token', 'string')
            ->addColumn('expired', 'boolean', ['default' => 0])
            ->addIndex(['expired'])
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'delete'=> 'NO_ACTION',
                    'update'=> 'NO_ACTION',
                    'constraint' => 'devices_user_id',
                ]
            )
            ->create();
    }

    public function down(): void
    {
        $this->table('devices')
            ->drop();
    }
}
