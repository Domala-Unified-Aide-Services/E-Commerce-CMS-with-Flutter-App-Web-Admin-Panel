<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    /**
     * ---------------------------------------------------------------
     * Default Connection Group
     * ---------------------------------------------------------------
     * The default database connection group name.
     */
    public string $defaultGroup = 'default';

    /**
     * ---------------------------------------------------------------
     * Default Database Connection Settings
     * ---------------------------------------------------------------
     * These settings define how your application connects to the database.
     */
    public array $default = [
        'DSN'      => '',
        'hostname' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'database' => 'ecommerce_db',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => (ENVIRONMENT !== 'production'),
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];

    /**
     * Constructor
     * Loads database configuration values from the .env file if available.
     */
    public function __construct()
    {
        parent::__construct();

        // Override values from .env file
        $this->default['hostname'] = env('database.default.hostname', $this->default['hostname']);
        $this->default['username'] = env('database.default.username', $this->default['username']);
        $this->default['password'] = env('database.default.password', $this->default['password']);
        $this->default['database'] = env('database.default.database', $this->default['database']);
        $this->default['DBDriver'] = env('database.default.DBDriver', $this->default['DBDriver']);
        $this->default['DBPrefix'] = env('database.default.DBPrefix', $this->default['DBPrefix']);
        $this->default['port']     = (int) env('database.default.port', (string) $this->default['port']);
    }
}
