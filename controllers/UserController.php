<?php

class UserController extends BaseController
{

    public function actionLogin()
    {

        $request = Request::getInstance();
        if (!Session::getInstance()->isGuest()) {
            $request->redirect('/');
        }
        if ($request->getIsPostRequest()) {
            $login = $request->getParam('login');
            $password = $request->getParam('password');
            if (!empty($login) && !empty($password)) {
                $userModel = User::model();
                $password = $userModel->getHash($password);
                $user = User::model()->findByAttributes(compact('login', 'password'));
                if (!empty($user)) {
                    Session::getInstance()->login($user);
                    Request::getInstance()->redirect('/');
                }
            }
        }
        $this->render('login');
    }

    public function actionLogout()
    {
        Session::getInstance()->logout();
        Request::getInstance()->redirect('/');
    }
}
