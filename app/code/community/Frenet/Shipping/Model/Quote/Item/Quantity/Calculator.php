<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */

use Mage_Sales_Model_Quote_Item as Item;
use Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface as ItemQuantityCalculatorInterface;

/**
 * Class Frenet_Shipping_Model_Quote_Item_Quantity_Calculator
 */
class Frenet_Shipping_Model_Quote_Item_Quantity_Calculator implements ItemQuantityCalculatorInterface
{
    /**
     * @param Item $item
     *
     * @return float
     */
    public function calculate(Item $item)
    {
        $type = $item->getProductType();

        if ($item->getParentItemId()) {
            $type = $item->getParentItem()->getProductType();
        }

        switch ($type) {
            case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
                $qty = $this->calculateBundleProduct($item);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                $qty = $this->calculateGroupedProduct($item);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                $qty = $this->calculateConfigurableProduct($item);
                break;

            case Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL:
            case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE:
            case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
            default:
                $qty = $this->calculateSimpleProduct($item);
        }

        return (float) max(1, $qty);
    }

    /**
     * @param Item $item
     *
     * @return float
     */
    private function calculateSimpleProduct(Item $item)
    {
        return (float) $item->getQty();
    }

    /**
     * @param Item $item
     *
     * @return float
     */
    private function calculateBundleProduct(Item $item)
    {
        $bundleQty = (float) $item->getParentItem()->getQty();
        return (float) $item->getQty() * $bundleQty;
    }

    /**
     * @param Item $item
     *
     * @return float
     */
    private function calculateGroupedProduct(Item $item)
    {
        return (float) $item->getQty();
    }

    /**
     * The right quantity for configurable products are on the parent item.
     *
     * @param Item $item
     *
     * @return float
     */
    private function calculateConfigurableProduct(Item $item)
    {
        return (float) $item->getParentItem()->getQty();
    }
}
