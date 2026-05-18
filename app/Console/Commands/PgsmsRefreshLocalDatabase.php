<?php

namespace App\Console\Commands;

use Database\Seeders\PgsmsLocalSeeder;
use Illuminate\Console\Command;

class PgsmsRefreshLocalDatabase extends Command
{
    protected $signature = 'pgsms:refresh-local';

    protected $description = 'Rebuild database/pgsms.sqlite using migrations/pgsms and seed HoD demo data';

    public function handle(): int
    {
        $this->call('migrate:fresh', [
            '--database' => 'sqlite_pgsms',
            '--path' => 'database/migrations/pgsms',
            '--force' => true,
        ]);

        $this->call('db:seed', [
            '--class' => PgsmsLocalSeeder::class,
            '--database' => 'sqlite_pgsms',
        ]);

        $this->components->info('Local PGSMS DB ready at '.database_path('pgsms.sqlite').'. Login: hod@local.test / password');

        return self::SUCCESS;
    }
}
