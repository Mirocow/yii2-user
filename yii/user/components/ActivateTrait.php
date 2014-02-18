<?php

namespace yii\user\components;

use yii;
use yii\base\Event;
use yii\base\Model;
  
trait ActivateTrait {

    public $hash = '';
    
    public $sid = '';    
    
    public function beforeValidate(){        
        
        if($hash = Yii::$app->request->getQueryParam('hash')){
            $this->hash = $hash;
        }
        
        if($sid = Yii::$app->request->getQueryParam('sid')){
            $this->sid = $sid;
        }
        
        if($this->hash && $this->sid){
            /** @var yii/base/Model $this*/
            $this->setScenario('hash');
            
            // Add validator
            // [['hash', 'sid'], 'validateHash', 'on' => ['account']],            
        }
        
        return parent::beforeValidate();
        
    }
    
    public function validateHash() {
        $i = 1;
    }    
    
}
