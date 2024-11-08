<?php

namespace Shopware\Paas\Tests;

use function Platformsh\ShopwareBridge\mapPlatformShEnvironment;

class PaasBridgeShopwareClusterTest extends PaasBridgeShopwareTest
{
    public function setUp(): void
    {
        parent::setUp();
        // Switched primary URL from first to second position routes. This is a real-world condition we're facing with PSH.
        //  Yes, both routes also have the type "shopware" assigned.
        \putenv('PLATFORM_ROUTES='.\base64_encode('{"https://test.tst.site/": {"primary": false, "id": "shopware", "production_url": null, "attributes": {}, "type": "upstream", "upstream": "app", "original_url": "https://{default}/"}, "https://prod.site/": {"original_url": "http://{default}/", "id": "shopware", "primary": true, "production_url": null, "attributes": {}, "type": "upstream", "production_url": null}}'));
    }

    public function testAppUrlSet(): void
    {
        mapPlatformShEnvironment();

        $this->assertEquals('https://prod.site/', getenv('APP_URL'));
    }
}
