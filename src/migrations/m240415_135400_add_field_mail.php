<?php

use open20\amos\sondaggi\models\SondaggiInvitations;
use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m240415_135400_add_field_mail
 */
class m240415_135400_add_field_mail extends Migration {

    const TABLE = '{{%sondaggi}}';

    /*     * , 'code'
     * {@inheritdoc}
     */

    public function safeUp() {
        $this->addColumn(self::TABLE, 'mail_subject_send_pdf_guest', $this->text()->null()->after('send_pdf_via_email'));
        $this->addColumn(self::TABLE, 'mail_send_pdf_guest', $this->text()->null()->after('mail_subject_send_pdf_guest'));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn(self::TABLE, 'mail_send_pdf_guest');
        $this->dropColumn(self::TABLE, 'mail_subject_send_pdf_guest');

        return true;
    }
}
