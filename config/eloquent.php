<?php
/**
 * Author: Stephanie Ranegar
 * Date: 5/24/2022
 * File: eloquent.php
 * Description: Eloquent file
 */

use DI\Container;
use Illuminate\Database\Capsule\Manager;

return static function (Container $container) {
    // boot eloquent
    $capsule = new Manager;
    $capsule->addConnection($container->get('settings')['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
};