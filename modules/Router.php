<?php
/**
 * Created by PhpStorm.
 * User: macseem
 * Date: 1/24/15
 * Time: 2:32 PM
 */

namespace jf\modules;
use jf\Core;
use jf\Exception;
use jf\base\Module;
use jf\helpers\UriHelper;

class Router extends Module{

    /** @var  string */
    public $uri;
    /** @var string
     *  TODO: get module from route
     */
    public $module;
    /** @var  string */
    public $controller;
    /** @var  string */
    public $action;
    /** @var  string */
    public $route;

    /**
     * @return static
     * @throws Exception
     */
    public function init()
    {
        if(empty($this->_config['default']['controller']) || empty($this->_config['default']['action']))
            throw new Exception('Please add default controller or action to your router config');
        $request = Core::$app->request;
        $this->uri = $request->uri;
        $this->resolve();
    }


    private function resolve()
    {
        $tokens = explode('?',$this->uri);
        $uri = $tokens[0];
        foreach($this->config['routes'] as $route => $params) {
            if(!preg_match($route,$uri,$matches))
                continue;
            unset($matches[0]);
            foreach($matches as $key => $match) {
                $this->{$params['matches'][$key]} = UriHelper::hyphenToCamel($match);
            }
            if(empty($this->controller)){
                $this->controller = '$default';
                if(!empty($params['default']['controller']))
                    $this->controller = $params['default']['controller'];
            }
            if(empty($this->action)){
                $this->action = '$default';
                if(!empty($params['default']['action']))
                    $this->action = $params['default']['action'];
            }
            if(empty($this->module)){
                $this->module = '';
                if(!empty($params['default']['module']))
                    $this->module = $params['default']['module'];
            }
            return true;
        }
        return false;
/*
            $uri = trim($this->uri,"/");
        $parts = explode('/',$uri);
        if(empty($parts[0])) {
            $this->controller = $this->_config['default']['controller'];
            $this->action = $this->_config['default']['action'];
            return true;
        }
        $this->controller = $parts[0];
        if(strpos($this->controller,'-')){
            $controllerParts = explode('-',$this->controller);
            $this->controller = '';
            foreach($controllerParts as $controllerPart) {
                $this->controller.=ucfirst($controllerPart);
            }
        }
        if(count($parts) == 1) {
            if(empty($this->_config[$this->controller]['default']['action']))
                throw new Exception("Please set default action for ".$this->controller." controller in config.",Core::EXCEPTION_ERROR_CODE);
            $this->action = $this->_config[$this->controller]['default']['action'];
            return true;
        }
        $this->action = $parts[1];
        if(strpos($this->action,'-')){
            $actionParts = explode('-',$this->action);
            $this->action = '';
            foreach($actionParts as $actionPart) {
                $this->action.=ucfirst($actionPart);
            }
        }
        return true;*/
    }

    public function getDefaultRoute()
    {
        if(empty($this->_config['default']['route']))
            throw new Exception("Default route is empty. Please set it in configs");
        return $this->_config['default']['route'];
    }

    /*    private function parseRoute($route)
        {
            $controller = '';
            $action = '';
            if(isset($route['controller'])){
                $controller = $route['controller'];
                $afterController = $route['pattern'];
                $uri = $this->uri;
            } else {
                $explodeByController = explode('<controller>', $route['pattern']);
                $afterController = $explodeByController[1];
                $beforeController = $explodeByController[0];
                $uri = substr($this->uri, strlen($beforeController));
            }
            if(isset($route['action'])){
                $action = $route['action'];
                $afterAction
            } else {
                $explodeByAction = explode('<action>', $afterController);
                $beforeAction = $explodeByAction[0];
                $afterAction = $explodeByAction[1];

                $uri = substr($uri,0,strlen($afterAction) * (-1));
                $final = explode($beforeAction,$uri);

            }

            return true;
        }*/
}