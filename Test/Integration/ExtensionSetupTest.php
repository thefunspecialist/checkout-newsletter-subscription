<?php

namespace MageSuite\CheckoutNewsletterSubscription\Test\Integration;

class ExtensionSetupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $moduleName = 'MageSuite_CheckoutNewsletterSubscription';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    public function testCheckoutNewsletterSubscriptionIsRegisteredAsModule()
    {
        /** @var \Magento\Framework\Component\ComponentRegistrar $componentRegistrar */
        $componentRegistrar = new \Magento\Framework\Component\ComponentRegistrar();
        $this->assertArrayHasKey(
            $this->moduleName,
            $componentRegistrar->getPaths(\Magento\Framework\Component\ComponentRegistrar::MODULE)
        );
    }

    public function testCheckoutNewsletterSubscriptionIsEnabled()
    {
        /** @var \Magento\Framework\Module\ModuleList $moduleList */
        $moduleList = $this->objectManager->get(\Magento\Framework\Module\ModuleList::class);
        $this->assertTrue($moduleList->has($this->moduleName));
    }
}
