<?php

namespace CSanquer\Silex\PdoServiceProvider\Provider;

use CSanquer\Silex\PdoServiceProvider\Config\PdoConfigFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Database PDO Provider.
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class PDOServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['pdo.dbs.options'] = array();
        $app['pdo.dbs.default'] = null;
        $app['pdo.dbs.default_options'] = array(
            'driver' => 'sqlite',
            'options' => array(),
        );

        $app['pdo.dbs'] = $app->share(function ($app) {
            if (!empty($app['pdo.dbs.options'])) {
                $factory = new PdoConfigFactory();
                $dbs = new \Pimple();
                foreach ($app['pdo.dbs.options'] as $name => $params) {
                    if (!isset($app['pdo.dbs.default']) || $app['pdo.dbs.default'] == null) {
                        $app['pdo.dbs.default'] = $name;
                    }

                    if (empty($params)) {
                        $params = $app['pdo.dbs.default_options'];
                    }

                    $cfg = $factory->createConfig($params);
                    $dbs[$name] = $cfg->connect($params);
                }

                return $dbs;
            }
        });
        
        // shortcuts for the "first" DB
        $app['pdo'] = $app->share(function ($app) {
            $dbs = $app['pdo.dbs'];

            return $dbs[$app['pdo.dbs.default']];
        });

    }

    public function boot(Application $app)
    {
        
    }

}