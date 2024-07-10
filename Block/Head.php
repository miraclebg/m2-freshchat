<?php
/**
 * Copyright © Nimasystems (info@nimasystems.com). All rights reserved.
 * Please visit Nimasystems.com for license details
 */

declare(strict_types=1);

namespace Nimasystems\Freshchat\Block;

use Magento\Framework\View\Element\Template;
use Nimasystems\Freshchat\Helpers\Data;

class Head extends Template
{
    /**
     * @var Data
     */
    private Data $dataHelper;

    public function __construct(Template\Context $context,
                                Data             $dataHelper,
                                array            $data = [])
    {
        parent::__construct($context, $data);

        $this->dataHelper = $dataHelper;
    }

    public function getFreshchatConfig(): array
    {
        return [
            'generic' => [
                'freshchatUpdateUrl' => $this->dataHelper->getFreshchatUpdateUrl(),
            ],
            'freshchat' => [
                'enabled' => $this->dataHelper->getFreshchatEnabled(),
                'token' => $this->dataHelper->getFreshchatToken(),
                'host' => $this->dataHelper->getFreshchatHost(),
                'siteId' => $this->dataHelper->getFreshchatSiteId(),
            ],
        ];

    }
}
