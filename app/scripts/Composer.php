<?php

namespace app\scripts;

use Composer\Script\Event;

class Composer{

    public static function postPackageInstall(Event $event){
        $installed_package = $event->getOperation()->getPackage();
        Composer::createPublicSymlinks($installed_package);
    }

    public static function postPackageUpdate(Event $event){
        $updated_package = $event->getOperation()->getInitialPackage();
        Composer::createPublicSymlinks($updated_package);
    }

    public static function createPublicSymlinks($package){
        $path = 'public/packages/tdt/';
        @mkdir($path, 0755, true);

        if(preg_match("/tdt\/(.*)-[0-9]*-dev/", $package, $matches)){
            $sub_package = $matches[1];
            $vendor_public_folder = 'vendor/tdt/'. $sub_package . '/public';

            if(file_exists($vendor_public_folder)){
                // Create symlink
                @symlink('../../../' .$vendor_public_folder, $path. $sub_package);
                echo "Created symlink to the package public folder\n";
            }
        }
    }

}