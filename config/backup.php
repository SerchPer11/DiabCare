<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the database backup system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Maximum Backups
    |--------------------------------------------------------------------------
    |
    | This value determines the maximum number of backup files to keep.
    | When this limit is exceeded, the oldest backups will be deleted.
    |
    */

    'max_backups' => env('BACKUP_MAX_FILES', 10),

    /*
    |--------------------------------------------------------------------------
    | Backup Path
    |--------------------------------------------------------------------------
    |
    | The path where backup files will be stored. This path is relative
    | to the storage/app directory.
    |
    */

    'backup_path' => env('BACKUP_PATH', 'backups'),

    /*
    |--------------------------------------------------------------------------
    | Compression
    |--------------------------------------------------------------------------
    |
    | Whether to compress backup files using gzip.
    |
    */

    'compress' => env('BACKUP_COMPRESS', false),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection to use for backups. If null, the default
    | connection will be used.
    |
    */

    'connection' => env('BACKUP_DB_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Backup Schedule
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic backup scheduling.
    |
    */

    'schedule' => [
        'enabled' => env('BACKUP_SCHEDULE_ENABLED', false),
        'frequency' => env('BACKUP_SCHEDULE_FREQUENCY', 'daily'), // daily, weekly, monthly
        'time' => env('BACKUP_SCHEDULE_TIME', '02:00'), // 24-hour format
        'keep_days' => env('BACKUP_KEEP_DAYS', 30), // Days to keep backups
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configuration for backup notifications.
    |
    */

    'notifications' => [
        'enabled' => env('BACKUP_NOTIFICATIONS_ENABLED', false),
        'email' => env('BACKUP_NOTIFICATION_EMAIL', null),
        'on_success' => env('BACKUP_NOTIFY_SUCCESS', false),
        'on_failure' => env('BACKUP_NOTIFY_FAILURE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification
    |--------------------------------------------------------------------------
    |
    | Settings for backup file verification.
    |
    */

    'verification' => [
        'enabled' => env('BACKUP_VERIFICATION_ENABLED', true),
        'algorithm' => env('BACKUP_HASH_ALGORITHM', 'sha256'),
    ],

];