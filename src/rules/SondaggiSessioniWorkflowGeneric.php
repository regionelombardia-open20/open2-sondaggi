<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\core\rules
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

namespace open20\amos\sondaggi\rules;

use yii\rbac\Rule;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;

/**
 * Class ReadContentRule
 * @package open20\amos\sondaggi\rules
 */
class SondaggiSessioniWorkflowGeneric extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'sondaggiSessioniWorkflowGeneric';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $sondaggiModule = \open20\amos\sondaggi\AmosSondaggi::instance();
        $roles          = \Yii::$app->authManager->getRolesByUser($user);
        if ($params['model'] instanceof SondaggiRisposteSessioni && !$params['model']->sondaggi->isCompilable())
            return false;
        if (!empty($params['status'])) {
            $status = $params['status'];
            if (!empty($status)) {
                foreach ($roles as $k => $v) {
                    if (in_array($k, $sondaggiModule->compilationWorkflowRules[$status])) {

                        return true;
                    }
                }
                return false;
            }
        } else if (!empty($params['model'])) {
            /**
             * @var \open20\amos\sondaggi\models\SondaggiRisposteSessioni $sessionModel
             */
            $sessionModel = $params['model'];
            if ($sessionModel->status == \open20\amos\sondaggi\models\SondaggiRisposteSessioni::WORKFLOW_STATUS_INVIATO) {
                foreach ($roles as $k2 => $v2) {
                    if (in_array($k2, $sondaggiModule->compilationWorkflowRules)) {
                        return true;
                    }
                }
                return false;
            } else {
                return true;
            }
        }
        return true;
    }
}