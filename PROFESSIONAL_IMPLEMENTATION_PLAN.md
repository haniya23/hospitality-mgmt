# 🚀 Professional Implementation Plan - Stay Loops Subscription System

## 📋 **Implementation Overview**

This plan transforms your current subscription system into a production-ready, professional-grade solution following the specification. We'll implement this in 3 phases with clear deliverables and testing at each stage.

---

## 🎯 **Phase 1: Core Infrastructure (Week 1-2)**

### **1.1 Database Schema Updates**

#### **Create New Tables**

```bash
# Generate migrations
php artisan make:migration create_subscriptions_table
php artisan make:migration create_subscription_addons_table
php artisan make:migration create_webhooks_table
php artisan make:migration create_subscription_history_table
php artisan make:migration create_refunds_table
php artisan make:migration update_payments_table_for_subscriptions
```

#### **Migration Files**

**1. `create_subscriptions_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_slug'); // trial, starter, professional
            $table->string('plan_name');
            $table->enum('status', ['trial', 'active', 'pending', 'expired', 'cancelled'])->default('trial');
            $table->integer('base_accommodation_limit')->default(3);
            $table->integer('addon_count')->default(0);
            $table->timestamp('start_at');
            $table->timestamp('current_period_end');
            $table->enum('billing_interval', ['month', 'year'])->default('month');
            $table->integer('price_cents')->default(0);
            $table->string('currency', 3)->default('INR');
            $table->string('cashfree_order_id')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'current_period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
```

**2. `create_subscription_addons_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->integer('qty');
            $table->integer('unit_price_cents');
            $table->timestamp('cycle_start');
            $table->timestamp('cycle_end');
            $table->timestamps();
            
            $table->index(['subscription_id', 'cycle_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_addons');
    }
};
```

**3. `create_webhooks_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // cashfree
            $table->string('event_id')->nullable(); // provider event ID
            $table->json('payload');
            $table->string('signature_header')->nullable();
            $table->timestamp('received_at');
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['provider', 'event_id']);
            $table->index(['processed', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
```

**4. `create_subscription_history_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('action'); // created, upgraded, addon_added, cancelled, extended
            $table->json('data')->nullable();
            $table->string('performed_by')->nullable(); // user, admin, system
            $table->timestamps();
            
            $table->index(['subscription_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_history');
    }
};
```

**5. `create_refunds_table.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->integer('amount_cents');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->index(['payment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
```

