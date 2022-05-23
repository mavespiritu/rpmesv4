<?php

use yii\db\Migration;

/**
 * Class m220523_063642_group_accomplishment
 */
class m220523_063642_group_accomplishment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tables = Yii::$app->db->schema->getTableNames();
        $dbType = $this->db->driverName;
        $tableOptions_mysql = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";
        $tableOptions_mssql = "";
        $tableOptions_pgsql = "";
        $tableOptions_sqlite = "";
        /* MYSQL */
        if (!in_array('group_accomplishment', $tables))  { 
        if ($dbType == "mysql") {
            $this->createTable('{{%group_accomplishment}}', [
                'id' => 'INT(255) NOT NULL AUTO_INCREMENT',
                0 => 'PRIMARY KEY (`id`)',
                'project_id' => 'INT(255) NULL',
                'year' => 'INT(4) NULL',
                'quarter' => 'ENUM(\'Q1\',\'Q2\',\'Q3\',\'Q4\') NULL',
                'value' => 'TEXT NULL',
                'remarks' => 'TEXT NULL',
            ], $tableOptions_mysql);
        }
        }
         
         
        $this->createIndex('idx_project_id_2479_00','group_accomplishment','project_id',0);
         
        $this->execute('SET foreign_key_checks = 0');
        $this->addForeignKey('fk_project_2472_00','{{%group_accomplishment}}', 'project_id', '{{%project}}', 'id', 'CASCADE', 'CASCADE' );
        $this->execute('SET foreign_key_checks = 1;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS `group_accomplishment`');
        $this->execute('SET foreign_key_checks = 1;');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220523_063642_group_accomplishment cannot be reverted.\n";

        return false;
    }
    */
}
