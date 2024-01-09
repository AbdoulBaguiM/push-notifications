<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class NotificationsQueue extends AbstractMigration
{
    public function up(): void
    {
        $this->table('notifications_queue')
            ->addColumn('notification_id', 'integer')
            ->addColumn('device_token', 'string')
            ->addColumn('status', 'enum', [
                'values' => ['queued', 'sent', 'failed'],
                'default' => 'queued'
            ])
            ->addForeignKey(
                'notification_id',
                'notifications',
                'id',
                [
                    'delete' => 'NO_ACTION',
                    'update' => 'NO_ACTION',
                    'constraint' => 'queue_notification_id',
                ]
            )
            ->addTimestamps()
            ->create();
    }

    public function down(): void
    {
        $this->table('notifications_queue')
            ->drop();
    }
}
