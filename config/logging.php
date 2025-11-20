<?php

/**
 * MonkeysLegion Logging Configuration
 * 
 * This file contains the logging configuration for the application.
 * You can customize channels, drivers, and log levels based on your needs.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified here should match one of the
    | channels defined in the "channels" configuration array below.
    |
    | Supported: Any channel name from the 'channels' array
    |
    */
    'default' => $_ENV['LOG_CHANNEL'] ?? 'stack',

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. MonkeysLegion
    | Log supports various drivers and allows you to create custom channels.
    |
    | Available Drivers: "stack", "file", "console", "syslog", "errorlog", "null"
    |
    */
    'channels' => [

        /*
        |----------------------------------------------------------------------
        | Stack Channel (Multiple Loggers)
        |----------------------------------------------------------------------
        |
        | The stack driver allows you to combine multiple log channels into
        | a single channel. All messages will be sent to each channel in
        | the 'channels' array.
        |
        | Options:
        |   - driver: Must be "stack"
        |   - channels: Array of channel names to stack together
        |   - ignore_exceptions: Whether to ignore exceptions from sub-channels
        |
        */
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'console'],
            'ignore_exceptions' => false,
        ],

        /*
        |----------------------------------------------------------------------
        | Single File Logger
        |----------------------------------------------------------------------
        |
        | Writes all logs to a single file without rotation.
        |
        | Options:
        |   - driver: Must be "file"
        |   - path: File path where logs will be written
        |   - level: Minimum log level (debug|info|notice|warning|error|critical|alert|emergency)
        |   - format: Log message format (see format tokens below)
        |
        | Format Tokens:
        |   {timestamp} - Current date and time
        |   {env}       - Environment name (dev, production, etc.)
        |   {level}     - Log level in uppercase
        |   {message}   - The log message
        |   {context}   - JSON-encoded context data
        |
        */
        'single' => [
            'driver' => 'file',
            'path' => 'logs/app.log',
            'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
            'format' => '[{timestamp}] [{env}] {level}: {message} {context}',
        ],

        /*
        |----------------------------------------------------------------------
        | Daily Rotating File Logger
        |----------------------------------------------------------------------
        |
        | Automatically creates a new log file each day with the date in the filename.
        | Example: logs/app.log becomes logs/app-2024-01-15.log
        |
        | Options:
        |   - driver: Must be "file"
        |   - path: Base file path (date will be inserted before extension)
        |   - daily: Set to true to enable daily rotation
        |   - date_format: PHP date format for the date in filename (default: Y-m-d)
        |   - level: Minimum log level to write
        |   - format: Log message format
        |
        | Examples:
        |   logs/app.log     -> logs/app-2024-01-15.log
        |   logs/errors.log  -> logs/errors-2024-01-15.log
        |   logs/debug.txt   -> logs/debug-2024-01-15.txt
        |
        */
        'daily' => [
            'driver' => 'file',
            'path' => 'logs/app.log',
            'daily' => true,  // Enable daily rotation
            'date_format' => 'Y-m-d',  // Date format: YYYY-MM-DD
            'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
            'format' => '[{timestamp}] [{env}] {level}: {message} {context}',
        ],

        /*
        |----------------------------------------------------------------------
        | Console Logger
        |----------------------------------------------------------------------
        |
        | Outputs log messages to the console/terminal with optional colors.
        |
        | Options:
        |   - driver: Must be "console"
        |   - level: Minimum log level to display
        |   - colorize: Enable/disable ANSI color codes (true/false)
        |   - format: Log message format
        |
        | Color Scheme (when colorize is true):
        |   Emergency/Alert/Critical - Bold Red
        |   Error                    - Red
        |   Warning                  - Yellow
        |   Notice                   - Cyan
        |   Info                     - Green
        |   Debug                    - White
        |
        */
        'console' => [
            'driver' => 'console',
            'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
            'colorize' => true,
            'format' => '[{env}] {level}: {message} {context}',
        ],

        /*
        |----------------------------------------------------------------------
        | Syslog Logger
        |----------------------------------------------------------------------
        |
        | Sends log messages to the system logger using PHP's syslog() function.
        | Useful for centralized logging on Unix/Linux systems.
        |
        | Options:
        |   - driver: Must be "syslog"
        |   - level: Minimum log level
        |   - ident: String identifier for your application
        |   - facility: Syslog facility (LOG_USER, LOG_LOCAL0-7, etc.)
        |
        | Common Facilities:
        |   LOG_USER   - Generic user-level messages (default)
        |   LOG_LOCAL0 through LOG_LOCAL7 - Reserved for local use
        |   LOG_DAEMON - System daemons
        |
        */
        'syslog' => [
            'driver' => 'syslog',
            'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
            'ident' => $_ENV['APP_NAME'] ?? 'php',
            'facility' => LOG_USER,
        ],

        /*
        |----------------------------------------------------------------------
        | Error Log Logger
        |----------------------------------------------------------------------
        |
        | Uses PHP's native error_log() function to write logs.
        |
        | Options:
        |   - driver: Must be "errorlog"
        |   - level: Minimum log level
        |   - message_type: Where to send the error message
        |   - destination: Destination (required for types 1 and 3)
        |
        | Message Types:
        |   0 - Operating system's logging mechanism (default)
        |   1 - Send by email to destination address
        |   3 - Append to file specified in destination
        |   4 - Send to SAPI logging handler
        |
        */
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
            'message_type' => 0,
            'destination' => null,
        ],

        /*
        |----------------------------------------------------------------------
        | Null Logger
        |----------------------------------------------------------------------
        |
        | Discards all log messages. Useful for testing or temporarily
        | disabling logging without changing application code.
        |
        | Options:
        |   - driver: Must be "null"
        |   - level: Not used, but can be set for consistency
        |
        */
        'null' => [
            'driver' => 'null',
            'level' => 'debug',
        ],

        /*
        |----------------------------------------------------------------------
        | Emergency Logger Example
        |----------------------------------------------------------------------
        |
        | A dedicated channel for emergency-level logs only.
        | Only logs at 'emergency' level will be written to this file.
        |
        */
        'emergency' => [
            'driver' => 'file',
            'path' => 'logs/emergency.log',
            'level' => 'emergency',  // Only emergency level and above
            'format' => '[{timestamp}] [{env}] EMERGENCY: {message} {context}',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Log Levels
    |--------------------------------------------------------------------------
    |
    | Log levels follow PSR-3 standards, in order of severity:
    |
    |   DEBUG      - Detailed debug information
    |   INFO       - Interesting events (user login, SQL logs)
    |   NOTICE     - Normal but significant events
    |   WARNING    - Exceptional occurrences that are not errors
    |   ERROR      - Runtime errors that don't require immediate action
    |   CRITICAL   - Critical conditions (component unavailable)
    |   ALERT      - Action must be taken immediately
    |   EMERGENCY  - System is unusable
    |
    | When you set a minimum level (e.g., 'warning'), only messages at that
    | level and above (error, critical, alert, emergency) will be logged.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Environment-Aware Logging
    |--------------------------------------------------------------------------
    |
    | The smartLog() method automatically adjusts log levels based on
    | the current environment:
    |
    |   Production  - Logs as INFO
    |   Staging     - Logs as NOTICE
    |   Testing     - Logs as WARNING
    |   Development - Logs as DEBUG
    |
    | This allows you to have different verbosity in different environments
    | without changing your code.
    |
    */
];
