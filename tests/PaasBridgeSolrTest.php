<?php
declare(strict_types=1);

namespace Shopware\Paas\Tests;

use PHPUnit\Framework\TestCase;

use function Platformsh\ShopwareBridge\mapPlatformShEnvironment;

class PaasBridgeSolrTest extends TestCase
{
    protected $relationships;
    protected $defaultValues;

    public function setUp(): void
    {
        parent::setUp();

        $this->relationships = [
            'solr' => [
                [
                    'service' => 'solrsearch',
                    'ip' => '169.254.69.109',
                    'hostname' => 'xxx.solrsearch.service._.us-2.platformsh.site',
                    'cluster' => 'xxx-master-7rqtwti',
                    'host' => 'solr.internal',
                    'rel' => 'collection1',
                    'path' => 'solr/collection1',
                    'scheme' => 'solr',
                    'type' => 'solr:7.7',
                    'port' => 8080
                ]
            ]
        ];

        $this->defaultValues = [];

        putenv('PLATFORM_ROUTES=eyJodHRwczovL3Rlc3QudHN0LnNpdGUvIjogeyJwcmltYXJ5IjogdHJ1ZSwgImlkIjogInNob3B3YXJlIiwgInByb2R1Y3Rpb25fdXJsIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAiYXR0cmlidXRlcyI6IHt9LCAidHlwZSI6ICJ1cHN0cmVhbSIsICJ1cHN0cmVhbSI6ICJhcHAiLCAib3JpZ2luYWxfdXJsIjogImh0dHBzOi8ve2RlZmF1bHR9LyJ9LCAiaHR0cDovL3Rlc3Quc2l0ZS8iOiB7Im9yaWdpbmFsX3VybCI6ICJodHRwOi8ve2RlZmF1bHR9LyIsICJpZCI6IG51bGwsICJwcmltYXJ5IjogZmFsc2UsICJ0eXBlIjogInJlZGlyZWN0IiwgInRvIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAicHJvZHVjdGlvbl91cmwiOiAiaHR0cDovL3Rlc3Quc2l0ZS8ifX0=');

    }

    public function testNotOnPlatformshDoesNotSetEnvVar(): void
    {
        mapPlatformShEnvironment();

        $this->assertArrayNotHasKey('SOLR_DSN', $_SERVER);
        $this->assertArrayNotHasKey('SOLR_CORE', $_SERVER);
    }

    public function testNoSolrRelationship(): void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');

        $rels = $this->relationships;
        unset($rels['solr']);

        putenv(sprintf('PLATFORM_RELATIONSHIPS=%s', base64_encode(json_encode($rels))));

        mapPlatformShEnvironment();

        $this->assertArrayNotHasKey('SOLR_DSN', $_SERVER);
        $this->assertArrayNotHasKey('SOLR_CORE', $_SERVER);
    }

    public function testRelationshipSet(): void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
        putenv(sprintf('PLATFORM_RELATIONSHIPS=%s', base64_encode(json_encode($this->relationships))));

        mapPlatformShEnvironment();

        $this->assertEquals('http://solr.internal:8080/solr', $_SERVER['SOLR_DSN']);
        $this->assertEquals('collection1', $_SERVER['SOLR_CORE']);
    }
}
