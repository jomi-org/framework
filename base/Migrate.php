<?php
/**
 * Created by PhpStorm.
 * User: macseem
 * Date: 1/31/15
 * Time: 5:13 PM
 */

namespace jf\base;


use jf\Controller;
use jf\Core;
use jf\Exception;
use jf\interfaces\IMigrate;
use jf\Migration;

abstract class Migrate extends Controller implements IMigrate{

    public $migrationPath;

    protected $migrationHistory = array();

    public function beforeAction($action)
    {
        $this->migrationPath = Core::$appDir.'/migrations';
        $this->init();
        parent::beforeAction($action);
    }

    public function actionDo($name)
    {
        return $this->getMigration($name)->up();
    }

    public function actionUp($count = 0)
    {
        $migrations = $this->getNewMigrations();
        if(count($migrations) == 0)
            return 'All migrations were up to date'.PHP_EOL;

        if($count == 0)
            $count = count($migrations);
        foreach($migrations as $key => $migration) {
            if($count--==0)
                break;
            $this->migrateUp($key);
            echo 'Migration '.$key.' was upped'.PHP_EOL;
        }
        return 'All migrations are up to date'.PHP_EOL;
    }

    public function actionDown($count = 0)
    {
        $migration = end($this->migrationHistory);
        if($count == 0)
            $count = count($this->migrationHistory)-1;
        for(;$migration && $count>0;$count--,$migration = prev($this->migrationHistory)) {
            $this->migrateDown($migration['name']);
            echo 'Migration '.$migration['name'].' was downed'.PHP_EOL;

        }
    }

    /**
     * @param $name
     */
    public function actionCreate($name)
    {
        $view = $this->getView();
        $file = Core::$jfDir.DIRECTORY_SEPARATOR.'templates/new_migration.php';
        $className = 'migration_'.time().'_'.$name;
        $content = $view->render($file,array('name' => $className,));
        $fp = fopen(Core::$appDir.'/migrations/'.$className.'.php',"w");
        fwrite($fp,$content);
        fclose($fp);
    }

    public function actionHistory()
    {
        foreach($this->migrationHistory as $migration) {
            echo $migration['time']. ' ' . $migration['name'].PHP_EOL;
        }
    }

    protected function migrateUp($name)
    {
        $result = $this->getMigration($name)->up();
        if($result === false)
            throw new Exception("Migration returns FALSE. Can't continue.", Core::EXCEPTION_ERROR_CODE);
        $this->markMigration($name);
        return $result;
    }

    protected function migrateDown($name)
    {
        $result = $this->getMigration($name)->down();
        if($result === false)
            throw new Exception("Migration returns FALSE. Can't continue.", Core::EXCEPTION_ERROR_CODE);
        $this->unMarkMigration($name);
        return $result;
    }
    protected abstract function init();

    /**
     * @param $name
     *
     * @return Migration
     */
    protected abstract function getMigration($name);
    protected abstract function markMigration($name);
    protected abstract function unMarkMigration($name);

    /**
     * @return array
     */
    protected abstract function getNewMigrations();

}