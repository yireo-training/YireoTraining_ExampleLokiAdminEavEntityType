<?php declare(strict_types=1);

namespace YireoTraining\ExampleLokiAdminEavEntityType\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

/**
 * Class Index
 *
 * @package YireoTraining\ExampleLokiAdminEavEntityType\Controller\Index
 */
class Grid extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'YireoTraining_ExampleLokiAdminEavEntityType::grid';

    /**
     * @var ResultPageFactory
     */
    private $resultPageFactory;


    /**
     * @param Context $context
     * @param ResultPageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ResultPageFactory $resultPageFactory,
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return Page
     * @throws \Exception
     */
    public function execute(): Page
    {
        $title = 'Loki Grid - EAV Entity Types';

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('loki_admin_eav_entity_type_index_grid.xml');
        $resultPage->setActiveMenu('YireoTraining_ExampleLokiAdminEavEntityType::index');
        $resultPage->addBreadcrumb(__($title), __($title));
        $resultPage->getConfig()->getTitle()->prepend(__($title));

        return $resultPage;
    }
}
