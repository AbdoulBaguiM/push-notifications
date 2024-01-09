<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class Notifications extends AbstractMigration
{
    public function up(): void
    {
        $this->table('notifications')
            ->addColumn('title', 'string')
            ->addColumn('message', 'text')
            ->addColumn('country_id', 'integer')
            ->addColumn('sent', 'integer', ['default' => 0])
            ->addColumn('failed', 'integer', ['default' => 0])
            ->addForeignKey(
                'country_id',
                'countries',
                'id',
                [
                    'delete'=> 'NO_ACTION',
                    'update'=> 'NO_ACTION',
                    'constraint' => 'notifications_country_id',
                ]
            )
            ->addTimestamps()
            ->create();
    }

    public function down(): void
    {
        $this->table('notifications')
            ->drop();
    }
}
