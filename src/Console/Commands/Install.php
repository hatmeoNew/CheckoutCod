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

class Install extends CommandInterface 

{
    protected $signature = 'CheckoutCod:install';

    protected $description = 'Install CheckoutCod an app';

    public function getAppVer() {
        return config("CheckoutCod.ver");
    }

    public function getAppName() {
        return config("CheckoutCod.name");
    }

    public function handle()
    {
        $this->info("Install app: CheckoutCod");
        if (!$this->confirm('Do you wish to continue?')) {
            // ...
            $this->error("App CheckoutCod Install cannelled");
            return false;
        }
    }
}