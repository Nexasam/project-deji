<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProductionDatabaseConfigurationTest extends TestCase
{
    public function test_mysql_compatible_connections_default_to_innodb(): void
    {
        $this->assertSame('InnoDB', config('database.connections.mysql.engine'));
        $this->assertSame('InnoDB', config('database.connections.mariadb.engine'));
    }
}
