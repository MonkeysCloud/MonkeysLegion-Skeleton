# Task Scheduling

MonKeysLegion provides cron-based task scheduling with health monitoring.

## Defining Tasks

Define scheduled tasks in your service provider or a dedicated schedule file:

```php
use MonkeysLegion\Schedule\Task;
use MonkeysLegion\Schedule\CronExpression;

// Run every minute
Task::everyMinute()
    ->call(fn() => $this->checkHealth())
    ->name('health-check');

// Run every hour
Task::hourly()
    ->call(fn() => $this->cleanupTempFiles())
    ->name('cleanup-temp');

// Run daily at midnight
Task::daily()
    ->call(fn() => $this->dailyReport())
    ->name('daily-report');

// Run weekly on Monday at 9 AM
Task::weekly('09:00')
    ->call(fn() => $this->weeklyBackup())
    ->name('weekly-backup');

// Custom cron expression
Task::cron('0 */6 * * *')
    ->call(fn() => $this->syncData())
    ->name('sync-every-6-hours');
```

## Task Options

```php
Task::daily('02:00')
    ->call(fn() => $this->processQueue())
    ->name('process-queue')
    ->onOneServer()              // Run on only one server (distributed lock)
    ->withoutOverlapping()       // Don't start if previous run is still going
    ->timeout(300)               // Kill after 5 minutes
    ->retry(3)                   // Retry 3 times on failure
    ->emailOnFailure('admin@example.com');
```

## Running the Scheduler

Add a cron entry to run the scheduler every minute:

```bash
* * * * * cd /path/to/app && php bin/ml schedule:run >> /dev/null 2>&1
```

### CLI Commands

```bash
php bin/ml schedule:run          # Run due tasks (called by cron)
php bin/ml schedule:list         # List all scheduled tasks
php bin/ml schedule:monitor      # Show task health status
php bin/ml schedule:work         | Run scheduler in foreground (dev)
```

## Schedule Monitor

The schedule monitor tracks task health:

```bash
php bin/ml schedule:monitor
```

Statuses:

| Status | Meaning |
|--------|---------|
| ✓ healthy | Running normally |
| ⚠ degraded | Has failures or is overdue |
| ✗ unhealthy | 3+ consecutive failures |
| ○ never_run | Has never executed |

## Queue-Based Tasks

Dispatch jobs on a schedule:

```php
Task::daily('06:00')
    ->dispatch(new GenerateDailyReportJob())
    ->name('daily-report')
    ->onQueue('reports');
```

## Conditional Tasks

```php
Task::daily('00:00')
    ->call(fn() => $this->archiveOldData())
    ->name('archive')
    ->when(fn() => $this->shouldArchive());  // Only runs if condition is true
```

## Environments

Run tasks only in specific environments:

```php
Task::daily('02:00')
    ->call(fn() => $this->syncFromProduction())
    ->name('sync')
    ->environments(['staging', 'production']);
```

## Testing

```php
// Assert a task is scheduled
$this->scheduler->assertScheduled('daily-report');

// Run a specific task
$this->scheduler->run('daily-report');

// Run all due tasks
$this->scheduler->runDue();
```
