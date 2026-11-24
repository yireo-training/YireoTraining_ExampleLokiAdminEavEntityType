<?php declare(strict_types=1);

namespace YireoTraining\ExampleLokiAdminEavEntityType\Test\Unit\Component\Grid;

use Loki\AdminComponents\Acl\ActionAuthorization;
use Loki\AdminComponents\Grid\Action\ActionInterface;
use Loki\AdminComponents\Grid\Action\ActionListing;
use Loki\AdminComponents\Grid\StateManager;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerListing;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;
use YireoTraining\ExampleLokiAdminEavEntityType\Component\Grid\ReadOnlyGridRepository;

class ReadOnlyGridRepositoryTest extends TestCase
{
    public function testInlineEditIsRemovedFromTheUpdate(): void
    {
        $action = $this->createMock(ActionInterface::class);
        $action->expects($this->exactly(2))->method('execute')
            ->with($this->anything(), ['sort' => ['sortBy' => 'entity_type_code', 'sortDirection' => 'ASC']]);

        $this->getRepository(['inline_edit' => $action, 'sort' => $action])->saveValue([
            'sort' => ['sortBy' => 'entity_type_code', 'sortDirection' => 'ASC'],
            'inline_edit' => ['identifier' => 1, 'property' => 'entity_model', 'value' => 'Foo'],
        ]);
    }

    private function getRepository(array $actions): ReadOnlyGridRepository
    {
        $authorization = $this->createStub(AuthorizationInterface::class);
        $authorization->method('isAllowed')->willReturn(true);

        $repository = new ReadOnlyGridRepository(
            $this->createStub(StateManager::class),
            $this->createStub(ProviderHandlerListing::class),
            $this->createStub(ObjectManagerInterface::class),
            new ActionListing($actions),
            new ActionAuthorization($authorization),
            $this->createStub(MessageManagerInterface::class)
        );

        $block = $this->createPartialMock(Template::class, []);
        $viewModel = $this->createStub(ComponentViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn($block);

        $component = $this->createStub(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);
        $repository->setComponent($component);

        return $repository;
    }
}
