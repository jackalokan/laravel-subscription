<?php

declare(strict_types=1);

namespace Abovesky\Subscription\Models;

use Spatie\Sluggable\SlugOptions;
use Abovesky\Subscription\Traits\HasSlug;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Abovesky\Subscription\Traits\ValidatingTrait;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Abovesky\Subscription\Models\Plan.
 *
 * @property int                 $id
 * @property string              $slug
 * @property array               $name
 * @property array               $description
 * @property bool                $is_active
 * @property float               $price
 * @property int                 $trial_period
 * @property string              $trial_interval
 * @property int                 $invoice_period
 * @property string              $invoice_interval
 * @property int                 $active_subscribers_limit
 * @property int                 $sort_order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Abovesky\Subscription\Models\PlanFeature[]      $features
 * @property-read \Illuminate\Database\Eloquent\Collection|\Abovesky\Subscription\Models\PlanSubscription[] $subscriptions
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan ordered($direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereActiveSubscribersLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereInvoiceInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereInvoicePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereTrialInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereTrialPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Abovesky\Subscription\Models\Plan whereDeletedAt($value)
 * @mixin \Eloquent
 */
class Plan extends Model implements Sortable
{
    use HasSlug;
    use SortableTrait;
    use ValidatingTrait;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active',
        'price',
        'trial_period',
        'trial_interval',
        'invoice_period',
        'invoice_interval',
        'active_subscribers_limit',
        'sort_order',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'slug' => 'string',
        'is_active' => 'boolean',
        'price' => 'float',
        'trial_period' => 'integer',
        'trial_interval' => 'string',
        'invoice_period' => 'integer',
        'invoice_interval' => 'string',
        'active_subscribers_limit' => 'integer',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'validating',
        'validated',
    ];

    /**
     * The sortable settings.
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort_order',
    ];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the model should throw a
     * ValidationException if it fails validation.
     *
     * @var bool
     */
    protected $throwValidationExceptions = true;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('abovesky.subscription.tables.plans'));
        $this->setRules([
            'slug' => 'required|alpha_dash|max:150|unique:'.config('abovesky.subscription.tables.plans').',slug',
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'price' => 'required|numeric',
            'trial_period' => 'sometimes|integer|max:100000',
            'trial_interval' => 'sometimes|in:hour,day,week,month',
            'invoice_period' => 'sometimes|integer|max:100000',
            'invoice_interval' => 'sometimes|in:hour,day,week,month',
            'sort_order' => 'nullable|integer|max:100000',
            'active_subscribers_limit' => 'nullable|integer|max:100000',
        ]);
    }

    /**
     * Get the options for generating the slug.
     *
     * @return \Spatie\Sluggable\SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                          ->doNotGenerateSlugsOnUpdate()
                          ->generateSlugsFrom('name')
                          ->saveSlugsTo('slug');
    }

    /**
     * The plan may have many features.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function features(): HasMany
    {
        return $this->hasMany(config('abovesky.subscription.models.plan_feature'), 'plan_id', 'id');
    }

    /**
     * The plan may have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(config('abovesky.subscription.models.plan_subscription'), 'plan_id', 'id');
    }

    /**
     * Check if plan is free.
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return (float) $this->price <= 0.00;
    }

    /**
     * Check if plan has trial.
     *
     * @return bool
     */
    public function hasTrial(): bool
    {
        return $this->trial_period && $this->trial_interval;
    }

    /**
     * Get plan feature by the given slug.
     *
     * @param string $featureSlug
     *
     * @return \Abovesky\Subscription\Models\PlanFeature|null
     */
    public function getFeatureBySlug(string $featureSlug): ?PlanFeature
    {
        return $this->features()->where('slug', $featureSlug)->first();
    }

    /**
     * Activate the plan.
     *
     * @return $this
     */
    public function activate()
    {
        $this->update(['is_active' => true]);

        return $this;
    }

    /**
     * Deactivate the plan.
     *
     * @return $this
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);

        return $this;
    }
}
