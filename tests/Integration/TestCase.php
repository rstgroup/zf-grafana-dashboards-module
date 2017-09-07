<?php


namespace RstGroup\ZfGrafanaModule\Tests\Integration;


use Doctrine\DBAL\Driver\PDOConnection;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;

abstract class TestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /** @var PDOConnection */
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
            static::getPDO(), self::$config['db']['schema']
        );
    }

    public static function setUpBeforeClass()
    {
        self::$config = require __DIR__ . '/../config/tests.config.php';
    }

    public function setUp()
    {
        foreach(static::getTablesDefinition() as $tableName => $tableSql) {
            $stmt = $this->getConnection()->getConnection()->prepare(
                $tableSql
            );

            // make sure table is created
            $this->assertTrue($stmt->execute(), sprintf('Could not create table %s.', $tableName));
        }

    }

    public function tearDown()
    {
        foreach (static::getTablesDefinition() as $tableName => $tableSql) {
            $stmt = $this->getConnection()->getConnection()->prepare(
                'DROP TABLE IF EXISTS ' . $tableName
            );

            // make sure table is removed
            $this->assertTrue($stmt->execute(), sprintf('Could not drop table %s', $tableName));
        }
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet([]);
    }

    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * @return PDOConnection
     */
    private static function getPDO()
    {
        if (!self::$PDO) {
            $config = self::getConfig()['db'];

            self::$PDO = new PDOConnection($config['dsn'], $config['user'], $config['password']);
        }

        return self::$PDO;
    }
}
