<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-sondaggi
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\sondaggi\modules\v1\controllers;

use open20\amos\sondaggi\modules\v1\models\TaskSondaggi;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use function GuzzleHttp\json_encode;

class ExtractController extends Controller
{

    /**
     * @var string yii console application file that will be executed
     */
    public $yiiscript;
    /**
     * @var string path to php executable
     */
    public $phpexec;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        set_time_limit(0);
        if($this->yiiscript == null) {
            $this->yiiscript = "@app/../yii";
        }
    }
    /**
     * Runs yii console command
     *
     * @param $cmd command with arguments
     * @param string $output filled with the command output
     * @return int termination status of the process that was run
     */
    public function run($cmd, $params = [])
    {
        //$status = exec($this->buildCommand($cmd));
        $status = pclose(popen($this->buildCommand($cmd), "r"));
        return $status;
    }
    /**
     * Builds the command string
     *
     * @param $cmd Yii command
     * @return string full command to execute
     */
    protected function buildCommand($cmd)
    {
        return $this->getPHPExecutable() . ' ' . Yii::getAlias($this->yiiscript) . ' ' . $cmd . ' 2>&1 &';
    }
    /**
     * If property $phpexec is set it will be used as php executable
     *
     * @return string path to php executable
     */
    public function getPHPExecutable()
    {
        if($this->phpexec) {
            return $this->phpexec;
        }
        //return strpos(PHP_SAPI, 'apache') !== false ? PHP_BINDIR . '/php' : PHP_BINARY;
        return  PHP_BINDIR . '/php';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'extract-sondaggio',
                            'extract-sondaggio-status',
                            'extract-sondaggio-result'
                            
                        ],
                        'roles' => ['@']
                    ]
                ]
            ]
        ]);
        return $behaviors;
    }

    /**
     *
     * @param type $sondaggio_id
     */
    public function actionExtractSondaggio($sondaggio_id){
        $cmd = "sondaggi/extract " . $sondaggio_id;
        $task = new TaskSondaggi();
        $task->command = $cmd;
        $task->status = 1;
        $task->filename = '';
        $task->save();
        $this->run($cmd. ' ' . $task->id);
        return json_encode(['task_id' => $task->id]);
    }


    /**
     *
     * @param type $task_id
     */
    public function actionExtractSondaggioStatus($task_id)
    {
        $status = 0;

        $task = TaskSondaggi::findOne($task_id);
        if(!is_null($task)){
            $status = $task->status;
        }
        return json_encode(['status' => $status]);
    }


    /**
     *
     * @param type $task_id
     */
    public function actionExtractSondaggioResult($task_id)
    {
        $name  = '';
        $task = TaskSondaggi::findOne($task_id);
        if(!is_null($task)){
            Yii::$app->response->sendFile($task->filename, $name, ['inline' => false])->send();
        }
    }
    
}