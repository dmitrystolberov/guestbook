<?php

class Request
{

    private static $instance;
    private $requestUri;

    private function __construct()
    {
        $this->normalizeRequest();
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
     * @return Request
     */
    public static function getInstance(): Request
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run()
    {
        $requestUri = $this->getRequestUri();
        $parts = explode('/', $requestUri);
        if (!empty($parts[1])) {
            $contollerName = ucfirst($parts[1]) . 'Controller';
            if (!empty($parts[2])) {
                $actionName = 'action' . ucfirst($parts[2]);
                if (class_exists($contollerName)) {
                    $controller = new $contollerName();
                    if (method_exists($controller, $actionName)) {
                        $controller->$actionName();
                    } else {
                        throw new Exception('Wrong response', 404);
                    }
                } else {
                    throw new Exception('Wrong response', 404);
                }
            } else {
                throw new Exception('Wrong response', 404);
            }
        } else {
            $controller = new CommentController();
            $controller->actionList();
        }
    }

    /**
     * Normalizes the request data.
     * This method strips off slashes in request data if get_magic_quotes_gpc() returns true.
     */
    protected function normalizeRequest()
    {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            if (isset($_GET)) {
                $_GET = $this->stripSlashes($_GET);
            }
            if (isset($_POST)) {
                $_POST = $this->stripSlashes($_POST);
            }
            if (isset($_REQUEST)) {
                $_REQUEST = $this->stripSlashes($_REQUEST);
            }
            if (isset($_COOKIE)) {
                $_COOKIE = $this->stripSlashes($_COOKIE);
            }
        }
    }

    /**
     * Strips slashes from input data.
     * This method is applied when magic quotes is enabled.
     * @param mixed $data input data to be processed
     * @return mixed processed data
     */
    public function stripSlashes(&$data)
    {
        if (is_array($data)) {
            if (count($data) == 0) {
                return $data;
            }
            $keys = array_map('stripslashes', array_keys($data));
            $data = array_combine($keys, array_values($data));
            return array_map(array($this, 'stripSlashes'), $data);
        } else {
            return stripslashes($data);
        }
    }

    /**
     * Returns the named GET or POST parameter value.
     * If the GET or POST parameter does not exist, the second parameter to this method will be returned.
     * If both GET and POST contains such a named parameter, the GET parameter takes precedence.
     * @param string $name the GET parameter name
     * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
     * @return mixed the GET parameter value
     * @see getQuery
     * @see getPost
     */
    public function getParam($name, $defaultValue = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
    }

    /**
     * Returns whether this is a POST request.
     * @return boolean whether this is a POST request.
     */
    public function getIsPostRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
                $this->requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            }
            elseif (isset($_SERVER['REQUEST_URI'])) {
                $this->requestUri = $_SERVER['REQUEST_URI'];
                if (!empty($_SERVER['HTTP_HOST'])) {
                    if (strpos($this->_requestUri, $_SERVER['HTTP_HOST']) !== false) {
                        $this->requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
                    }
                } else {
                    $this->requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $this->_requestUri);
                }
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
                $this->requestUri = $_SERVER['ORIG_PATH_INFO'];
            }
            if (($pos = strpos($this->requestUri, '?')) !== false) {
                $this->requestUri = substr($this->requestUri, 0, $pos);
            }
        }
        return $this->requestUri;
    }

    /**
     * @param $url
     */
    public function redirect($url)
    {
        header('Location:' . $url);
    }

}
