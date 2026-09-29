# Observability Guide — MonKeysLegion v2

This guide covers the four pillars of observability in MonKeysLegion:
**API documentation** (OpenAPI/Swagger), **health checks**, **structured logging**,
and **distributed tracing/metrics** (OpenTelemetry/Prometheus).

---

## 1. OpenAPI Documentation

### Auto-Generation

MonKeysLegion generates an OpenAPI 3.1 specification automatically from your
route attributes and controller metadata.

```php
#[Route('GET', '/users/{id:\d+}', name: 'users.show', summary: 'Get user by ID', tags: ['Users'])]
#[ApiResponse(status: 200, description: 'User details', ref: UserResource::class)]
public function show(string $id): Response { ... }
```

The generator reads:
- `#[Route]` — path, method, summary, tags, description
- `#[RequestBody]` — request body schema (inline or DTO class ref)
- `#[ApiResponse]` — response status codes and schemas
- `#[ApiParam]` — query/path/header parameters
- `#[ApiSecurity]` — security requirements
- `#[ApiDeprecated]` — deprecation markers
- `#[ApiHidden]` — hide from documentation
- `#[ApiInfo]` — API title, version, description
- DTO type-hints → auto-generated request body schemas from validation attributes

### CLI Commands

```bash
# Export the spec to a file
php bin/ml openapi:export storage/openapi/openapi.json

# Validate the spec for common issues
php bin/ml openapi:validate

# Serve Swagger UI on a standalone port
php bin/ml openapi:serve --port=8888
```

### Swagger UI & ReDoc

The OpenApiMiddleware serves interactive documentation at:
- **Swagger UI**: `http://localhost:8080/_swagger` (or `/docs`)
- **ReDoc**: `http://localhost:8080/_redoc`
- **Spec JSON**: `http://localhost:8080/openapi.json`

Configure paths in `config/observability.mlc`:
```
openapi {
    swagger_path = "/_swagger"
    redoc_path   = "/_redoc"
    dark_mode    = false
}
```

### Schema Generation from DTOs

The `SchemaGenerator` reads validation attributes on DTO properties and
generates JSON Schema:

```php
final readonly class CreateUserRequest
{
    public function __construct(
        #[NotBlank]
        #[Length(min: 2, max: 255)]
        public string $name,

        #[NotBlank]
        #[Email]
        public string $email,

        #[Range(min: 18)]
        public int $age,
    ) {}
}
```

Generates:
```json
{
  "type": "object",
  "properties": {
    "name": { "type": "string", "minLength": 2, "maxLength": 255 },
    "email": { "type": "string", "format": "email" },
    "age": { "type": "integer", "minimum": 18 }
  },
  "required": ["name", "email"]
}
```

---

## 2. Health Checks

### Endpoints

- `GET /health` — Simple 200/503 response for load balancers
- `GET /health/detailed` — Full JSON breakdown of all checks

### CLI

```bash
php bin/ml health:check
```

### Default Checks

| Check | Description |
|-------|-------------|
| `database` | Runs `SELECT 1` to verify DB connectivity |
| `cache` | Writes and reads a test key |
| `disk_space` | Checks `disk_free_space` against threshold |

### Custom Checks

Implement `HealthCheckInterface` and register with the `HealthCheckService`:

```php
final class QueueHealthCheck implements HealthCheckInterface
{
    public function name(): string { return 'queue'; }

    public function check(): HealthCheckResult
    {
        $start = microtime(true);
        try {
            $queueSize = $this->queue->size();
            $status = $queueSize > 10000 ? 'degraded' : 'healthy';
            return new HealthCheckResult(
                'queue', $status,
                "Queue size: {$queueSize}",
                (microtime(true) - $start) * 1000,
            );
        } catch (\Throwable $e) {
            return new HealthCheckResult('queue', 'unhealthy', $e->getMessage());
        }
    }
}
```

---

## 3. Structured Logging

### JSON Format

When `logging.structured = true` in `config/observability.mlc`, all log entries
are emitted as JSON with standard fields:

```json
{
  "timestamp": "2026-09-28T12:00:00+00:00",
  "level": "info",
  "message": "User created",
  "context": { "user_id": 42 },
  "request_id": "a1b2c3d4-...",
  "correlation_id": "e5f6g7h8-...",
  "user_id": 42,
  "route": "users.create",
  "method": "POST",
  "duration_ms": 15.3,
  "ip": "192.168.1.1"
}
```

### Request ID Propagation

The `RequestIdMiddleware` generates a UUID v4 for each request (or accepts an
upstream `X-Request-Id` header) and propagates it through:

1. Request attribute: `$request->getAttribute('request_id')`
2. Response header: `X-Request-Id`
3. Log context: auto-injected by `JsonFormatter`

### Correlation ID

The `CorrelationIdMiddleware` propagates a correlation ID across services:

