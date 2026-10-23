<?php
/**
 * Datasource configuration
 */
#[\AllowDynamicProperties]
class DATABASE_CONFIG
{
    /**
     * Default connection
     *
     * @var array
     */
    public $default = [
        'datasource' => 'Database/Mysql',
        'host' => 'localhost',
    ];
}