**6. `update_payments_table_for_subscriptions.php`**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('cashfree_order_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->integer('amount_cents')->nullable();
            $table->string('currency', 3)->default('INR');
            $table->string('method')->nullable();
            $table->json('raw_response')->nullable();
            
            $table->index(['subscription_id', 'status']);
            $table->index(['cashfree_order_id']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn([
                'subscription_id',
                'cashfree_order_id',
                'payment_id',
                'amount_cents',
                'currency',
                'method',
                'raw_response'
            ]);
        });
    }
};
```

### **1.2 Service Layer Implementation**

#### **Create Services Directory Structure**

```bash
# Create service classes
php artisan make:class Services/SubscriptionService
php artisan make:class Services/WebhookProcessingService
php artisan make:class Services/NotificationService
php artisan make:class Services/ReconciliationService
```

#### **SubscriptionService Implementation**

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionAddon;
use App\Models\SubscriptionHistory;
use App\Events\SubscriptionCreated;
use App\Events\SubscriptionUpgraded;
use App\Events\AddonsPurchased;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionService
{
    public function createSubscription(User $user, string $plan, string $billingInterval = 'month', int $duration = 1): Subscription
    {
        return DB::transaction(function () use ($user, $plan, $billingInterval, $duration) {
            // Calculate pricing
            $planConfig = config("cashfree.plans.{$plan}");
            $priceCents = $planConfig['amount'] * 100; // Convert to cents
            
            // Calculate period end
            $startAt = now();
            $currentPeriodEnd = $billingInterval === 'year' 
                ? $startAt->copy()->addYear() 
                : $startAt->copy()->addMonths($duration);
            
            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_slug' => $plan,
                'plan_name' => $planConfig['name'],
                'status' => 'active',
                'base_accommodation_limit' => $this->getPlanLimit($plan),
                'addon_count' => 0,
                'start_at' => $startAt,
                'current_period_end' => $currentPeriodEnd,
                'billing_interval' => $billingInterval,
                'price_cents' => $priceCents,
                'currency' => 'INR',
            ]);
            
            // Update user
            $user->update([
                'subscription_status' => $plan,
                'subscription_ends_at' => $currentPeriodEnd,
                'is_trial_active' => false,
                'properties_limit' => $this->getPlanLimit($plan),
            ]);
            
            // Log history
            $this->logSubscriptionHistory($subscription, 'created', [
                'plan' => $plan,
                'billing_interval' => $billingInterval,
                'duration' => $duration,
            ], 'system');
            
            // Dispatch event
            event(new SubscriptionCreated($subscription));
            
            return $subscription;
        });
    }
    
    public function upgradeSubscription(Subscription $subscription, string $newPlan): Subscription
    {
        return DB::transaction(function () use ($subscription, $newPlan) {
            $oldPlan = $subscription->plan_slug;
            $planConfig = config("cashfree.plans.{$newPlan}");
            
            // Update subscription
            $subscription->update([
                'plan_slug' => $newPlan,
                'plan_name' => $planConfig['name'],
                'base_accommodation_limit' => $this->getPlanLimit($newPlan),
                'price_cents' => $planConfig['amount'] * 100,
            ]);
            
            // Update user
            $subscription->user->update([
                'subscription_status' => $newPlan,
                'properties_limit' => $this->getPlanLimit($newPlan),
            ]);
            
            // Log history
            $this->logSubscriptionHistory($subscription, 'upgraded', [
                'from_plan' => $oldPlan,
                'to_plan' => $newPlan,
            ], 'user');
            
            // Dispatch event
            event(new SubscriptionUpgraded($subscription, $oldPlan));
            
            return $subscription;
        });
    }
    
    public function addAddons(Subscription $subscription, int $quantity): SubscriptionAddon
    {
        return DB::transaction(function () use ($subscription, $quantity) {
            $unitPriceCents = config('cashfree.plans.additional_accommodation.amount') * 100;
            $cycleStart = now();
            $cycleEnd = $subscription->current_period_end;
            
            // Create addon
            $addon = SubscriptionAddon::create([
                'subscription_id' => $subscription->id,
                'qty' => $quantity,
                'unit_price_cents' => $unitPriceCents,
                'cycle_start' => $cycleStart,
                'cycle_end' => $cycleEnd,
            ]);
            
            // Update subscription
            $subscription->increment('addon_count', $quantity);
            
            // Log history
            $this->logSubscriptionHistory($subscription, 'addon_added', [
                'quantity' => $quantity,
                'unit_price_cents' => $unitPriceCents,
            ], 'user');
            
            // Dispatch event
            event(new AddonsPurchased($subscription, $addon));
            
            return $addon;
        });
    }
    
    public function extendSubscription(Subscription $subscription, int $months): Subscription
    {
        return DB::transaction(function () use ($subscription, $months) {
            $newEndDate = $subscription->current_period_end->copy()->addMonths($months);
            
            $subscription->update([
                'current_period_end' => $newEndDate,
            ]);
            
            $subscription->user->update([
                'subscription_ends_at' => $newEndDate,
            ]);
            
            // Log history
            $this->logSubscriptionHistory($subscription, 'extended', [
                'months' => $months,
                'new_end_date' => $newEndDate,
            ], 'admin');
            
            return $subscription;
        });
    }
    
    public function cancelSubscription(Subscription $subscription, string $reason = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $reason) {
            $subscription->update([
                'status' => 'cancelled',
            ]);
            
            $subscription->user->update([
                'subscription_status' => 'cancelled',
            ]);
            
            // Log history
            $this->logSubscriptionHistory($subscription, 'cancelled', [
                'reason' => $reason,
            ], 'user');
            
            return $subscription;
        });
    }
    
    private function getPlanLimit(string $plan): int
    {
        return match ($plan) {
            'starter' => 1,
            'professional' => 5,
            'trial' => 5,
            default => 1,
        };
    }
    
    private function logSubscriptionHistory(Subscription $subscription, string $action, array $data, string $performedBy): void
    {
        SubscriptionHistory::create([
            'subscription_id' => $subscription->id,
            'action' => $action,
            'data' => $data,
            'performed_by' => $performedBy,
        ]);
    }
}
```

