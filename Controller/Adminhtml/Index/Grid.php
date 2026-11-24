<?php declare(strict_types=1);

namespace YireoTraining\ExampleLokiAdminEavEntityType\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class Grid extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'YireoTraining_ExampleLokiAdminEavEntityType::grid';

    public function __construct(
        Context $context,
        private readonly ResultPageFactory $resultPageFactory,
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $title = __('Loki Grid - EAV Entity Types');

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('YireoTraining_ExampleLokiAdminEavEntityType::index');
        $resultPage->addBreadcrumb($title, $title);
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
