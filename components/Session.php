<?php

class Session
{

    private $user_id;
    private static $instance;

    private function __construct()
    {
        $this->restoreUserIdFromSession();
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }

    /**
     * @return Session
     */
    public static function getInstance(): Session
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $user
     */
    public function login($user)
    {
        $this->user_id = $user->id;
        $_SESSION['user_id'] = $user->id;
    }

    public function logout()
    {
        session_destroy();
    }

    /**
     * @return bool
     */
    public function isGuest()
    {
        return empty($this->user_id);
    }

    public function restoreUserIdFromSession()
    {
        $this->user_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : null;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        if (!$this->isGuest()) {
            return User::model()->findByAttributes(['id' => $this->user_id]);
        }
    }
}
