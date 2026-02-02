<?php

use open20\amos\sondaggi\models\SondaggiInvitations;
use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m210726_113704_add_fields_sondaggi_domande
 */
class m240124_105500_add_anonimous_poll_table_sondaggi extends Migration
{
    const TABLE = '{{%sondaggi}}';

    /**, 'code'
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            self::TABLE,
            'anonimous_poll',
            $this->boolean()->defaultValue(false)->after('abilita_registrazione'));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE, 'anonimous_poll');

        return true;
    }
}
