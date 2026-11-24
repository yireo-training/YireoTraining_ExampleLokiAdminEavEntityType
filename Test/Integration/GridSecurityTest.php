<?php declare(strict_types=1);

namespace YireoTraining\ExampleLokiAdminEavEntityType\Test\Integration;

use Loki\Components\Config\XmlConfig;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use YireoTraining\ExampleLokiAdminEavEntityType\Component\Grid\ReadOnlyGridRepository;
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
        $this->assertInstanceOf(ReadOnlyGridRepository::class, $repository);
    }

    public function testAclResourcesAreNestedUnderContentElements(): void
    {
        $acl = ObjectManager::getInstance()->get(AclBuilder::class)->getAcl();

        foreach (['grid', 'save', 'delete'] as $resource) {
            $resourceId = self::MODULE . '::' . $resource;
            $this->assertTrue($acl->hasResource($resourceId), $resourceId);
            $this->assertTrue($acl->inheritsResource($resourceId, 'Magento_Backend::content_elements'), $resourceId);
        }

        $this->assertTrue($acl->inheritsResource(self::MODULE . '::save', self::MODULE . '::grid', true));
        $this->assertFalse($acl->hasResource(self::MODULE . '::form'));
        $this->assertFalse($acl->hasResource(self::MODULE . '::index'));
    }

    public function testControllerOnlyAcceptsGetAndUsesGridResource(): void
    {
        $this->assertTrue((new ReflectionClass(Grid::class))->implementsInterface(HttpGetActionInterface::class));
        $this->assertSame(self::MODULE . '::grid', Grid::ADMIN_RESOURCE);
    }
}
