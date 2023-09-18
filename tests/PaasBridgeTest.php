<?php
declare(strict_types=1);

namespace Shopware\Paas\Tests;

use PHPUnit\Framework\TestCase;

use function Platformsh\ShopwareBridge\mapPlatformShEnvironment;

class PaasBridgeTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('PLATFORM_ROUTES=eyJodHRwczovL3Rlc3QudHN0LnNpdGUvIjogeyJwcmltYXJ5IjogdHJ1ZSwgImlkIjogInNob3B3YXJlIiwgInByb2R1Y3Rpb25fdXJsIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAiYXR0cmlidXRlcyI6IHt9LCAidHlwZSI6ICJ1cHN0cmVhbSIsICJ1cHN0cmVhbSI6ICJhcHAiLCAib3JpZ2luYWxfdXJsIjogImh0dHBzOi8ve2RlZmF1bHR9LyJ9LCAiaHR0cDovL3Rlc3Quc2l0ZS8iOiB7Im9yaWdpbmFsX3VybCI6ICJodHRwOi8ve2RlZmF1bHR9LyIsICJpZCI6IG51bGwsICJwcmltYXJ5IjogZmFsc2UsICJ0eXBlIjogInJlZGlyZWN0IiwgInRvIjogImh0dHBzOi8vdGVzdC50c3Quc2l0ZS8iLCAicHJvZHVjdGlvbl91cmwiOiAiaHR0cDovL3Rlc3Quc2l0ZS8ifX0=');
    }

    public function testDoesNotRunWithoutPlatformshVariables() : void
    {
        mapPlatformShEnvironment();

        $this->assertFalse(getenv('APP_SECRET'));
    }

    public function testSetAppSecret() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');

        mapPlatformShEnvironment();

        $this->assertEquals('test', $_SERVER['APP_SECRET']);
        $this->assertEquals('test', getenv('APP_SECRET'));
    }

    public function testDontChangeAppSecret() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
        putenv('APP_SECRET=original');

        mapPlatformShEnvironment();

        $this->assertEquals('original', $_SERVER['APP_SECRET']);
        $this->assertEquals('original', getenv('APP_SECRET'));
    }

    public function testAppEnvAlreadySetInServer() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
        putenv('APP_ENV=dev');

        mapPlatformShEnvironment();

        $this->assertEquals('dev', $_SERVER['APP_ENV']);
        $this->assertEquals('dev', getenv('APP_ENV'));
    }

    public function testAppEnvAlreadySetInEnv() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
        putenv('APP_ENV=dev');

        mapPlatformShEnvironment();

        $this->assertEquals('dev', $_SERVER['APP_ENV']);
        $this->assertEquals('dev', getenv('APP_ENV'));
    }

    public function testAppEnvNeedsDefault() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
        putenv('PLATFORM_PROJECT_ENTROPY=test');

        mapPlatformShEnvironment();

        $this->assertEquals('prod', $_SERVER['APP_ENV']);
        $this->assertEquals('prod', getenv('APP_ENV'));
    }

    public function testSwiftmailer() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
        putenv('PLATFORM_PROJECT_ENTROPY=test');

        mapPlatformShEnvironment();

        $this->assertEquals('smtp://1.2.3.4:25/', $_SERVER['MAILER_URL']);
        $this->assertEquals('smtp://1.2.3.4:25/', getenv('MAILER_URL'));
    }

    public function testSwiftmailerDisabledMailEnvVarEmpty() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=');

        mapPlatformShEnvironment();

        $this->assertEquals('null://localhost:25/', $_SERVER['MAILER_URL']);
        $this->assertEquals('null://localhost:25/', getenv('MAILER_URL'));
    }

    public function testSwiftmailerDisabledMailEnvVarNotDefined() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');

        mapPlatformShEnvironment();

        $this->assertEquals('null://localhost:25/', $_SERVER['MAILER_URL']);
        $this->assertEquals('null://localhost:25/', getenv('MAILER_URL'));
    }

    public function testMailer() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_SMTP_HOST=1.2.3.4');
        putenv('PLATFORM_PROJECT_ENTROPY=test');

        mapPlatformShEnvironment();

        $this->assertEquals('smtp://1.2.3.4:25/', $_SERVER['MAILER_DSN']);
        $this->assertEquals('smtp://1.2.3.4:25/', getenv('MAILER_DSN'));
    }

    public function testMailerDisabledMailEnvVarEmpty() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');
        putenv('PLATFORM_SMTP_HOST=');

        mapPlatformShEnvironment();

        $this->assertEquals('null://localhost:25/', $_SERVER['MAILER_DSN']);
        $this->assertEquals('null://localhost:25/', getenv('MAILER_DSN'));
    }

    public function testMailerDisabledMailEnvVarNotDefined() : void
    {
        putenv('PLATFORM_APPLICATION_NAME=test');
        putenv('PLATFORM_ENVIRONMENT=test');
        putenv('PLATFORM_PROJECT_ENTROPY=test');

        mapPlatformShEnvironment();

        $this->assertEquals('null://localhost:25/', $_SERVER['MAILER_DSN']);
        $this->assertEquals('null://localhost:25/', getenv('MAILER_DSN'));
    }
}
