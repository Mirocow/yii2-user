<?php

namespace yii\user\models;

use yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\helpers\Security;
use yii\user\models\User;

/**
 * Session model
 *
 * @property string $id
 * @property string $user_id
 * @property int $type
 * @property string $sid
 * @property string $create_time
 * @property string $consume_time
 * @property string $expire_time
 *
 * @property User $user
 */
class Session extends ActiveRecord {

    /**
     * @var int Key for email activations (=registering)
     */
    const TYPE_EMAIL_ACTIVATE = 'EMAIL_ACTIVATE';

    /**
     * @var int Key for email changes (=updating account page)
     */
    const TYPE_EMAIL_CHANGE = 'EMAIL_CHANGE';

    /**
     * @var int Key for password resets
     */
    const TYPE_PASSWORD_RESET = 'PASSWORD_RESET';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%session}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'type', 'sid'], 'required'],
            [['user_id'], 'integer'],
            [['type'], 'string'],
            [['create_time', 'consume_time', 'expire_time'], 'safe'],
            [['sid'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'sid' => 'User SID Key',
            'create_time' => 'Create Time',
            'consume_time' => 'Consume Time',
            'expire_time' => 'Expire Time',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\AutoTimestamp',
                'attributes' => [
                    // set only create_time because there is no update_time
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_time'],
                ],
                'timestamp' => function() { return date("Y-m-d H:i:s"); },
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        $this->ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $this->user_agent = Yii::$app->getRequest()->getUserAgent();
        return parent::beforeSave($insert);
    }    

    /**
     * Generate and return a new session
     *
     * @param int $userId
     * @param int $type
     * @param string $expireTime format of strtotime()
     *  Examples:
     *    - now
     *    - 10 September 2000
     *    - +1 day
     *    - +2 week
     *    - +1 month
     *    - +1 week 2 days 4 hours 2 seconds
     * @return static
     */
    public static function generate($userId, $type, $expireTime = null) {

        // attempt to find existing record
        // otherwise create new record
        $model = static::findActiveByUser($userId, $type);
        if (!$model) {
            $model = new static();
        }

        // set/update data
        $model->user_id = $userId;
        $model->type = $type;
        $model->create_time = date("Y-m-d H:i:s");
        if($expireTime){
          $model->expire_time = date("Y-m-d H:i:s", strtotime($expireTime, strtotime($model->create_time)));
        }
        $model->sid = Security::generateRandomKey();
        $model->save();
        
        if($model->hasErrors()){
            throw new HttpException(500, 'Can`t create session.');
        }

        return $model;
    }

    /**
     * Find an active session
     *
     * @param int $userId
     * @param int $type
     * @return static
     */
    public static function findActiveByUser($userId, $type) {
        
        return static::find()
            ->where([
                "user_id" => $userId,
                "type" => $type,
                "consume_time" => null,
            ])
            ->andWhere("([[expire_time]] >= NOW() or [[expire_time]] is NULL)")
            ->one();
            
    }

    /**
     * Find a session object for confirming
     *
     * @param string $hash     
     * @param string $sid
     * @param int|string $type
     * @return static
     */
    public static function findActiveByKey($hash, $sid, $type) {
        
        return static::find()
            // TODO: 9 Исправить когда решится вопрос по https://github.com/yiisoft/yii2/issues/1628            
            ->leftJoin('{{%user}}', '{{%user}}.id = `tbl_session`.user_id')
            //->joinWith(['user'])
            ->where([                
                "sid" => $sid,
                "type" => $type,
                //"consume_time" => null,
            ])
            ->andWhere("([[expire_time]] >= NOW() or [[expire_time]] is NULL)")
            ->andWhere("{{%user}}.hash = :hash", [':hash' => $hash])
            ->one();
            
    }

    /**
     * Consume session record
     *
     * @return static
     */
    public function consume() {
        
        $this->consume_time = date("Y-m-d H:i:s");
        $this->type = self::TYPE_PASSWORD_RESET;
        $this->save(false);
        return $this;
        
    }

    /**
     * Expire session record
     *
     * @return static
     */
    public function expire() {
        
        $this->expire_time = date("Y-m-d H:i:s");
        $this->save(false);
        return $this;
        
    }
}
