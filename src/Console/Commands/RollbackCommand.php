<?php

declare(strict_types=1);

namespace Abovesky\Subscription\Console\Commands;

use Illuminate\Console\Command;

class RollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abovesky:rollback:subscription {--f|force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback Abovesky Subscription Tables.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->alert($this->description);

        $path = config('abovesky.subscription.autoload_migrations') ?
            'vendor/abovesky/laravel-subscription/database/migrations' :
            'database/migrations/abovesky/laravel-subscription';

        if (file_exists($path)) {
            $this->call('migrate:reset', [
                '--path' => $path,
                '--force' => $this->option('force'),
            ]);
        } else {
            $this->warn('No migrations found! Consider publish them first: <fg=green>php artisan abovesky:publish:subscription</>');
        }

        $this->line('');
    }
}
