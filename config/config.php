<?php

declare(strict_types=1);

return [

    // Manage autoload migrations
    'autoload_migrations' => true,

    // Subscriptions Database Tables
    'tables' => [

        'plans' => 'plans',
        'plan_features' => 'plan_features',
        'plan_subscriptions' => 'plan_subscriptions',
        'plan_subscription_usage' => 'plan_subscription_usage',

    ],

    // Subscriptions Models
    'models' => [

        'plan' => \Abovesky\Subscription\Models\Plan::class,
        'plan_feature' => \Abovesky\Subscription\Models\PlanFeature::class,
        'plan_subscription' => \Abovesky\Subscription\Models\PlanSubscription::class,
        'plan_subscription_usage' => \Abovesky\Subscription\Models\PlanSubscriptionUsage::class,

    ],

];
