<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\commands
 * @category   CategoryName
 */

namespace open20\amos\sondaggi\commands;

use open20\amos\admin\models\UserProfile;
use open20\amos\core\utilities\Email;
use open20\amos\core\user\User;
use open20\amos\organizzazioni\models\Profilo;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\controllers\ConsoleController;
use open20\amos\sondaggi\models\search\SondaggiInvitationsSearch;
use open20\amos\sondaggi\models\Sondaggi;
use open20\amos\sondaggi\models\SondaggiInvitationMm;
use open20\amos\sondaggi\models\SondaggiInvitations;
use open20\amos\sondaggi\models\SondaggiRisposteSessioni;
use open20\amos\sondaggi\models\SondaggiUsersInvitationMm;
use open20\amos\sondaggi\utility\SondaggiUtility;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * Class NotifierController
 * @package open20\amos\sondaggi\commands
 */
class NotifierController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->module = AmosSondaggi::instance();
        parent::init();
    }

    /**
     * @param string $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return [];
    }

    public function actionSendClosed() {
        try {
            Console::stdout('Start sending closing e-mails ' . PHP_EOL);
            $sondaggi = $this->listSondaggiClosed();
            foreach ($sondaggi as $sondaggio) {
                $this->sendEmailPollClosed($sondaggio);
            }
            Console::stdout('All e-mails sent correctly.' . PHP_EOL);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * This action sends nightly mails.
     */
    public function actionSendInvitations()
    {
        try {
            Console::stdout('Start sending invitation e-mails ' . PHP_EOL);
            $sondaggi = $this->listSondaggi();

            foreach ($sondaggi as $sondaggio) {
                $queryInv = $sondaggio->getInvitations();
                $queryInv->andWhere(['invited' => false])->andWhere(['active' => true]);
                $sent_to = [];

                $invitations = $queryInv->all();
                foreach ($invitations as $invitation) {
                    if ($invitation->target == SondaggiInvitations::TARGET_ORGANIZATIONS) {
                        $this->notifyOrganization($sondaggio, $invitation, $sent_to);
                    }
                    else if ($invitation->target == SondaggiInvitations::TARGET_USERS) {
                        $this->notifyUser($sondaggio, $invitation, $sent_to);
                    }
                }
            }

            Console::stdout('End send invitations ' . PHP_EOL);

        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param $id
     * @param $task_id
     * @return void
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionExtract($id, $task_id = null)
    {
        ConsoleController::actionExtract($id, $task_id);
    }

    /**
     * @return mixed
     */
    protected function listSondaggi()
    {
        $search = $this->module->createModel('SondaggiSearch');
        $query = $search->find();
        $query->andWhere(['sondaggi.status' => Sondaggi::WORKFLOW_STATUS_VALIDATO]);
        $query->andWhere(['<=', 'publish_date', new Expression('CURDATE()')]);
        $query->andWhere(['or', ['>=', 'close_date', new Expression('CURDATE()')], ['close_date' => null]]);

        return $query->all();
    }

    /**
     * @return mixed
     */
    protected function listSondaggiClosed()
    {
        $query = Sondaggi::find();
        $query->andWhere(['sondaggi.status' => Sondaggi::WORKFLOW_STATUS_VALIDATO]);
        $query->andWhere(['close_date' => new Expression('DATE_ADD(CURDATE(), INTERVAL -1 DAY)')]);

        return $query->all();
    }

    /**
     * @param Sondaggi $sondaggio
     * @param UserProfile $referenteOperativo
     * @param Profilo $organization
     */
    protected function sendMail($sondaggio, $referenteOperativo, $organization)
    {
        $this->sendEmailReferenteOperativo($sondaggio, $referenteOperativo, $organization);

        Console::stdout('Send Mail to ' . $referenteOperativo->user->email . PHP_EOL);
        $inv_mm = new SondaggiInvitationMm();
        $inv_mm->sondaggi_id = $sondaggio->id;
        $inv_mm->to_id = $organization->id;
        $inv_mm->save(false);
    }

    /**
     * @param Sondaggi $sondaggio
     * @param Profilo $organization
     */
    protected function sendMailOrganization($sondaggio, $organization)
    {
        $this->sendEmailOrganization($sondaggio, $organization);

        Console::stdout('Send Mail to ' . $organization->operativeHeadquarter->email);
        $inv_mm = new SondaggiInvitationMm();
        $inv_mm->sondaggi_id = $sondaggio->id;
        $inv_mm->to_id = $organization->id;
        $inv_mm->save(false);
    }

    /**
     * @param $sondaggio Sondaggi
     * @param $user User
     * @param $invitation SondaggiInvitations
     * @return void
     * @throws InvalidConfigException
     */
    protected function sendMailUser($sondaggio, $user, $invitation)
    {
        $to = [$user->email];
        $subject = AmosSondaggi::t('amossondaggi', 'Invito sondaggio');
        $message = SondaggiUtility::getInvitationEmailContent($sondaggio, $user->userProfile);
        $this->sendEmailGeneral($to, null, $subject, $message);

        Console::stdout('Send Mail to ' . $user->email);
        $inv_mm = new SondaggiUsersInvitationMm();
        $inv_mm->sondaggi_id = $sondaggio->id;
        $inv_mm->user_id = $user->id;
        $inv_mm->to_id = $user->id;
        $inv_mm->save(false);
    }

    /**
     * @param array|string $to
     * @param $profile
     * @param $subject
     * @param $message
     * @param array $files
     * @return bool
     */
    protected function sendEmailGeneral($to, $profile, $subject, $message, $files = [])
    {
        try {
            $from = '';
            if (isset(\Yii::$app->params['email-assistenza'])) {
                //use default platform email assistance
                $from = \Yii::$app->params['email-assistenza'];
            }

            /** @var Email $email */
            $email = new Email();
            $email->sendMail($from, $to, $subject, $message, $files, [], ['profile' => $profile]);
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return true;
    }

    /**
     * @param Sondaggi $sondaggio
     * @param UserProfile $referenteOperativo
     * @throws InvalidConfigException
     */
    protected function sendEmailReferenteOperativo($sondaggio, $referenteOperativo,$organization)
    {
        $emailsTo = [$referenteOperativo->user->email,$organization->operativeHeadquarter->email];
        $subject = AmosSondaggi::t('amossondaggi', '#invito_referenteOperativo_subject');
        $message = SondaggiUtility::getInvitationEmailContent($sondaggio, $referenteOperativo);

        $this->sendEmailGeneral($emailsTo, $referenteOperativo->user, $subject, $message);
    }

    /**
     * @param Sondaggi $sondaggio
     * @param Profilo $organization
     * @throws InvalidConfigException
     */
    protected function sendEmailOrganization($sondaggio, $organization)
    {
        $emailsTo = [$organization->operativeHeadquarter->email];
        $subject = AmosSondaggi::t('amossondaggi', '#invito_organization_subject');
        $message = SondaggiUtility::getInvitationOrganizationEmailContent($sondaggio, $organization);

        $this->sendEmailGeneral($emailsTo, null, $subject, $message);
    }

    protected function sendEmailPollClosed($sondaggio) {
        // TO REFACTOR: se il cron gira una volta al giorno, partirà una sola mail per sondaggio.
        // Pensare ad un metodo più elegante...
        Console::stdout('Sending e-mail for '.$sondaggio->titolo . PHP_EOL);

        $close_date = AmosSondaggi::t('amossondaggi', '#email_closed_poll_close_date', [
            'closeDate' => Yii::$app->formatter->asDate($sondaggio->close_date)
        ]);

        $imageUrl = Yii::$app->urlManager->createAbsoluteUrl([
            '/' . $sondaggio->getModelImageUrl()
        ]);

        $manageLink = Yii::$app->urlManager->createAbsoluteUrl([
            '/' . AmosSondaggi::getModuleName() . '/dashboard/dashboard',
            'id' => $sondaggio->id
        ]);

        $participations = AmosSondaggi::t('amossondaggi', '#email_closed_poll_participations', [
            'invited' => $sondaggio->getEntiInvitati()->count(),
            'compiled' => \Yii::$app->getModule('sondaggi')->forceOnlyFrontend ? $sondaggio->getNumeroPartecipazioni() : $sondaggio->getCompilazioniStatus(null, [0, 1])
        ]);

        $subject = AmosSondaggi::t('amossondaggi', '#email_closed_poll_subject', ['title' => $sondaggio->titolo]);
        $message = AmosSondaggi::t('amossondaggi', '#email_closed_poll', [
            'imageUrl' => $imageUrl,
            'title' => $sondaggio->titolo,
            'description' => $sondaggio->descrizione,
            'closeDateMessage' => $close_date,
            'manageLink' => $manageLink,
            'participations' => $participations
        ]);
        $xlsResults = SondaggiUtility::generateXlsResults($sondaggio->id);
        $files = [];
        if (!empty($xlsResults)) {
            $files = [$xlsResults];
        }

        $users = User::find()->where(['status' => User::STATUS_ACTIVE])->all();

        // Send mail to users with permission AMMINISTRAZIONE_SONDAGGI
        foreach($users as $user) {
            if (!empty($user->email) && Yii::$app->authManager->checkAccess($user->id, 'AMMINISTRAZIONE_SONDAGGI')) {
                Console::stdout('   Sending e-mail to '.$user->email . PHP_EOL);
                $this->sendEmailGeneral([trim($user->email)], $user, $subject, $message, $files);
            }
        }

        $additionalEmails = [];
        if (!empty($sondaggio->additional_emails) && $sondaggio->send_pdf_via_email_closed) {
            $additionalEmails = explode(';', $sondaggio->additional_emails);
        }

        // Send mail to additional emails
        foreach($additionalEmails as $email) {
            $user = User::find()->andWhere(['email' => $email])->one();
            Console::stdout('   Sending e-mail to '.$email . PHP_EOL);
            $this->sendEmailGeneral([trim($email)], $user, $subject, $message, $files);
        }

        // Delete xls file
        unlink($xlsResults);
    }

    /**
     * @param $sondaggio Sondaggi
     * @param $invitation SondaggiInvitations
     * @param $sent_to array
     * @return void
     * @throws InvalidConfigException
     */
    protected function notifyOrganization($sondaggio, $invitation, $sent_to)
    {
        Console::stdout('Sending mails to organizations' . PHP_EOL);
        $organizationsQuery = SondaggiInvitationsSearch::searchOrganizations($invitation->toArray())->query;
        $invitedOrgs = \yii\helpers\ArrayHelper::getColumn(
            SondaggiInvitationMm::find()
                ->select('to_id')
                ->andWhere(['sondaggi_id' => $sondaggio->id])
                ->asArray()
                ->all(),
            'to_id');
        $sent_to = \yii\helpers\ArrayHelper::merge($sent_to, $invitedOrgs);
        $invitation->count = $organizationsQuery->count();
        Console::stdout('Already invited: ' . implode(', ', $sent_to) . PHP_EOL);
        $invitation->invited = 1;
        $invitation->save(false);
        // $organizationsQuery->leftJoin(SondaggiInvitationMm::tableName(), [SondaggiInvitationMm::tableName() . '.sondaggi_id' => $sondaggio->id,
        //     SondaggiInvitationMm::tableName() . '.to_id' => new Expression('profilo.id'), SondaggiInvitationMm::tableName() . '.deleted_at' => null]);
        // $organizationsQuery->andWhere([SondaggiInvitationMm::tableName() . '.sondaggi_id' => null,
        //     SondaggiInvitationMm::tableName() . '.to_id' => null]);
        foreach ($organizationsQuery->each() as $organization) {
            // Checks if invite has been sent already to this organization...
            if (!in_array($organization->id, $sent_to)) {
                Console::stdout('Organization ' . $organization->name . PHP_EOL);
                $email = '';
                $referente = $organization->referenteOperativo;
                $sent_to[] = $organization->id;
                if (!is_null($referente)) {
                    $this->sendMail($sondaggio, $referente, $organization);
                } else {
                    $this->sendMailOrganization($sondaggio, $organization);
                }
            }
        }
    }

    /**
     * @param $sondaggio Sondaggi
     * @param $invitation SondaggiInvitations
     * @param $sent_to array
     * @return void
     * @throws InvalidConfigException
     */
    protected function notifyUser($sondaggio, $invitation, $sent_to)
    {
        Console::stdout('Sending mails to users' . PHP_EOL);
        $params = $invitation->toArray();
        $params['users'] = $invitation->search_users;
        $params['tagValues'] = $invitation->search_tags;
        if ($sondaggio->isCommunitySurvey()) {
            $params['community_id'] = $sondaggio->community_id;
        }
        $usersQuery = SondaggiInvitationsSearch::searchInvitedUsers($params);

        $invitedUsers = \yii\helpers\ArrayHelper::getColumn(
            SondaggiUsersInvitationMm::find()
                ->andWhere(['sondaggi_id' => $sondaggio->id])
                ->all(),
            'to_id');
        $sent_to = \yii\helpers\ArrayHelper::merge($sent_to, $invitedUsers);
        $invitation->count = $usersQuery->count();
        Console::stdout('Already invited: ' . implode(', ', $sent_to) . PHP_EOL);
        $invitation->invited = 1;
        $invitation->save(false);
        foreach ($usersQuery->each() as $user) {
            // Checks if invite has been sent already to this user...
            if (!in_array($user->id, $sent_to)) {
                Console::stdout('User ' . $user->id . ': ' . $user->userProfile->nomeCognome . PHP_EOL);
                $sent_to[] = $user->id;
                $this->sendMailUser($sondaggio, $user, $invitation);
            }
        }
    }

}