### **1.3 Queue System Setup**

#### **Create Jobs Directory**

```bash
# Create job classes
php artisan make:job ProcessCashfreeWebhook
php artisan make:job SendSubscriptionEmail
php artisan make:job ProcessSubscriptionUpgrade
php artisan make:job DailyReconciliation
```

#### **ProcessCashfreeWebhook Job**

```php
<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Services\WebhookProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCashfreeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $webhookId;
    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    public function __construct(int $webhookId)
    {
        $this->webhookId = $webhookId;
    }

    public function handle(WebhookProcessingService $webhookService): void
    {
        try {
            $webhook = Webhook::findOrFail($this->webhookId);
            
            if ($webhook->processed) {
                Log::info('Webhook already processed', ['webhook_id' => $this->webhookId]);
                return;
            }
            
            $webhookService->processWebhook($webhook);
            
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'webhook_id' => $this->webhookId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $webhook = Webhook::find($this->webhookId);
        if ($webhook) {
            $webhook->update([
                'error_message' => $exception->getMessage(),
            ]);
        }
        
        Log::error('Webhook processing job failed permanently', [
            'webhook_id' => $this->webhookId,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

### **1.4 Event System Setup**

#### **Create Events Directory**

```bash
# Create event classes
php artisan make:event SubscriptionCreated
php artisan make:event SubscriptionUpgraded
php artisan make:event AddonsPurchased
php artisan make:event SubscriptionCancelled
php artisan make:event PaymentFailed
```

#### **SubscriptionCreated Event**

```php
<?php

namespace App\Events;

use App\Models\Subscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Subscription $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
```

#### **Create Event Listeners**

```bash
# Create listener classes
php artisan make:listener SendSubscriptionWelcomeEmail
php artisan make:listener UpdateAdminDashboard
php artisan make:listener LogSubscriptionEvent
```

#### **SendSubscriptionWelcomeEmail Listener**

```php
<?php

namespace App\Listeners;

use App\Events\SubscriptionCreated;
use App\Jobs\SendSubscriptionEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSubscriptionWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SubscriptionCreated $event): void
    {
        SendSubscriptionEmail::dispatch(
            $event->subscription->user,
            'welcome',
            [
                'plan' => $event->subscription->plan_name,
                'expires_at' => $event->subscription->current_period_end,
            ]
        );
    }
}
```

---

## 🎯 **Phase 2: Enhanced Features (Week 3-4)**

### **2.1 API Endpoints Implementation**

#### **Create API Controller**

```bash
php artisan make:controller Api/SubscriptionController
```

#### **API Routes**

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('subscription')->group(function () {
        Route::post('/create-order', [Api\SubscriptionController::class, 'createOrder']);
        Route::post('/addons', [Api\SubscriptionController::class, 'addAddons']);
        Route::get('/status', [Api\SubscriptionController::class, 'getStatus']);
        Route::post('/cancel', [Api\SubscriptionController::class, 'cancel']);
        Route::get('/invoices', [Api\SubscriptionController::class, 'getInvoices']);
    });
});
```

