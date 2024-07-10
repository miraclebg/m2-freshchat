<?php
/**
 * Copyright © Nimasystems (info@nimasystems.com). All rights reserved.
 * Please visit Nimasystems.com for license details
 */

declare(strict_types=1);

namespace Nimasystems\Freshchat\Helpers;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Url;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const MODULE_NAME = 'Nimasystems_Freshchat';

    const XML_PATH_GENERIC = 'nimasystems_freshchat/generic/';
    const XML_PATH_FRESHCHAT = 'nimasystems_freshchat/freshchat/';

    /**
     * @var Url
     */
    protected Url $urlHelper;

    /**
     * @param Context $context
     * @param Url $urlHelper
     */
    public function __construct(Context $context,
                                Url     $urlHelper)
    {
        parent::__construct($context);

        $this->urlHelper = $urlHelper;
    }

    /**
     * @param string $code
     * @param integer|null $storeId
     * @param string $path
     * @return mixed
     */
    public function getStoreConfig(string $code, int $storeId = null, string $path = self::XML_PATH_GENERIC)
    {
        return $this->scopeConfig->getValue(
            $path . $code, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getFreshchatEnabled(): bool
    {
        return $this->getStoreConfig('enabled', null, self::XML_PATH_FRESHCHAT) == '1';
    }

    public function getFreshchatToken(): ?string
    {
        return $this->getStoreConfig('token', null, self::XML_PATH_FRESHCHAT);
    }

    public function getFreshchatHost(): ?string
    {
        return $this->getStoreConfig('host', null, self::XML_PATH_FRESHCHAT);
    }

    public function getFreshchatSiteId(): ?string
    {
        return $this->getStoreConfig('site_id', null, self::XML_PATH_FRESHCHAT);
    }

    /**
     * @return string
     */
    public function getFreshchatUpdateUrl(): string
    {
        return $this->urlHelper->getUrl('nimasystems_freshchat/freshchat/update', ['_secure' => true]);
    }
}
