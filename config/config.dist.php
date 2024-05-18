<?php

// const IS_DEVELOPMENT = true;

const SESSION_COOKIE_NAME = 'zp';

const PROJECT_CONFIG = [
    'site' => [
        // locale
        'locale' => 'de',
        // site name
        'name' => '...',
        // site owner name
        'owner' => '...',
        // page title
        'title' => '...',
        // page title short
        'title_short' => '...',
        // site address
        'url' => 'https://.../',
        // logo
        'logo' => '',
        // version
        'version' => '1.0.0',
        // contacts
        'contacts' => [],
        // email sender address
        'email_sender' => '',
        // meta
        'meta' => [
            'description' => '',
            'description_en' => '',
        ],
    ],
    /*************************************************************
     * Developer admin
     *************************************************************/
    'admin' => [
        'discord' => 0,
    ],
    /*************************************************************
     * Cache settings Database, APC, Memcached
     *************************************************************/
    'cache' => [
        'active' => 'FALSE', // Set Database, APC, Memcached, Redis or FALSE to not use any caching
        'classes' => [
            'Database' => [
                'method' => 'App\Lib\Cache\Database',
            ],
            'APC' => [
                'method' => 'App\Lib\Cache\APC',
            ],
            'Redis' => [
                'method' => 'App\Lib\Cache\Redis',
                'server' => [
                    'host' => 'localhost',
                    'port' => 6379
                ]
            ],
            'Memcached' => [
                'method' => 'Memcached',
                'servers' => [
                    [
                        'host' => 'localhost',
                        'port' => 11211
                    ]
                ]
            ],
        ]
    ],
    /*************************************************************
     * Database related settings
     *************************************************************/
    'db' => [
        'connection' => [
            'use' => 'mysql', // Default database type for everything (mysql, postgresql)
            'mysql' => [
                'dsn' => 'mysql:host=mariadb',
                'database' => '...',
                'username' => '...',
                'password' => '...',
            ],
            'postgresql' => [
                'dsn' => 'pgsql:host=postgresql',
                'database' => '...',
                'username' => '...',
                'password' => '...',
            ],
            'mongodb' => [
                'dsn' => 'mongodb://mongodb:27017',
                'collection' => '...',
                'options' => [
                    'username' => '...',
                    'password' => '...',
                ],
            ],
        ]
    ],
    /*************************************************************
     * PHP ini settings - Please refer \apache\bin\php.ini
     *************************************************************/
    'php' => [// Defines the default timezone used by the date functions
    ],
    /*************************************************************
     * TWITCH API Settings
     *************************************************************/
    'twitch' => [
        'user_id' => '...',
        'token' => '...',
        'refresh_token' => '...',
        'client_id' => '...',
        'client_secret' => '...',
        'redirect_uri' => 'https://.../auth/twitch/',
    ],
    /*************************************************************
     * Discord API Settings
     *************************************************************/
    'discord' => [
        'oauth2' => [
            'client_id' => '...',
            'client_secret' => '...',
            'bot_token' => '...',
            'authorize_url' => 'https://discordapp.com/api/oauth2/authorize',
            'token_url' => 'https://discordapp.com/api/oauth2/token',
            'revoke_url' => 'https://discordapp.com/api/oauth2/token/revoke',
            'api_url_user' => 'https://discordapp.com/api/v6/users/@me',
            'api_url_guild' => 'https://discordapp.com/api/users/@me/guilds',
            'api_url_guild_members' => 'https://discordapp.com/api/v6/guilds/{guild_id}/members/{member_id}',
            'redirect_params' => '?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&response_type=code&scope=identify',
            'redirect_uri' => 'https://.../auth/discord/',
        ],
        'join_url' => 'https://...',
    ]
];