#### **API Controller Implementation**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'plan' => 'required|in:starter,professional',
            'billing_interval' => 'required|in:month,year',
            'additional_accommodations' => 'integer|min:0|max:50',
        ]);

        $user = auth()->user();
        $plan = $request->plan;
        $billingInterval = $request->billing_interval;
        $additionalAccommodations = $request->additional_accommodations ?? 0;

        // Create order via Cashfree
        $orderData = $this->createCashfreeOrder($user, $plan, $billingInterval, $additionalAccommodations);

        return response()->json([
            'success' => true,
            'order_id' => $orderData['order_id'],
            'payment_url' => $orderData['payment_url'],
        ]);
    }

    public function addAddons(Request $request): JsonResponse
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:50',
        ]);

        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription || $subscription->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Active subscription required',
            ], 400);
        }

        $addon = $this->subscriptionService->addAddons($subscription, $request->qty);

        return response()->json([
            'success' => true,
            'addon' => $addon,
            'new_total_accommodations' => $subscription->base_accommodation_limit + $subscription->addon_count,
        ]);
    }

    public function getStatus(Request $request): JsonResponse
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'subscription' => [
                'plan' => $subscription->plan_name,
                'status' => $subscription->status,
                'base_accommodation_limit' => $subscription->base_accommodation_limit,
                'addon_count' => $subscription->addon_count,
                'total_accommodations' => $subscription->base_accommodation_limit + $subscription->addon_count,
                'current_period_end' => $subscription->current_period_end,
                'billing_interval' => $subscription->billing_interval,
            ],
        ]);
    }

    public function cancel(Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription found',
            ], 404);
        }

        $this->subscriptionService->cancelSubscription($subscription, $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully',
        ]);
    }

    public function getInvoices(Request $request): JsonResponse
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription found',
            ], 404);
        }

        $invoices = $subscription->payments()
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'invoices' => $invoices,
        ]);
    }

    private function createCashfreeOrder($user, $plan, $billingInterval, $additionalAccommodations): array
    {
        // Implementation similar to existing CashfreeController
        // but with enhanced error handling and logging
        // ... (implementation details)
    }
}
```

### **2.2 Admin Panel Enhancements**

#### **Create Enhanced Filament Resources**

```bash
# Create new Filament resources
php artisan make:filament-resource Subscription --generate
php artisan make:filament-resource Payment --generate
php artisan make:filament-resource Webhook --generate
php artisan make:filament-resource SubscriptionHistory --generate
```

#### **Enhanced Subscription Resource**

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Subscriptions';
    protected static ?string $navigationGroup = 'Subscription Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('plan_slug')
                    ->options([
                        'trial' => 'Trial',
                        'starter' => 'Starter',
                        'professional' => 'Professional',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'trial' => 'Trial',
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('base_accommodation_limit')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('addon_count')
                    ->numeric()
                    ->default(0),
                Forms\Components\DateTimePicker::make('start_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('current_period_end')
                    ->required(),
                Forms\Components\Select::make('billing_interval')
                    ->options([
                        'month' => 'Monthly',
                        'year' => 'Yearly',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('price_cents')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('currency')
                    ->default('INR')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan_name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Trial Plan' => 'warning',
                        'Starter Plan' => 'success',
                        'Professional Plan' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trial' => 'warning',
                        'pending' => 'info',
                        'expired' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('base_accommodation_limit')
                    ->label('Base Limit'),
                Tables\Columns\TextColumn::make('addon_count')
                    ->label('Add-ons'),
                Tables\Columns\TextColumn::make('total_accommodations')
                    ->label('Total')
                    ->getStateUsing(fn (Subscription $record): int => 
                        $record->base_accommodation_limit + $record->addon_count
                    ),
                Tables\Columns\TextColumn::make('current_period_end')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_cents')
                    ->label('Price')
                    ->money('INR'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'trial' => 'Trial',
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('plan_slug')
                    ->options([
                        'trial' => 'Trial',
                        'starter' => 'Starter',
                        'professional' => 'Professional',
                    ]),
            ])
            ->actions([
                Action::make('extend')
                    ->icon('heroicon-m-clock')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('months')
                            ->options([
                                1 => '1 Month',
                                3 => '3 Months',
                                6 => '6 Months',
                                12 => '12 Months',
                            ])
                            ->required(),
                    ])
                    ->action(function (Subscription $record, array $data) {
                        app(SubscriptionService::class)->extendSubscription($record, $data['months']);
                        
                        Notification::make()
                            ->title('Subscription extended successfully')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Cancellation Reason')
                            ->required(),
                    ])
                    ->action(function (Subscription $record, array $data) {
                        app(SubscriptionService::class)->cancelSubscription($record, $data['reason']);
                        
                        Notification::make()
                            ->title('Subscription cancelled successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
```

