<?php
namespace AntiBounce\Model\Table;

use AntiBounce\Model\Entity\BounceLog;
use Aws\Sns\Message;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BounceLogs Model
 *
 * @package \AntiBounce\Model\Table
 *
 * @property UsersTable Users
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BounceLogsTable extends Table
{
    private $config;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->config = Configure::read('AntiBounce');

        $this->table('bounce_logs');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo("AntiBounce.Users", [
            'foreignKey' => "target_id",
            'bindingKey' => $this->config['settings']['email']['key']
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        return $validator;
    }

    /**
     * Execute update
     *
     * @param Message $message
     * @return void
     * @throws \Exception
     */
    public function store(Message $message)
    {
        $detail = json_decode($message['Message'], true);

        if ($message['TopicArn'] != $this->config['topic'] &&
            $detail['mail']['source'] != $this->config['mail']) {
            throw new \Exception('Error: Failed checkSnsTopic.');
        }

        $targetEmail = reset($detail['bounce']['bouncedRecipients'])['emailAddress'];

        $connection = $this->connection();
        $connection->transactional(function () use ($targetEmail, $message) {
            $settings = $this->config['settings'];

            try {
                // Insert bounce_logs
                $key = $this->Users->getKeyByEmail($targetEmail);

                /** @var BounceLog $bounceLogs */
                $bounceLogs = $this->newEntity([
                    "target_id" => $key,
                    'message' => $message['Message'],
                ]);

                if ($bounceLogs->errors()) {
                    throw new \Exception('Error: Failed insertLog(). %s = %d');
                }

                $this->save($bounceLogs, ['atomic' => false]);

                // Update mail sending setting
                $count = $this->find()
                    ->where(["target_id" => $key])
                    ->count();

                if ($settings['stopSending'] && $count > $settings['bounceLimit']) {
                    $this->Users->updateFields($targetEmail);
                }
            } catch (\Exception $e) {
                Log::write(LOG_ERR, $e->getTraceAsString());
                return false;
            }
            return true;
        });
    }

    /**
     * Notify end point.
     *
     * @param Message $message
     * @return void
     * @throws \Exception
     */
    public function SubscriptionEndPoint(Message $message)
    {
        if ($message['TopicArn'] != $this->config['topic']) {
            throw new \Exception('Error: Failed checkSnsTopic.');
        }

        $conn = curl_init();
        $url = $message['SubscribeURL'];
        curl_setopt($conn, CURLOPT_URL, $url);
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, true);
        curl_exec($conn);
        curl_close($conn);
    }
}