1. Reads `X-Correlation-Id` header (from upstream gateway)
2. Falls back to `request_id` if not set
3. Generates new UUID if neither exists
4. Echoes in `X-Correlation-Id` response header

---

## 4. Distributed Tracing

### OpenTelemetry OTLP Export

Configure in `config/observability.mlc`:
```
tracing {
    enabled  = true
    exporter = "otlp"
    endpoint = "http://localhost:4318/v1/traces"
    sampling_rate = 1.0
    service_name = "monkeyslegion-app"
}
```

Or via environment variables:
```bash
export OTEL_ENABLED=true
export OTEL_EXPORTER_OTLP_ENDPOINT=http://collector:4318/v1/traces
export OTEL_SERVICE_NAME=my-service
export OTEL_TRACES_SAMPLER_ARG=0.5
```

### W3C Trace Context

The `TraceContext` class implements W3C trace context propagation:

```
traceparent: 00-0af7651916cd43dd8448eb211c80319c-b7ad6b7169203331-01
```

The `RequestTracingMiddleware` automatically:
1. Extracts trace context from incoming `traceparent` header
2. Creates a SERVER span for each request
3. Injects trace context into the response `traceparent` header
4. Exports spans to the configured OTLP endpoint

### Manual Spans

```php
$tracer = Telemetry::tracer();

$span = $tracer->startSpan('database.query');
$span->setAttribute('db.system', 'mysql');
$span->setAttribute('db.statement', 'SELECT * FROM users');

// ... execute query ...

$span->setStatus(SpanStatus::OK);
$span->end();
```

---

## 5. Prometheus Metrics

### Endpoint

`GET /metrics` returns metrics in Prometheus text exposition format:

```
# HELP ml_http_requests_total Total HTTP requests
# TYPE ml_http_requests_total counter
ml_http_requests_total{method="GET",status="200",route="users.index"} 42

# HELP ml_http_request_duration_seconds Request duration
# TYPE ml_http_request_duration_seconds histogram
ml_http_request_duration_seconds_bucket{le="0.005",route="users.index"} 38
ml_http_request_duration_seconds_bucket{le="0.01",route="users.index"} 40
ml_http_request_duration_seconds_bucket{le="+Inf",route="users.index"} 42
ml_http_request_duration_seconds_sum{route="users.index"} 0.234
ml_http_request_duration_seconds_count{route="users.index"} 42
```

### Standard Metrics

| Metric | Type | Description |
|--------|------|-------------|
| `ml_http_requests_total` | counter | Total HTTP requests by method/status/route |
| `ml_http_request_duration_seconds` | histogram | Request latency by route |
| `ml_http_requests_in_flight` | gauge | Currently active requests |
| `ml_memory_usage_bytes` | gauge | PHP memory usage |
| `ml_memory_peak_bytes` | gauge | PHP peak memory usage |

### Recording Custom Metrics

```php
$metrics = Telemetry::metrics();

// Counter
$metrics->counter('orders_created', 1, ['status' => 'completed']);

// Gauge
$metrics->gauge('queue_depth', 42, ['queue' => 'default']);

// Histogram
$metrics->histogram('api_response_size', 1024, ['endpoint' => '/api/users']);

// Timer
$stop = $metrics->timer('db_query_duration');
// ... execute query ...
$stop(['query' => 'SELECT * FROM users']);
```

---

## 6. API Versioning

### URL Prefix Versioning

```php
#[RoutePrefix('/api/v2/users')]
#[ApiVersion('v2')]
final class UserApiController
{
    #[Route('GET', '/', name: 'api.v2.users.index')]
    public function index(): Response { ... }
}
```

### Header Versioning

```php
#[ApiVersion('2', strategy: 'header')]
public function index(ServerRequestInterface $request): Response
{
    $version = $request->getAttribute('api_version'); // "2"
    // ...
}
```

### Deprecation

Mark endpoints as deprecated with `#[Deprecated]`:

```php
#[Deprecated(
    reason: 'Use /api/v2/users instead',
    sunset: 'Wed, 11 Nov 2026 23:59:59 GMT',
    link: 'https://docs.example.com/migration-guide'
)]
#[Route('GET', '/api/v1/users', name: 'api.v1.users.index')]
public function index(): Response { ... }
```

This adds the following response headers:
```
Deprecation: true
Sunset: Wed, 11 Nov 2026 23:59:59 GMT
Link: <https://docs.example.com/migration-guide>; rel="deprecation"
```

---

## 7. Rate Limit Headers

The `RateLimitMiddleware` adds standard rate limit headers to responses:

```
RateLimit-Limit: 60
RateLimit-Remaining: 58
RateLimit-Reset: 60
```

When the limit is exceeded, returns `429 Too Many Requests` with:
```
Retry-After: 60
RateLimit-Reset: 60
```

---

## Configuration Summary

All observability settings are in `config/observability.mlc`. See the file
for all available options with inline comments.
