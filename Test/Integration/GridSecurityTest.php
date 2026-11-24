<?php declare(strict_types=1);

namespace YireoTraining\ExampleLokiAdminEavEntityType\Test\Integration;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Grid\Action\ActionListing;
use Loki\Components\Config\XmlConfig;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use YireoTraining\ExampleLokiAdminEavEntityType\Controller\Adminhtml\Index\Grid;

#[AppArea('adminhtml')]
class GridSecurityTest extends TestCase
{
    private const MODULE = 'YireoTraining_ExampleLokiAdminEavEntityType';
    private const COMPONENT = 'loki_admin_eav_entity_type.grid';

    public function testComponentUsesReadOnlyRepository(): void
    {
        $componentDefinitions = ObjectManager::getInstance()->get(XmlConfig::class)->getComponentDefinitions();
        $this->assertArrayHasKey(self::COMPONENT, $componentDefinitions);

        $repositoryClass = (string)$componentDefinitions[self::COMPONENT]->getRepositoryClass();
        $repository = ObjectManager::getInstance()->create($repositoryClass);
        $this->assertInstanceOf(GridRepository::class, $repository);

        $property = new ReflectionProperty(GridRepository::class, 'actionListing');
        $actionListing = $property->getValue($repository);
        $this->assertInstanceOf(ActionListing::class, $actionListing);

        $actions = array_keys($actionListing->getActions());
        $this->assertNotContains('inline_edit', $actions);
        $this->assertContains('sort', $actions);
        $this->assertContains('search', $actions);
    }

    public function testGridBlockDeclaresAclResources(): void
    {
        $layout = ObjectManager::getInstance()->create(LayoutInterface::class);
        $layout->getUpdate()->load(['default', 'loki_admin_eav_entity_type_index_grid']);
        $layout->generateXml();
        $layout->generateElements();

        $block = $layout->getBlock(self::COMPONENT);
        $this->assertNotFalse($block);
        $this->assertSame(self::MODULE . '::grid', $block->getData('acl_resource'));
        $this->assertSame(self::MODULE . '::save', $block->getData('save_acl_resource'));
        $this->assertSame(self::MODULE . '::delete', $block->getData('delete_acl_resource'));
        $this->assertTrue((bool)$block->getData('hide_actions'));
    }

    public function testAclResourcesAreNestedUnderContentElements(): void
    {
        $acl = ObjectManager::getInstance()->get(AclBuilder::class)->getAcl();

        foreach (['grid', 'save', 'delete'] as $resource) {
            $resourceId = self::MODULE . '::' . $resource;
            $this->assertTrue($acl->has($resourceId), $resourceId);
            $this->assertTrue($acl->inheritsResource($resourceId, 'Magento_Backend::content_elements'), $resourceId);
        }

        $this->assertTrue($acl->inheritsResource(self::MODULE . '::save', self::MODULE . '::grid', true));
        $this->assertFalse($acl->has(self::MODULE . '::form'));
        $this->assertFalse($acl->has(self::MODULE . '::index'));
    }

    public function testControllerOnlyAcceptsGetAndUsesGridResource(): void
    {
        $this->assertTrue(is_subclass_of(Grid::class, HttpGetActionInterface::class));
        $this->assertSame(self::MODULE . '::grid', Grid::ADMIN_RESOURCE);
    }
}
