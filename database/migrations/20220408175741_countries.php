<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Countries extends AbstractMigration
{
    public function up(): void
    {
        $this->table('countries')
            ->addColumn('name', 'string', ['limit' => 40])
            ->create();
    }

    public function down(): void
    {
        $this->table('countries')
            ->drop();
    }
}
