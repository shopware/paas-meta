<?php

namespace Shopware\Paas\Tests;

use PHPUnit\Framework\TestCase;
use function Platformsh\ShopwareBridge\mapPlatformShEnvironment;

class PaasBridgeShopwareTest extends TestCase
{
    public function setUp(): void
    {
        putenv('PLATFORM_ROUTES=eyJodHRwczovL3Rlc3QudHN0LnNpdGUvIjogeyJwcmltYXJ5IjogdHJ1ZSwgImlkIjogInNob3B3YXJlIiwgInByb2R1Y3Rpb25fdXJsIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAiYXR0cmlidXRlcyI6IHt9LCAidHlwZSI6ICJ1cHN0cmVhbSIsICJ1cHN0cmVhbSI6ICJhcHAiLCAib3JpZ2luYWxfdXJsIjogImh0dHBzOi8ve2RlZmF1bHR9LyJ9LCAiaHR0cDovL3Rlc3Quc2l0ZS8iOiB7Im9yaWdpbmFsX3VybCI6ICJodHRwOi8ve2RlZmF1bHR9LyIsICJpZCI6IG51bGwsICJwcmltYXJ5IjogZmFsc2UsICJ0eXBlIjogInJlZGlyZWN0IiwgInRvIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAicHJvZHVjdGlvbl91cmwiOiAiaHR0cDovL3Rlc3Quc2l0ZS8ifX0=');
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
    }

    public function testAppUrlSet(): void
    {
        mapPlatformShEnvironment();

        $this->assertEquals('https://test.tst.site/', getenv('APP_URL'));
    }
}