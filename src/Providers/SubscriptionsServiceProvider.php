<?php

declare(strict_types=1);

namespace Abovesky\Subscription\Providers;

use Abovesky\Subscription\Models\Plan;
use Illuminate\Support\ServiceProvider;
use Abovesky\Subscription\Traits\ConsoleTools;
use Abovesky\Subscription\Models\PlanFeature;
use Abovesky\Subscription\Models\PlanSubscription;
use Abovesky\Subscription\Models\PlanSubscriptionUsage;
use Abovesky\Subscription\Console\Commands\MigrateCommand;
use Abovesky\Subscription\Console\Commands\PublishCommand;
use Abovesky\Subscription\Console\Commands\RollbackCommand;

class SubscriptionsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.abovesky.subscription.migrate',
        PublishCommand::class => 'command.abovesky.subscription.publish',
        RollbackCommand::class => 'command.abovesky.subscription.rollback',
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'abovesky.subscription');

        // Bind eloquent models to IoC container
        $this->registerModels([
            'abovesky.subscription.plan' => Plan::class,
            'abovesky.subscription.plan_feature' => PlanFeature::class,
            'abovesky.subscription.plan_subscription' => PlanSubscription::class,
            'abovesky.subscription.plan_subscription_usage' => PlanSubscriptionUsage::class,
        ]);

        // Register console commands
        $this->registerCommands($this->commands);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish Resources
        $this->publishesConfig('abovesky/laravel-subscription');
        $this->publishesMigrations('abovesky/laravel-subscription');
        ! $this->autoloadMigrations('abovesky/laravel-subscription') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
