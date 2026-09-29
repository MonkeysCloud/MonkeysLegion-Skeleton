# Notifications

MonKeysLegion provides a multi-channel notification system with Database, Mail, Slack, Microsoft Teams, and Webhook channels.

## Channels

| Channel | Key | Use Case |
|---------|-----|----------|
| Database | `database` | Store in-app notifications |
| Mail | `mail` | Email notifications |
| Slack | `slack` | Slack channel messages |
| Teams | `teams` | Microsoft Teams messages |
| Webhook | `webhook` | Generic webhook with HMAC signing |

## Usage

### Create a Notification

```php
use MonkeysLegion\Notifications\Contracts\NotificationInterface;
use MonkeysLegion\Notifications\Messages\SlackMessage;

final class DeploymentNotification implements NotificationInterface
{
    public function via(NotifiableInterface $notifiable): array
    {
        return ['slack', 'database'];
    }

    public function toSlack(NotifiableInterface $notifiable): SlackMessage
    {
        return (new SlackMessage('#deployments'))
            ->header('Deployment Successful')
            ->section('*Version:* v1.2.3')
            ->section('*Environment:* production')
            ->divider()
            ->context(['Deployed by: CI/CD Pipeline']);
    }

    public function toArray(NotifiableInterface $notifiable): array
    {
        return ['event' => 'deployment.success', 'version' => 'v1.2.3'];
    }

    public function toDatabase(NotifiableInterface $notifiable): array
    {
        return ['message' => 'Deployment v1.2.3 completed'];
    }

    public function toMail(NotifiableInterface $notifiable): mixed
    {
        return null;
    }
}
```

### Slack Messages

```php
$message = (new SlackMessage('#general'))
    ->text('Hello from MonKeysLegion!')
    ->header('Alert')
    ->section('Something happened')
    ->field('Severity', 'High')
    ->field('Time', date('Y-m-d H:i:s'))
    ->divider()
    ->context(['Server: prod-01']);
```

### Teams Messages

```php
$message = (new TeamsMessage())
    ->title('Deployment Alert')
    ->text('Version v1.2.3 deployed successfully')
    ->fact('Environment', 'production')
    ->fact('Duration', '2m 30s')
    ->color('good')
    ->action('View Build', 'https://ci.example.com/build/123');
```

### Webhook Channel

The webhook channel sends HMAC-SHA256 signed POST requests:

```php
// The notifiable returns the webhook URL via routeNotificationFor('webhook')
// Headers sent:
//   Content-Type: application/json
//   X-Webhook-Signature: sha256=<hmac>
//   X-Webhook-Event: <event name>
```
