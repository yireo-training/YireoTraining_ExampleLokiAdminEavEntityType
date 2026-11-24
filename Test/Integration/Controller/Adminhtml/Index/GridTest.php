<?php declare(strict_types=1);

namespace YireoTraining\ExampleLokiAdminEavEntityType\Test\Integration\Controller\Adminhtml\Index;

use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class GridTest extends AbstractBackendController
{
    private const MODULE = 'YireoTraining_ExampleLokiAdminEavEntityType';

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = self::MODULE . '::grid';
        $this->uri = 'backend/loki_admin_eav_entity_type/index/grid';
    }

    public function testGridBlockDeclaresAclResources(): void
    {
        $this->dispatch($this->uri);
        $response = $this->getResponse();
        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertSame(200, $response->getHttpResponseCode());

        $block = $this->_objectManager->get(LayoutInterface::class)->getBlock('loki_admin_eav_entity_type.grid');
        $this->assertInstanceOf(AbstractBlock::class, $block);
        $this->assertSame(self::MODULE . '::grid', $block->getData('acl_resource'));
        $this->assertSame(self::MODULE . '::save', $block->getData('save_acl_resource'));
        $this->assertSame(self::MODULE . '::delete', $block->getData('delete_acl_resource'));
        $this->assertTrue((bool)$block->getData('hide_actions'));
    }
}
