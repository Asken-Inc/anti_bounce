<?php
use Migrations\AbstractMigration;
use Phinx\Db\Table;

class CreateTableBounceLogs extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        /** @var Table $table */
        $table = $this->table('bounce_logs');
        $table->addColumn("target_id", 'integer', [
            'signed' => false,
            'null' => false,
        ])
        ->addColumn("message", 'text', [
            'default' => null,
            'limit' => null,
            'null' => false,
        ])
        ->addColumn("created", 'datetime', [
            'default' => null,
            'null' => true,
        ])
        ->addColumn("modified", 'datetime', [
            'default' => null,
            'null' => true,
        ])
        ->addIndex(
            [
                'target_id'
            ]
        )
        ->create();
    }
}
