<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SmtpSetting;
use Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    { 
        if (\Schema::hasTable('smtp_settings')) {

            $smtpsetting = SmtpSetting::first();
            if ($smtpsetting) {
                $data = [

                    'driver' => $smtpsetting->mailer,
                    'host' => $smtpsetting->host,
                    'port' => $smtpsetting->post,
                    'username' => $smtpsetting->username,
                    'password' => $smtpsetting->password,
                    'encryption' => $smtpsetting->encryption, 
                    'from' => [
                        'address' => $smtpsetting->from_address,
                        'name' => 'RealEstate Nepal'
                    ]
                ];
                Config::set('mail',$data);
            }
            
            // Set default mail from name if not already set in config/mail.php
            if (empty(config('mail.from.name'))) {
                config(['mail.from.name' => 'RealEstate Nepal']);
            }
        } // End If 
    }
}
