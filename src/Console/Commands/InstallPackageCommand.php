<?php

namespace Hexters\Ladmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InstallPackageCommand extends Command
{


    protected $option = 'Modules\\';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ladmin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install ladmin package';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Installing...');

        $this->checkModuleFoler();

        $this->overideComposerJson();

        $this->call('vendor:publish', [
            '--tag' => 'ladmin-config',
            '--force' => true
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'ladmin-module',
            '--force' => true
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'ladmin-menu',
            '--force' => true
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'ladmin-asset',
            '--force' => true
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'ladmin-stub',
            '--force' => true
        ]);

        if (!Schema::hasTable('notifications')) {
            $this->call('notifications:table');
        }
        
        $this->info('Please wait a moment for dump-autoload...');

        exec( 'composer dump-autoload' );
        
        $this->line('');
        
        $this->line('----------------------------------------------------');
        $this->line('# 1. Now please run migrate, for install ladmin database tables');
        $this->info('php artisan migrate --seed');
        
        $this->line('');

        $this->line('# 2. And run seed ladmin module, for assign role and permission to existing user.');
        $this->info('php artisan module:seed Ladmin');
        $this->line('----------------------------------------------------');
        
        $this->line('');
        $this->info('Instalation finished successfully. 🏁');
        $this->line('');

        $this->line('----------------------------------------------------');
        $this->info('Visit : ' . url('/administrator'));
        $this->line('----------------------------------------------------');
    }

    protected function overideComposerJson()
    {

        $composer = base_path('composer.json');
        $decode = json_decode(str_replace(PHP_EOL, '', file_get_contents($composer)), true);

        if (!array_key_exists($this->option, $decode['autoload']['psr-4'])) {
            $getPsr4 = $decode['autoload']['psr-4'];
            $newPsr4 = array_merge($getPsr4, [$this->option => $this->option]);
            $autoload = [
                'autoload' => [
                    'psr-4' => $newPsr4
                ]
            ];
            $merge = array_merge($decode, $autoload);
            $encode = json_encode($merge, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            file_put_contents($composer, $encode);

            $this->info(rtrim($this->option, '\\') . ' autoload successfully added 👍');
        } else {
            $this->info('Autoload is ready 👍');
        }
    }

    protected function checkModuleFoler()
    {
        $folder = base_path('Modules');
        if (!is_dir($folder)) {
            mkdir($folder);
            $this->info('Module folder has been created 👍');
        } else {
            $this->info('Module folder is ready 👍');
        }
    }
}
