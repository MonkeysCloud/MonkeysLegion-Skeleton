# Authentication

MonKeysLegion provides JWT authentication, RBAC (Role-Based Access Control), policies, and OAuth social login.

## Authentication Attributes

```php
use MonkeysLegion\Auth\Attribute\{Authenticated, RequiresRole};

#[Authenticated]               // Require a logged-in user
public function dashboard(): Response { ... }

#[RequiresRole('admin')]       // Require specific role
public function adminPanel(): Response { ... }

#[RequiresRole('admin', 'moderator')]  // Multiple roles (OR)
public function moderate(): Response { ... }
```

## Rate Limiting

```php
use MonkeysLegion\Auth\Attribute\Throttle;

#[Throttle(max: 60, per: 1)]     // 60 requests per minute
#[Route('POST', '/api/login')]
public function login(): Response { ... }

#[Throttle(max: 5, per: 1)]      // Stricter for sensitive routes
#[Route('POST', '/api/password/reset')]
public function resetPassword(): Response { ... }
```

## Auth Service

```php
final class AuthService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly JwtService $jwt,
    ) {}

    public function attempt(string $email, string $password): ?string
    {
        $user = $this->users->findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            return $this->jwt->issue($user);
        }

        return null;
    }

    public function userFromToken(string $token): ?User
    {
        return $this->jwt->verify($token);
    }
}
```

## Authorization Policies

```php
final class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->isAdmin() || $user->id === $post->user_id;
    }
}
```

### Using Policies

```php
// In a controller
if (!$this->gate->allows('update', [$post])) {
    return Response::json(['error' => 'Forbidden'], 403);
}
```

## OAuth Social Login

MonKeysLegion supports Google, GitHub, GitLab, Facebook, X (Twitter), and Microsoft.

### Configuration

```hocon
# config/services.mlc
services {
    oauth {
        github {
            client_id     = ${GITHUB_CLIENT_ID:""}
            client_secret = ${GITHUB_CLIENT_SECRET:""}
            redirect_uri  = "http://localhost:8080/oauth/callback/github"
        }
    }
}
```

### Usage

```php
use MonkeysLegion\Auth\OAuth\OAuthManager;

$manager = $container->get(OAuthManager::class);

// Redirect to provider
$state = $manager->generateState();
$url = $manager->provider('github')->getAuthorizationUrl($state);

// Handle callback
$token = $manager->provider('github')->getAccessToken($code);
$user = $manager->provider('github')->getUser($token['access_token']);
// $user->email, $user->name, $user->avatar, $user->providerId
```

See the [OAuth guide](oauth-socialite.md) for full details.

## Session-Based Auth

For web applications using sessions:

```hocon
# config/session.mlc
session {
    driver   = "redis"
    lifetime = 120
    encrypt  = true
}
```

## CSRF Protection

CSRF tokens are automatically generated for sessions:

```php
// In templates
<form method="POST">
    @csrf
    <input name="email" />
</form>
```

## Configuration

```hocon
# config/auth.mlc
auth {
    default {
        guard = "jwt"
    }
    guards {
        jwt {
            driver     = "jwt"
            secret     = ${APP_KEY}
            expiration = 3600
        }
        session {
            driver = "session"
        }
    }
}
```
