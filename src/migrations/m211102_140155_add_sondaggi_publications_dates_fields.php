<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\migrations
 * @category   CategoryName
 */

use open20\amos\sondaggi\models\Sondaggi;
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m211102_140155_add_sondaggi_publications_dates_fields
 */
class m211102_140155_add_sondaggi_publications_dates_fields extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(Sondaggi::tableName(), 'publication_date_begin', $this->dateTime()->null()->after('status'));
        $this->addColumn(Sondaggi::tableName(), 'publication_date_end', $this->dateTime()->null()->after('publication_date_begin'));
        $this->update(Sondaggi::tableName(), ['publication_date_begin' => new Expression('created_at')]);
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(Sondaggi::tableName(), 'publication_date_begin');
        $this->dropColumn(Sondaggi::tableName(), 'publication_date_end');
        return true;
    }
}
