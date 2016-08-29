<?php
namespace AntiBounce\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * Class UsersTable
 *
 * @package AntiBounce\Model\Table
 *
 * @property BounceLogsTable BounceLogs
 */
class UsersTable extends Table
{
    private $config;

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * You can use this method to define associations, attach behaviors
     * define validation and do any other initialization logic you need.
     *
     * ```
     *  public function initialize(array $config)
     *  {
     *      $this->belongsTo('Users');
     *      $this->belongsToMany('Tagging.Tags');
     *      $this->primaryKey('something_else');
     *  }
     * ```
     *
     * @param array $config Configuration options passed to the constructor
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->config = Configure::read('AntiBounce');

        $this->table($this->config['settings']['email']['table']);
        $this->addBehavior('Timestamp');
        $this->hasMany("BounceLogs", [
            "foreignKey" => "target_id",
            "bindingKey" => $this->config['settings']['email']['key']
        ]);
    }

    /**
     * @param $email
     * @return mixed
     * @throws \Exception
     */
    public function getKeyByEmail($email)
    {
        $setting = $this->config['settings']['email'];
        if (empty($setting['mailField']) || empty($setting['key'])) {
            throw new \Exception;
        }

        $key = $this->find()
            ->where([$setting['mailField'] => $email])
            ->firstOrFail()
            ->{$setting['key']};

        return $key;
    }

    /**
     * @param $email
     * @return bool|\Cake\Datasource\EntityInterface|mixed
     * @throws RecordNotFoundException|\Exception
     */
    public function updateFields($email)
    {
        $key = $this->getKeyByEmail($email);
        $Model = TableRegistry::get($this->config['settings']['updateFields']['model']);

        $entity = $Model->get($key);
        foreach ($this->config['settings']['updateFields']['fields'] as $fieldKey => $fieldValue) {
            $entity->{$fieldKey} = $fieldValue;
            $entity->dirty($fieldKey, true);
        }

        return $Model->save($entity, ['atomic' => false]);
    }
}
