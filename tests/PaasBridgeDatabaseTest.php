<?php
declare(strict_types=1);

namespace Shopware\Paas\Tests;

use PHPUnit\Framework\TestCase;

use function Platformsh\ShopwareBridge\mapPlatformShEnvironment;
use const Platformsh\ShopwareBridge\DEFAULT_MARIADB_VERSION;
use const Platformsh\ShopwareBridge\DEFAULT_POSTGRESQL_VERSION;

class PaasBridgeDatabaseTest extends TestCase
{
    protected $relationships;
    protected $defaultDbUrl;

    public function setUp(): void
    {
        parent::setUp();

        $this->relationships = [
            'database' => [
                [
                    'scheme' => 'mysql',
                    'username' => 'user',
                    'password' => '',
                    'host' => 'database.internal',
                    'port' => '3306',
                    'path' => 'main',
                    'query' => ['is_master' => true],
                    'type' => 'mysql:10.2'
                ]
            ]
        ];

        $this->defaultDbUrl = sprintf(
            '%s://%s:%s@%s:%s/%s?charset=utf8mb4&serverVersion=mariadb-10.2.12',
            'mysql',
            '',
            '',
            'localhost',
            3306,
            ''
        );

        putenv('PLATFORM_ROUTES=eyJodHRwczovL3Rlc3QudHN0LnNpdGUvIjogeyJwcmltYXJ5IjogdHJ1ZSwgImlkIjogInNob3B3YXJlIiwgInByb2R1Y3Rpb25fdXJsIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAiYXR0cmlidXRlcyI6IHt9LCAidHlwZSI6ICJ1cHN0cmVhbSIsICJ1cHN0cmVhbSI6ICJhcHAiLCAib3JpZ2luYWxfdXJsIjogImh0dHBzOi8ve2RlZmF1bHR9LyJ9LCAiaHR0cDovL3Rlc3Quc2l0ZS8iOiB7Im9yaWdpbmFsX3VybCI6ICJodHRwOi8ve2RlZmF1bHR9LyIsICJpZCI6IG51bGwsICJwcmltYXJ5IjogZmFsc2UsICJ0eXBlIjogInJlZGlyZWN0IiwgInRvIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAicHJvZHVjdGlvbl91cmwiOiAiaHR0cDovL3Rlc3Quc2l0ZS8ifX0=');
    }

    public function testNotOnPlatformshDoesNotSetDatabase() : void
    {
        mapPlatformShEnvironment();

        $this->assertArrayNotHasKey('DATABASE_URL', $_SERVER);
    }

    public function testNoRelationshipsBecauseBuild() : void
    {
        // Application name but no environment name means build hook.

        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');

        //putenv(sprintf('PLATFORM_RELATIONSHIPS=%s', base64_encode(json_encode($this->relationships))));

        mapPlatformShEnvironment();

        $this->assertEquals($this->defaultDbUrl, $_SERVER['DATABASE_URL']);
    }

    public function testNoDatabaseRelationshipInRuntime() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');

        $rels = $this->relationships;
        unset($rels['database']);

        putenv(sprintf('PLATFORM_RELATIONSHIPS=%s', base64_encode(json_encode($rels))));

        mapPlatformShEnvironment();

        $this->assertArrayNotHasKey('DATABASE_URL', $_SERVER);
    }

    /**
     * @dataProvider databaseVersionsProvider
     */
    public function testDatabaseRelationshipFormatters(string $type, string $scheme, string $expected) : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');

        $rels = $this->relationships;

        $rels['database'][0]['type'] = $type;
        $rels['database'][0]['scheme'] = $scheme;

        putenv(sprintf('PLATFORM_RELATIONSHIPS=%s', base64_encode(json_encode($rels))));

        mapPlatformShEnvironment();

        $this->assertEquals($expected, $_SERVER['DATABASE_URL']);
    }

    /**
     * @dataProvider databaseVersionsProvider
     */
    public function testDatabaseRelationshipFormattersFoundationV1(string $type, string $scheme, string $expected) : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');

        $rels = $this->relationships;

        $rels['database'][0]['scheme'] = $scheme;

        // On Foundation 1, there is no `type` property.  Therefore we do not know what the DB version
        // should be.  So we fall back to the default guesses and hope. If someone wants to use a different
        // version, they should move to Foundation 3.
        unset($rels['database'][0]['type']);
        switch ($scheme) {
            case 'mysql':
                $default = 'mariadb-' . DEFAULT_MARIADB_VERSION . '.12';
                break;
            case 'pgsql':
                $default = DEFAULT_POSTGRESQL_VERSION;
        }
        $expected = preg_replace('/serverVersion=.+$/', 'serverVersion=' . $default, $expected);

        putenv(sprintf('PLATFORM_RELATIONSHIPS=%s', base64_encode(json_encode($rels))));

        mapPlatformShEnvironment();

        $this->assertEquals($expected, $_SERVER['DATABASE_URL']);
    }


    public static function databaseVersionsProvider() : iterable
    {
        yield 'postgresql 9.6' => [
            'type' => 'postgresql:9.6',
            'scheme' => 'pgsql',
            'expected' => 'pgsql://user:@database.internal:3306/main?serverVersion=9.6',
        ];
        yield 'postgresql 10' => [
            'type' => 'postgresql:10',
            'scheme' => 'pgsql',
            'expected' => 'pgsql://user:@database.internal:3306/main?serverVersion=10',
        ];

        // This is the oddball that doesn't have a .0, because reasons.
        yield 'mariadb 10.2' => [
            'type' => 'mariadb:10.2',
            'scheme' => 'mysql',
            'expected' => 'mysql://user:@database.internal:3306/main?charset=utf8mb4&serverVersion=mariadb-10.2.12',
        ];

        yield 'mariadb 10.4' => [
            'type' => 'mariadb:10.4',
            'scheme' => 'mysql',
            'expected' => 'mysql://user:@database.internal:3306/main?charset=utf8mb4&serverVersion=mariadb-10.4.0',
        ];

        yield 'mariadb 10.2 aliased' => [
            'type' => 'mysql:10.2',
            'scheme' => 'mysql',
            'expected' => 'mysql://user:@database.internal:3306/main?charset=utf8mb4&serverVersion=mariadb-10.2.12',
        ];

        yield 'mysql 8' => [
            'type' => 'oracle-mysql:8.0',
            'scheme' => 'mysql',
            'expected' => 'mysql://user:@database.internal:3306/main?charset=utf8mb4&serverVersion=8.0',
        ];
    }
}
