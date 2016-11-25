<?php
use Migrations\AbstractMigration;
use Phinx\Db\Table;

class AddMailColumn extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        /** @var Table $table */
        $table = $this->table('bounce_logs');
        $table->changeColumn("target_id", 'integer', [
            'signed' => false,
            'null' => true,
        ])
        ->addColumn("mail", 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'after' => 'target_id',
        ])
        ->addIndex(
            [
                'mail'
            ]
        )
        ->update();
    }

    public function down()
    {
        /** @var Table $table */
        $table = $this->table('bounce_logs');
        $table->changeColumn("target_id", 'integer', [
            'signed' => false,
            'null' => false,
        ])
        ->removeColumn("mail")
        ->update();
    }
}
