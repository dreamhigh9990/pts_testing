<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii;
use bs\dbManager\models\Dump;
use bs\dbManager\models\Restore;

class CronController extends Controller
{
    public function actionDatabaseBackupAndRemove() {
        date_default_timezone_set('Australia/Melbourne');
        $dbInfo = Yii::$app->db;
        if(!empty($dbInfo->dsn) && !empty($dbInfo->username) && isset($dbInfo->password)){
            $dsn = $dbInfo->dsn;
            $host = $this->getDsnAttribute('host', $dsn);
            $port = $this->getDsnAttribute('port', $dsn);
            $username = $dbInfo->username;
            $password = $dbInfo->password;
            $database = $this->getDsnAttribute('dbname', $dsn);
        } else {
            $host = 'localhost';
            $port = '3306';
            $username = 'root';
            $password = 'MqiyepeWDkiT9UbZ';
            $database = 'pipe_test_v2';
        }
        // 0 1 * * * /usr/bin/php /var/www/html/testing_v2/yii cron/database-backup-and-remove
        // $path = getcwd().'/backups/';
        // $path = dirname(__FILE__).'/../backups/';
        $backupDatabase = $database.'_full_default_'.date('Y-m-d_H-i-s').'.sql';
        $backupPath = dirname(__FILE__).'/../backups/';
        $fullPathWithDbName = $backupPath.$backupDatabase;
        
        //! create backup for mysql
        try {
            $command = "mysqldump --host=".$host." --port=".$port." --user=".$username." --password='".$password."' ".$database." > ".$fullPathWithDbName;
            exec($command);
        } catch(\Exception $e) {
            echo "Error: ".$e->getMessage();
        }

        //! remove backup mysql file, which are older than 60 days
        $days = 60;
        $path = dirname(__FILE__).'/../backups/';
        
        // Open the directory  
        if ($handle = opendir($path)) {
            // Loop through the directory  
            while (false !== ($file = readdir($handle))) {
                // Check the file we're doing is actually a file  
                if (is_file($path.$file)) {
                    // Check if the file is older than X days old  
                    if (filemtime($path.$file) < ( time() - ( $days * 24 * 60 * 60 ) ) ) {
                        // Do the deletion  
                        unlink($path.$file);  
                    }  
                }
            }
        }
    }

    protected function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}