<?php

namespace modules\auth\controllers;

use modules\auth\models\Confirm;
use modules\auth\models\Login;
use modules\auth\models\Register;
use modules\auth\models\PasswordReset;
use modules\auth\components\ModuleController;

/**
 * Class IndexController
 * @package app\modules\auth\controllers
 */
class IndexController extends ModuleController
{
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new Login();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Login::logout();
        return $this->goHome();
    }

    public function actionRegister()
    {
        $model = new Register();
        if ($model->load(\Yii::$app->request->post())) {
            if ($user = $model->register()) {
                return $this->render('register', [
                    'model' => $model,
                    'needConfirmation' => true
                ]);
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * @param $token
     * @return string|\yii\web\Response
     */
    public function actionConfirm($token)
    {
        return $this->passwordConfirm($token);
    }

    /**
     * Reset password action
     * @param null $token
     * @return string|\yii\web\Response
     */
    public function actionPasswordReset($token = null)
    {
        if ($token) {
            return $this->passwordConfirm($token, Confirm::SCENARIO_PASSWORD_RESET);
        }

        return $this->passwordResetRequest();
    }

    /**
     * Reset form
     * @return string
     * @throws \yii\base\Exception
     */
    private function passwordResetRequest()
    {
        $model = new PasswordReset();
        $reset = false;
        if ($model->load(\Yii::$app->request->post()) && $model->resetPassword()) {
            $reset = true;
        }

        return $this->render('passwordReset', compact('model', 'reset'));
    }

    private function passwordConfirm($token, $scenario = null)
    {
        $model = new Confirm($token, ['scenario' => $scenario]);
        if ($model->load(\Yii::$app->request->post()) && $user = $model->confirm()) {
            if (\Yii::$app->getUser()->login($user)) {
                return $this->goBack();
            }
        }

        return $this->render('confirm', compact('model'));
    }
}