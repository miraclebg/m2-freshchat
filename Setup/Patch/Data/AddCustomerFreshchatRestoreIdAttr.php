<?php
/**
 * Copyright © Nimasystems (info@nimasystems.com). All rights reserved.
 * Please visit Nimasystems.com for license details
 */

declare(strict_types=1);

namespace Nimasystems\Freshchat\Setup\Patch\Data;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddCustomerFreshchatRestoreIdAttr
 */
class AddCustomerFreshchatRestoreIdAttr implements DataPatchInterface
{
    public const FRESHCHAT_ATTRIBUTE_ID = 'freshchat_restore_id';

    /**
     * @var ModuleDataSetupInterface
     */
    protected ModuleDataSetupInterface $_moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    protected EavSetupFactory $_eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory
    )
    {
        $this->_moduleDataSetup = $moduleDataSetup;
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);

        $eavSetup->addAttribute(
            Customer::ENTITY,
            self::FRESHCHAT_ATTRIBUTE_ID,
            [
                'type' => 'varchar',
                'label' => 'Freshchat Restore ID',
                'input' => 'text',
                'required' => false,
                'visible' => false,
                'user_defined' => true,
                'position' => 999,
                'system' => 0,
            ]
        );

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            self::FRESHCHAT_ATTRIBUTE_ID
        );
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
