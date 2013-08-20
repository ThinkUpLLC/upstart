<?php

class UpgradeApplicationController extends Controller {

    public function control() {
        echo "<pre>";
        // TODO Check to make sure all Dispatch workers are shut down

        // TODO Git pull user_master and chameleon installations

        // Get most recent git commit from user_master and chameleon installs -- they should match
        $installer = new AppInstaller();
        $commit_hash = $installer->getMasterInstallCommitHash();

        // Set counters to 0
        $successful_upgrades = 0;
        $failed_upgrades = 0;

        // Get in the right directory to exec the upgrade
        $cfg = Config::getInstance();
        $master_app_source_path = $cfg->getValue('master_app_source_path');
        if (!chdir($master_app_source_path.'/install/cli/thinkupllc-chameleon-upgrader') ) {
            throw new Exception("Could not chdir to ".
            $master_app_source_path.'/install/cli/thinkupllc-chameleon-upgrader');
        }

        // Initialize upgrade call parameters that are the same for every installation
        /*
        * {"installation_name":"steveklabnik", "timezone":"America/Los_Angeles", "db_host":"localhost",
        * "db_name":"thinkupstart_steveklabnik", "db_socket":"/tmp/mysql.sock",  "db_port":""}
        */
        $upgrade_params_array = array(
            'installation_name'=>null,
            'timezone'=>$cfg->getValue('dispatch_timezone'),
            'db_host'=>$cfg->getValue('db_host'),
            'db_name'=>null,
            'db_socket'=>$cfg->getValue('dispatch_socket'),
            'db_port'=>$cfg->getValue('db_port')
        );

        // Get 10 installations that are active but haven't been upgraded to latest hash
        $dao = new UserRouteMySQLDAO();
        $installs_to_upgrade = $dao->getInstallsToUpgrade($commit_hash);

        // While there are installations that need to be upgraded:
        while (sizeof($installs_to_upgrade) > 0) {
            foreach ($installs_to_upgrade as $install_to_upgrade) {
                // Call chameleon upgrader at the command line with appropriate JSON
                $installation_name = $install_to_upgrade['twitter_username'];
                $database_name = $install_to_upgrade['database_name'];
                $upgrade_params_array['installation_name'] = $installation_name;
                $upgrade_params_array['db_name'] = $database_name;
                $upgrade_params_json = json_encode($upgrade_params_array);

                // Capture returned JSON
                if (!exec("php upgrade.php '".$upgrade_params_json."'", $upgrade_status_json) ) {
                    throw new Exception('Unable to exec php upgrade.php '.$upgrade_params_json);
                }

                print_r($upgrade_status_json);
                $upgrade_status_array = JSONDecoder::decode($upgrade_status_json[0], true);

                // DEBUG start
                echo "php upgrade.php '".$upgrade_params_json."'";
                print_r($upgrade_status_array);
                // DEBUG end

                if ($upgrade_status_array['migration_success'] === true) {
                    // If success, store git commit in 2 tables, message, and status in install_log
                    $dao->updateCommitHash($install_to_upgrade['id'], $commit_hash);
                    $dao->insertLogEntry($install_to_upgrade['id'], $commit_hash, 1,
                    $upgrade_status_array['migration_message']);
                    $successful_upgrades++;
                } else {
                    // If error, set inactive, and store message, status, commit in install_log
                    $dao->setActive($install_to_upgrade['id'], 0);
                    $dao->insertLogEntry($install_to_upgrade['id'], $commit_hash, 0,
                    $upgrade_status_array['migration_message']);
                    $failed_upgrades++;
                }
                $upgrade_status_json = null;
                $upgrade_status_array = null;
            }
            // Get another 10 installations that are active but haven't been upgraded to latest hash
            $installs_to_upgrade = $dao->getInstallsToUpgrade($commit_hash);
        }
        // Output how many installs error'ed, how many success
        echo $successful_upgrades ." SUCCESSFUL and ".$failed_upgrades." FAILED upgrades to ".$commit_hash.
        " completed.";

        // TODO Restart Dispatch workers
    }
}