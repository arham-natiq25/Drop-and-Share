<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public URL of the Vue frontend
    |--------------------------------------------------------------------------
    |
    | Used to build the human-shareable link that is handed back after an
    | upload, and to whitelist the origin for CORS.
    |
    */

    'frontend_url' => rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/'),

    /*
    |--------------------------------------------------------------------------
    | Where the zip archives live
    |--------------------------------------------------------------------------
    |
    | Deliberately OUTSIDE storage/app/public: that directory is symlinked to
    | public/storage, which would let anyone fetch an archive straight off
    | disk and skip the expiry check and download counter.
    |
    */

    'zips_path' => storage_path('app/private/zips'),

    'temp_path' => storage_path('app/temp'),

    /*
    |--------------------------------------------------------------------------
    | Upload limits
    |--------------------------------------------------------------------------
    |
    | Keep these at or below the PHP limits in .user.ini / php.ini, otherwise
    | large uploads are rejected by PHP before Laravel ever sees them.
    |
    */

    'max_file_size' => (int) env('UPLOAD_MAX_FILE_SIZE_MB', 100) * 1024 * 1024,

    'max_total_size' => (int) env('UPLOAD_MAX_TOTAL_SIZE_MB', 500) * 1024 * 1024,

    'max_files' => (int) env('UPLOAD_MAX_FILES', 50),

    /*
    |--------------------------------------------------------------------------
    | Share lifetime
    |--------------------------------------------------------------------------
    |
    | How many hours a share link stays alive before `shares:prune` removes
    | the zip from disk and marks the row expired.
    |
    */

    'expiry_hours' => (int) env('SHARE_EXPIRY_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME types
    |--------------------------------------------------------------------------
    */

    'allowed_mime_types' => [
        // Images
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        'image/bmp', 'image/tiff', 'image/heic', 'image/avif',

        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/rtf',
        'text/plain', 'text/csv', 'text/markdown',

        // Archives
        'application/zip', 'application/x-rar-compressed', 'application/vnd.rar',
        'application/x-7z-compressed', 'application/x-tar', 'application/gzip',
        'application/x-bzip2',

        // Audio / video
        'audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/ogg', 'audio/flac',
        'audio/mp4', 'audio/aac',
        'video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo',
        'video/x-matroska', 'video/mpeg',

        // Code / data
        'text/html', 'text/css', 'text/javascript', 'application/javascript',
        'application/json', 'application/xml', 'text/xml',

        // Fonts
        'font/ttf', 'font/otf', 'font/woff', 'font/woff2',
    ],

];
