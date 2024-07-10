<?php
/**
 * Copyright © Nimasystems (info@nimasystems.com). All rights reserved.
 * Please visit Nimasystems.com for license details
 */

declare(strict_types=1);

namespace Nimasystems\Freshchat\Helpers;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const MODULE_NAME = 'Nimasystems_Freshchat';

    const XML_PATH_GENERIC = 'nimasystems_freshchat/generic/';

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

    public function getFreshdeskLivechatEnabled(): bool
    {
        return $this->getStoreConfig('freshdesk_enable_livechat') == '1';
    }

    public function getFreshdeskWidgetJsSrc(): ?string
    {
        return $this->getStoreConfig('freshdesk_widget_js_src');
    }
}
