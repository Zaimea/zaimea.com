<?php

use Laravel\Pulse\Http\Middleware\Authorize;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders;

return [

    /*
    |--------------------------------------------------------------------------
    | Pulse Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain which the Pulse dashboard will be accessible from.
    | When set to null, the dashboard will reside under the same domain as
    | the application. Remember to configure your DNS entries correctly.
    |
    */

    'domain' => env('PULSE_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Path
    |--------------------------------------------------------------------------
    |
    | This is the path which the Pulse dashboard will be accessible from. Feel
    | free to change this path to anything you'd like. Note that this won't
    | affect the path of the internal API that is never exposed to users.
    |
    */

    'path' => env('PULSE_PATH', 'pulse'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Master Switch
    |--------------------------------------------------------------------------
    |
    | This configuration option may be used to completely disable all Pulse
    | data recorders regardless of their individual configurations. This
    | provides a single option to quickly disable all Pulse recording.
    |
    */

    'enabled' => env('PULSE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Pulse Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration option determines which storage driver will be used
    | while storing entries from Pulse's recorders. In addition, you also
    | may provide any options to configure the selected storage driver.
    |
    */

    'storage' => [
        'driver' => env('PULSE_STORAGE_DRIVER', 'database'),

        'database' => [
            'connection' => env('PULSE_DB_CONNECTION'),
            'chunk' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Ingest Driver
    |--------------------------------------------------------------------------
    |
    | This configuration options determines the ingest driver that will be used
    | to capture entries from Pulse's recorders. Ingest drivers are great to
    | free up your request workers quickly by offloading the data storage.
    |
    */

    'ingest' => [
        'driver' => env('PULSE_INGEST_DRIVER', 'storage'),

        'buffer' => env('PULSE_INGEST_BUFFER', 5_000),

        'trim' => [
            'lottery' => [1, 1_000],
            'keep' => '7 days',
        ],

        'redis' => [
            'connection' => env('PULSE_REDIS_CONNECTION'),
            'chunk' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Cache Driver
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the cache driver that will be used
    | for various tasks, including caching dashboard results, establishing
    | locks for events that should only occur on one server and signals.
    |
    */

    'cache' => env('PULSE_CACHE_DRIVER'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every Pulse route, giving you the
    | chance to add your own middleware to this list or change any of the
    | existing middleware. Of course, reasonable defaults are provided.
    |
    */

    'middleware' => [
        'web',
        Authorize::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Recorders
    |--------------------------------------------------------------------------
    |
    | The following array lists the "recorders" that will be registered with
    | Pulse, along with their configuration. Recorders gather application
    | event data from requests and tasks to pass to your ingest driver.
    |
    */

    'recorders' => [
        Recorders\CacheInteractions::class => [
            'enabled' => env('PULSE_CACHE_INTERACTIONS_ENABLED', true),
            'sample_rate' => env('PULSE_CACHE_INTERACTIONS_SAMPLE_RATE', 1),
            'ignore' => [
                ...Pulse::defaultVendorCacheKeys(),
            ],
            'groups' => [
                '/^job-exceptions:.*/' => 'job-exceptions:*',
                // '/:\d+/' => ':*',
            ],
        ],

        Recorders\Exceptions::class => [
            'enabled' => env('PULSE_EXCEPTIONS_ENABLED', true),
            'sample_rate' => env('PULSE_EXCEPTIONS_SAMPLE_RATE', 1),
            'location' => env('PULSE_EXCEPTIONS_LOCATION', true),
            'ignore' => [
                // '/^Package\\\\Exceptions\\\\/',
            ],
        ],

        Recorders\Queues::class => [
            'enabled' => env('PULSE_QUEUES_ENABLED', true),
            'sample_rate' => env('PULSE_QUEUES_SAMPLE_RATE', 1),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        Recorders\Servers::class => [
            'server_name' => env('PULSE_SERVER_NAME', gethostname()),
            'directories' => explode(':', env('PULSE_SERVER_DIRECTORIES', '/')),
        ],

        Recorders\SlowJobs::class => [
            'enabled' => env('PULSE_SLOW_JOBS_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_JOBS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_JOBS_THRESHOLD', 1000),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        Recorders\SlowOutgoingRequests::class => [
            'enabled' => env('PULSE_SLOW_OUTGOING_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_OUTGOING_REQUESTS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_OUTGOING_REQUESTS_THRESHOLD', 1000),
            'ignore' => [
                // '#^http://127\.0\.0\.1:13714#', // Inertia SSR...
            ],
            'groups' => [
                // '#^https://api\.github\.com/repos/.*$#' => 'api.github.com/repos/*',
                // '#^https?://([^/]*).*$#' => '\1',
                // '#/\d+#' => '/*',
            ],
        ],

        Recorders\SlowQueries::class => [
            'enabled' => env('PULSE_SLOW_QUERIES_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_QUERIES_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_QUERIES_THRESHOLD', 1000),
            'location' => env('PULSE_SLOW_QUERIES_LOCATION', true),
            'max_query_length' => env('PULSE_SLOW_QUERIES_MAX_QUERY_LENGTH'),
            'ignore' => [
                '/(["`])pulse_[\w]+?\1/', // Pulse tables...
                '/(["`])telescope_[\w]+?\1/', // Telescope tables...
            ],
        ],

        Recorders\SlowRequests::class => [
            'enabled' => env('PULSE_SLOW_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_REQUESTS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_REQUESTS_THRESHOLD', 1000),
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'$#', // Pulse dashboard...
                '#^/telescope#', // Telescope dashboard...
            ],
        ],

        Recorders\UserJobs::class => [
            'enabled' => env('PULSE_USER_JOBS_ENABLED', true),
            'sample_rate' => env('PULSE_USER_JOBS_SAMPLE_RATE', 1),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        Recorders\UserRequests::class => [
            'enabled' => env('PULSE_USER_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_USER_REQUESTS_SAMPLE_RATE', 1),
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'$#', // Pulse dashboard...
                '#^/telescope#', // Telescope dashboard...
            ],
        ],

        ZaimeaLabs\Pulse\Analytics\Recorders\Actions::class => [
            'enabled'           => env('PULSE_ACTION_ENABLED', true),
            'on_retrieved'      => false,
            'on_creating'       => false,
            'on_created'        => true,
            'on_updating'       => false,
            'on_updated'        => true,
            'on_saving'         => false,
            'on_saved'          => true,
            'on_restoring'      => true,
            'on_restored'       => true,
            'on_replicate'      => true,
            'on_deleting'       => false,
            'on_deleted'        => true,
            'on_forceDeleting'  => false,
            'on_forceDeleted'   => true,
            'obervers' => [
                \App\Models\User::class,
                \App\Models\Group::class,
                \Zaimea\Models\User\Record::class,
                \Zaimea\Models\Group\Client::class,
                \Zaimea\Models\Group\Config::class,
                \Zaimea\Models\Group\Group::class,
                \Zaimea\Models\Group\Holiday::class,
                \Zaimea\Models\Group\Invitation::class,
                \Zaimea\Models\Group\Locking::class,
                \Zaimea\Models\Group\Membership::class,
                \Zaimea\Models\Group\MonthlyQuotas::class,
                \Zaimea\Models\Group\Plugin::class,
                \Zaimea\Models\Group\Project::class,
                \Zaimea\Models\Group\ProjectTask::class,
                \Zaimea\Models\Group\ProjectTemplate::class,
                \Zaimea\Models\Group\Record::class,
                \Zaimea\Models\Group\Role::class,
                \Zaimea\Models\Group\RolePermission::class,
                \Zaimea\Models\Client\Client::class,
                \Zaimea\Models\Client\Clientship::class,
                \Zaimea\Models\Client\Invitation::class,
            ],
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'$#', // Pulse dashboard...
                '#^/login#',
                '#^/logout#',
            ],
        ],

        ZaimeaLabs\Pulse\Analytics\Recorders\Visits::class => [
            'enabled' => env('PULSE_VISIT_ENABLED', true),
            'ajax_requests' => true, // Disable visit in Ajax mode, set it to false.
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'$#', // Pulse dashboard...
                '#^/telescope#', // Telescope dashboard...
                '#^/_ignition/health-check#',
                '#^/login#',
                '#^/logout#',
                '#^/livewire#',
                '#^/_debugbar#',
            ],

            /*
            * If you want to delete visit rows after some days, you can change this to 360 for example,
            * but you don't like to delete rows you can change it to 0.
            *
            * For this feature you need Task-Scheduling => https://laravel.com/docs/11.x/scheduling
            */
            'delete_days' => 0,
        ],

        ZaimeaLabs\Pulse\Analytics\Recorders\Authentications::class => [
            'enabled' => env('PULSE_AUTHENTICATION_ENABLED', true),
            'guard' => 'web', // The correct guard.
            'ignore' => [
                //'login',
                //'logout',
            ],
        ],

        ZaimeaLabs\Pulse\Analytics\Recorders\Campaign::class => [
            'enabled' => env('PULSE_CAMPAIGN_ENABLED', true),
            'catch' => [
                '#^ctm_campaign=advertisement&ctm_source=Custura.de$#',
                '#^ctm=github$#',
            ],
        ],
    ],
];