---

## 🎯 **Phase 3: Advanced Features (Week 5-6)**

### **3.1 Monitoring & Operations**

#### **Create Monitoring Dashboard**

```bash
# Create monitoring resources
php artisan make:filament-resource Webhook --generate
php artisan make:filament-resource SubscriptionHistory --generate
```

#### **Daily Reconciliation Job**

```php
<?php

namespace App\Jobs;

use App\Services\ReconciliationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyReconciliation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ReconciliationService $reconciliationService): void
    {
        try {
            $reconciliationService->reconcileDailyTransactions();
            Log::info('Daily reconciliation completed successfully');
        } catch (\Exception $e) {
            Log::error('Daily reconciliation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
```

#### **ReconciliationService**

```php
<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Webhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReconciliationService
{
    private $baseUrl;
    private $appId;
    private $secretKey;
    private $apiVersion;

    public function __construct()
    {
        $this->appId = config('cashfree.app_id');
        $this->secretKey = config('cashfree.secret_key');
        $this->apiVersion = config('cashfree.api_version');
        $this->baseUrl = config('cashfree.base_url')[config('cashfree.mode')];
    }

    public function reconcileDailyTransactions(): void
    {
        $yesterday = Carbon::yesterday();
        $today = Carbon::today();

        // Fetch transactions from Cashfree
        $cashfreeTransactions = $this->fetchCashfreeTransactions($yesterday, $today);

        // Compare with local database
        $localTransactions = Payment::whereBetween('created_at', [$yesterday, $today])
            ->where('status', 'completed')
            ->get();

        $discrepancies = $this->compareTransactions($cashfreeTransactions, $localTransactions);

        if (!empty($discrepancies)) {
            $this->handleDiscrepancies($discrepancies);
        }

        Log::info('Daily reconciliation completed', [
            'cashfree_transactions' => count($cashfreeTransactions),
            'local_transactions' => $localTransactions->count(),
            'discrepancies' => count($discrepancies),
        ]);
    }

    private function fetchCashfreeTransactions(Carbon $startDate, Carbon $endDate): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-version' => $this->apiVersion,
                'x-client-id' => $this->appId,
                'x-client-secret' => $this->secretKey,
            ])->get("{$this->baseUrl}/pg/orders", [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            throw new \Exception('Failed to fetch Cashfree transactions');
        } catch (\Exception $e) {
            Log::error('Failed to fetch Cashfree transactions', [
                'error' => $e->getMessage(),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);
            return [];
        }
    }

    private function compareTransactions(array $cashfreeTransactions, $localTransactions): array
    {
        $discrepancies = [];

        foreach ($cashfreeTransactions as $cfTransaction) {
            $localTransaction = $localTransactions->firstWhere('cashfree_order_id', $cfTransaction['order_id']);

            if (!$localTransaction) {
                $discrepancies[] = [
                    'type' => 'missing_local',
                    'cashfree_order_id' => $cfTransaction['order_id'],
                    'amount' => $cfTransaction['order_amount'],
                    'status' => $cfTransaction['order_status'],
                ];
            } elseif ($localTransaction->status !== $this->mapCashfreeStatus($cfTransaction['order_status'])) {
                $discrepancies[] = [
                    'type' => 'status_mismatch',
                    'cashfree_order_id' => $cfTransaction['order_id'],
                    'cashfree_status' => $cfTransaction['order_status'],
                    'local_status' => $localTransaction->status,
                ];
            }
        }

        return $discrepancies;
    }

    private function handleDiscrepancies(array $discrepancies): void
    {
        foreach ($discrepancies as $discrepancy) {
            Log::warning('Transaction discrepancy found', $discrepancy);
            
            // Send alert to admin
            // Implementation depends on your notification system
        }
    }

    private function mapCashfreeStatus(string $cashfreeStatus): string
    {
        return match ($cashfreeStatus) {
            'PAID' => 'completed',
            'FAILED' => 'failed',
            'PENDING' => 'pending',
            default => 'pending',
        };
    }
}
```

### **3.2 Security & Compliance**

#### **Enhanced Webhook Security**

