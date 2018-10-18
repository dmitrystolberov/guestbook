<?php

class BaseController
{

    /**
     * @param $view
     * @param array $parameters
     */
    public function render($view, $parameters = [])
    {
        $dir = 'views' . DIRECTORY_SEPARATOR . strtolower(str_replace('Controller', '', get_class($this)));
        if (is_array($parameters)) {
            extract($parameters, EXTR_PREFIX_SAME, 'parameters');
        }
        ob_start();
        ob_implicit_flush(false);
        require ($dir . DIRECTORY_SEPARATOR . $view . '.php');
        $content = ob_get_clean();
        require ('views' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'layout.php');
    }
}
