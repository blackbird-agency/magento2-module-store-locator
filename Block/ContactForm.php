<?php

namespace Smile\StoreLocator\Block;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Smile\StoreLocator\Helper\Data as StoreLocatorHelper;

/**
 * Store Locator Contact Form.
 */
class ContactForm extends AbstractView
{
    private ?array $postData = null;

    private DataPersistorInterface $dataPersistor;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        private StoreLocatorHelper $storeLocatorHelper,
        DataPersistorInterface $dataPersistorInterface,
        array $data
    ) {
        $this->dataPersistor = $dataPersistorInterface;
        parent::__construct($context, $coreRegistry, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Return form action url.
     */
    public function getFormAction(): string
    {
        return $this->getUrl('storelocator/store/contactPost', ['_secure' => true]);
    }

    /**
     * Get value from POST by key.
     */
    public function getPostValue(string $key): string
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->dataPersistor->get('contact_store');
            $this->dataPersistor->clear('contact_store');
        }

        if (isset($this->postData[$key])) {
            return (string) $this->postData[$key];
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setPageTitle()
            ->setBreadcrumbs();

        return $this;
    }

    /**
     * Set the current page title.
     */
    private function setPageTitle(): self
    {
        $retailer = $this->getRetailer();

        $titleBlock = $this->getLayout()->getBlock('page.main.title');

        if ($titleBlock) {
            $titleBlock->setPageTitle(__("Contact %1", $retailer->getName()));
        }

        $this->pageConfig->getTitle()->set(__("Contact %1", $retailer->getName()));

        return $this;
    }

    /**
     * Build breadcrumbs for the current page.
     */
    private function setBreadcrumbs(): self
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbsBlock) {
            $retailer = $this->getRetailer();
            $homeUrl = $this->_storeManager->getStore()->getBaseUrl();
            $storeLocatorHomeUrl = $this->storeLocatorHelper->getHomeUrl();
            $storeUrl = $this->storeLocatorHelper->getRetailerUrl($retailer);

            $breadcrumbsBlock->addCrumb(
                'home',
                ['label' => __('Home'), 'title' => __('Go to Home Page'), 'link' => $homeUrl]
            );

            $breadcrumbsBlock->addCrumb(
                'search',
                ['label' => __('Our stores'), 'title' => __('Our stores'), 'link' => $storeLocatorHomeUrl]
            );

            $breadcrumbsBlock->addCrumb(
                'store',
                ['label' => $retailer->getName(), 'title' => $retailer->getName(), 'link' => $storeUrl]
            );

            $breadcrumbsBlock->addCrumb(
                'contact',
                ['label' => __('Contact'), 'title' => __('Contact')]
            );
        }

        return $this;
    }
}