```php
<?php

namespace App\Services;

use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookProcessingService
{
    public function processWebhook(Webhook $webhook): void
    {
        try {
            $payload = $webhook->payload;
            $eventType = $payload['type'] ?? null;

            switch ($eventType) {
                case 'PAYMENT_SUCCESS_WEBHOOK':
                    $this->handlePaymentSuccess($payload);
                    break;
                case 'PAYMENT_FAILED_WEBHOOK':
                    $this->handlePaymentFailed($payload);
                    break;
                case 'PAYMENT_USER_DROPPED_WEBHOOK':
                    $this->handlePaymentDropped($payload);
                    break;
                default:
                    Log::warning('Unknown webhook event type', [
                        'webhook_id' => $webhook->id,
                        'event_type' => $eventType,
                    ]);
            }

            $webhook->update([
                'processed' => true,
                'processed_at' => now(),
            ]);

        } catch (\Exception $e) {
            $webhook->update([
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('x-webhook-signature');
        $payload = $request->getContent();
        $webhookSecret = config('cashfree.webhook_secret');

        if (!$signature || !$webhookSecret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }

    private function handlePaymentSuccess(array $payload): void
    {
        $orderId = $payload['data']['order']['order_id'] ?? null;
        
        if (!$orderId) {
            throw new \Exception('No order ID in payment success webhook');
        }

        // Process payment success
        // Implementation similar to existing CashfreeController
    }

    private function handlePaymentFailed(array $payload): void
    {
        $orderId = $payload['data']['order']['order_id'] ?? null;
        
        if (!$orderId) {
            throw new \Exception('No order ID in payment failed webhook');
        }

        // Process payment failure
        // Update subscription status, send notification, etc.
    }

    private function handlePaymentDropped(array $payload): void
    {
        $orderId = $payload['data']['order']['order_id'] ?? null;
        
        if (!$orderId) {
            throw new \Exception('No order ID in payment dropped webhook');
        }

        // Process payment dropped
        // Update subscription status, send notification, etc.
    }
}
```

---

## 📋 **Implementation Checklist**

### **Phase 1: Core Infrastructure**
- [ ] Create database migrations
- [ ] Implement SubscriptionService
- [ ] Implement WebhookProcessingService
- [ ] Implement NotificationService
- [ ] Set up queue system
- [ ] Create webhook processing jobs
- [ ] Create subscription events
- [ ] Create event listeners
- [ ] Test core functionality

### **Phase 2: Enhanced Features**
- [ ] Implement API endpoints
- [ ] Create enhanced Filament resources
- [ ] Implement subscription timeline
- [ ] Implement payment history
- [ ] Implement add-on management
- [ ] Test API functionality
- [ ] Test admin panel enhancements

### **Phase 3: Advanced Features**
- [ ] Implement monitoring dashboard
- [ ] Implement daily reconciliation
- [ ] Implement webhook monitoring
- [ ] Implement security enhancements
- [ ] Implement compliance features
- [ ] Test monitoring and operations
- [ ] Test security and compliance

### **Testing & Quality Assurance**
- [ ] Unit tests for all services
- [ ] Integration tests for API endpoints
- [ ] Webhook replay tests
- [ ] Security tests
- [ ] Load testing
- [ ] UAT with real users
- [ ] Performance testing

### **Deployment & Operations**
- [ ] Set up monitoring and alerts
- [ ] Configure queue workers
- [ ] Set up daily reconciliation
- [ ] Prepare go-live checklist
- [ ] Conduct pre-launch testing
- [ ] Launch and monitor

---

## 🚀 **Next Steps**

1. **Start with Phase 1** - Implement core infrastructure
2. **Run migrations** - Set up new database tables
3. **Implement services** - Create business logic layer
4. **Set up queues** - Configure background processing
5. **Test thoroughly** - Ensure each component works
6. **Move to Phase 2** - Implement enhanced features
7. **Complete Phase 3** - Add advanced features
8. **Conduct UAT** - Test with real users
9. **Deploy to production** - Go live with monitoring

This implementation plan provides a clear path to transform your current subscription system into a professional-grade solution that meets all the specification requirements.
