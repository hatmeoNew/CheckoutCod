<?php
/**
 * 
 * This file is auto generate by Nicelizhi\Apps\Commands\Create
 * @author Steve
 * @date 2024-10-29 19:01:49
 * @link https://github.com/xxxl4
 * 
 */
namespace NexaMerchant\CheckoutCod\Console\Commands;

use NexaMerchant\Apps\Console\Commands\CommandInterface;

class UnInstall extends CommandInterface 

{
    protected $signature = 'CheckoutCod:uninstall';

    protected $description = 'Uninstall CheckoutCod an app';

    public function getAppVer() {
        return config("CheckoutCod.ver");
    }

    public function getAppName() {
        return config("CheckoutCod.name");
    }

    public function handle()
    {
        if (!$this->confirm('Do you wish to continue?')) {
            // ...
            $this->error("App CheckoutCod UnInstall cannelled");
            return false;
        }
    }
}