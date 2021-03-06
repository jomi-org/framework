<?php
/**
 * Created by PhpStorm.
 * User: macseem
 * Date: 1/25/15
 * Time: 11:23 PM
 */

namespace jf;


use jf\controllers\ErrorController;

class ConsoleApplication extends Application{

    protected function init(Config $config)
    {
        parent::init($config);
        $this->type = 'console';
    }

    protected function getControllerSuffixes()
    {
        return array(
            '',
            'Command',
            'Controller'
        );
    }

    protected function performError(\Exception $e)
    {
        $controller = new ErrorController();
        $this->response->perform($controller->actionConsoleException($e));
    }
}