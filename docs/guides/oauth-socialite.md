# OAuth Socialite

MonKeysLegion provides OAuth 2.0 integration for social login with Google, GitHub, GitLab, Facebook, X (Twitter), and Microsoft.

## Configuration

```hocon
# config/services.mlc
services {
    oauth {
        github {
            client_id     = ${GITHUB_CLIENT_ID:""}
            client_secret = ${GITHUB_CLIENT_SECRET:""}
            redirect_uri  = "http://localhost:8080/oauth/callback/github"
            scopes        = ["read:user", "user:email"]
        }
    }
}
```

## Usage

### Redirect to Provider

```php
use MonkeysLegion\Auth\OAuth\OAuthManager;

$manager = $container->get(OAuthManager::class);
$state = $manager->generateState();
$_SESSION['oauth_state'] = $state;

$url = $manager->provider('github')->getAuthorizationUrl($state);
return Response::redirect($url);
```

### Handle Callback

```php
// In your callback controller
$provider = $manager->provider('github');
$token = $provider->getAccessToken($code);
$user = $provider->getUser($token['access_token']);

// $user is an OAuthUser DTO
echo $user->email;      // user@example.com
echo $user->name;       // John Doe
echo $user->avatar;     // https://...
echo $user->provider;   // github
echo $user->providerId; // 12345
```

## Supported Providers

| Provider | Key | Scopes |
|----------|-----|--------|
| Google | `google` | `openid email profile` |
| GitHub | `github` | `read:user user:email` |
| GitLab | `gitlab` | `read_user` |
| Facebook | `facebook` | `email public_profile` |
| X (Twitter) | `x` | `users.read tweet.read` |
| Microsoft | `microsoft` | `User.Read` |

## Security

- **PKCE**: All providers use PKCE (Proof Key for Code Exchange) for enhanced security
- **State**: A random state parameter prevents CSRF attacks
- **OAuthUser DTO**: Raw provider response is excluded from `toArray()` to prevent data leakage
