<?php

namespace Boris\Loader\Provider;

class Drupal extends AbstractProvider
{
    public $name = 'drupal';

    public function assertDir($dir)
    {
        return is_file("$dir/core/lib/Drupal.php");
    }

    public function initialize(\Boris\Boris $boris, $dir)
    {
        parent::initialize($boris, $dir);

        chdir($dir);

        require_once "$dir/core/includes/bootstrap.inc";

        drupal_bootstrap(DRUPAL_BOOTSTRAP_CONFIGURATION);

        $kernel = new \Drupal\Core\DrupalKernel(
            'dev',
            drupal_classloader(),
            false
        );
        $kernel->boot();

        drupal_bootstrap(DRUPAL_BOOTSTRAP_CODE);

        $boris->onStart(function ($worker, $vars) use ($kernel) {
            $worker->setLocal('kernel', $kernel);
            $worker->setLocal('container', $kernel->getContainer());
        });
    }
}
