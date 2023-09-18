<?php
declare(strict_types=1);

namespace Shopware\Paas\Tests;

use PHPUnit\Framework\TestCase;

use function Platformsh\ShopwareBridge\mapPlatformShEnvironment;

class PaasBridgeMongoDatabaseTest extends TestCase
{
    protected $relationships;
    protected $defaultValues;

    public function setUp(): void
    {
        parent::setUp();

        $this->relationships = [
            'mongodatabase' => [
                [
                    'scheme'   => 'mongodb',
                    'username' => 'main_username',
                    'password' => 'main_password',
                    'host'     => 'mongodatabase.internal',
                    'port'     => 27017,
                    'path'     => 'main',
                    'query'    => ['is_master' => true],
                ]
            ]
        ];

        $this->defaultValues = [];

        putenv('PLATFORM_ROUTES=eyJodHRwczovL3Rlc3QudHN0LnNpdGUvIjogeyJwcmltYXJ5IjogdHJ1ZSwgImlkIjogInNob3B3YXJlIiwgInByb2R1Y3Rpb25fdXJsIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAiYXR0cmlidXRlcyI6IHt9LCAidHlwZSI6ICJ1cHN0cmVhbSIsICJ1cHN0cmVhbSI6ICJhcHAiLCAib3JpZ2luYWxfdXJsIjogImh0dHBzOi8ve2RlZmF1bHR9LyJ9LCAiaHR0cDovL3Rlc3Quc2l0ZS8iOiB7Im9yaWdpbmFsX3VybCI6ICJodHRwOi8ve2RlZmF1bHR9LyIsICJpZCI6IG51bGwsICJwcmltYXJ5IjogZmFsc2UsICJ0eXBlIjogInJlZGlyZWN0IiwgInRvIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAicHJvZHVjdGlvbl91cmwiOiAiaHR0cDovL3Rlc3Quc2l0ZS8ifX0=');
    }

    public function testNotOnPlatformshDoesNotSetDatabase(): void
    {
        mapPlatformShEnvironment();

        $this->assertArrayNotHasKey('MONGODB_SERVER', $_SERVER);
        $this->assertArrayNotHasKey('MONGODB_DB', $_SERVER);
        $this->assertArrayNotHasKey('MONGODB_USERNAME', $_SERVER);
        $this->assertArrayNotHasKey('MONGODB_PASSWORD', $_SERVER);
    }

    public function testNoDatabaseRelationship(): void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');

        $rels = $this->relationships;
        unset($rels['mongodatabase']);

        putenv(sprintf('PLATFORM_RELATIONSHIPS=%s', base64_encode(json_encode($rels))));

        mapPlatformShEnvironment();

        $this->assertArrayNotHasKey('MONGODB_SERVER', $_SERVER);
        $this->assertArrayNotHasKey('MONGODB_DB', $_SERVER);
        $this->assertArrayNotHasKey('MONGODB_USERNAME', $_SERVER);
        $this->assertArrayNotHasKey('MONGODB_PASSWORD', $_SERVER);
    }

    public function testDatabaseRelationshipSet(): void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
        putenv(sprintf('PLATFORM_RELATIONSHIPS=%s', base64_encode(json_encode($this->relationships))));

        mapPlatformShEnvironment();

        $this->assertEquals('mongodb://mongodatabase.internal:27017', $_SERVER['MONGODB_SERVER']);
        $this->assertEquals('main', $_SERVER['MONGODB_DB']);
        $this->assertEquals('main_username', $_SERVER['MONGODB_USERNAME']);
        $this->assertEquals('main_password', $_SERVER['MONGODB_PASSWORD']);
    }
}
