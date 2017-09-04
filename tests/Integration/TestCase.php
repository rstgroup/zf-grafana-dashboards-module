<?php


namespace RstGroup\ZfGrafanaModule\Tests\Integration;


use PHPUnit_Extensions_Database_DB_IDatabaseConnection;

abstract class TestCase extends \PHPUnit_Extensions_Database_TestCase
{
    const SCHEMA = 'test';


    /** @var \PDO */
    static $PDO;

    /** @var  array */
    static $config;

    /**
     * @return string[]
     */
    abstract public static function getTablesDefinition();

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        return $this->createDefaultDBConnection(
            static::getPDO(), static::SCHEMA
        );
    }

    public static function setUpBeforeClass()
    {
        self::$config = require __DIR__ . '/../config/tests.config.php';
    }

    public function setUp()
    {
        foreach(static::getTablesDefinition() as $tableSql) {
            $stmt = $this->getConnection()->getConnection()->prepare(
                $tableSql
            );

            // make sure table is created
            $this->assertTrue($stmt->execute());
        }

    }

    public function tearDown()
    {
        foreach (static::getTablesDefinition() as $tableName => $tableSql) {
            $stmt = $this->getConnection()->getConnection()->prepare(
                'DROP TABLE IF EXISTS ' . $tableName
            );

            // make sure table is removed
            $this->assertTrue($stmt->execute());
        }
    }

    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * @return \PDO
     */
    private static function getPDO()
    {
        if (!self::$PDO) {
            $config = self::getConfig()['db'];

            self::$PDO = new \PDO($config['dsn'], $config['user'], $config['password']);
        }

        return self::$PDO;
    }
}
