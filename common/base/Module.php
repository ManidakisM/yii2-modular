<?php

namespace common\base;

use \common\interfaces\ModuleBootstrapInterface;
use yii\base\Event;

/**
 * Class BaseModule
 * @package app\common
 */
abstract class Module extends \yii\base\Module implements ModuleBootstrapInterface
{
    public $routes;
    public $events;

    /**
     * @inheritdoc
     */
    public static function bootstrap($app)
    {
        return true;
    }

    public function init()
    {
        parent::init();
        $this->layout = 'main';
        $configPath = realpath($this->getBasePath() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'web.php');
        if ($configPath) {
            \Yii::configure($this, require($configPath));
        }
    }

    /**
     * Wrapper for event trigger
     *
     * @param $message
     * @param Event $event
     */
    public function sendMessage($message, Event $event) {
        \Yii::$app->trigger($message, $event);
    }
}