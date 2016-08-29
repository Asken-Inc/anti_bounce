<?php
namespace AntiBounce\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;

/**
 * BounceLogs Controller
 *
 * @package AntiBounce\Countroller
 *
 * @property \AntiBounce\Model\Table\BounceLogsTable $BounceLogs
 */
class BounceLogsController extends Controller
{
    /**
     * beforeFilter
     *
     * @param Event $event
     * @return null|void
     */
    public function beforeFilter(Event $event)
    {
        $this->autoRender = false;
        return parent::beforeFilter($event);
    }

    /**
     * Update record from received bounce mail.
     */
    public function receive()
    {
        try {
            // Get from SNS notification.
            $message = Message::fromRawPostData();

            // Check from SNS notification.
            if (! (new MessageValidator())->isValid($message)) {
                throw new \Exception('Error: Failed MessageValidator::isValid()');
            }

            if (! empty($message['SubscribeURL'])) {
                $this->BounceLogs->SubscriptionEndPoint($message);
            } else {
                $this->BounceLogs->store($message);
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }

        return null;
    }
}
