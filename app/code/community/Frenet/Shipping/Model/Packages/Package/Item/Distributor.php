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
use Mage_Shipping_Model_Rate_Request as RateRequest;
use Mage_Sales_Model_Quote_Item as QuoteItem;

/**
 * Class Frenet_Shipping_Model_Packages_Package_Item_Distributor
 */
class Frenet_Shipping_Model_Packages_Package_Item_Distributor
{
    use Frenet_Shipping_Helper_ObjectsTrait;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Validator
     */
    private $quoteItemValidator;

    /**
     * @var Frenet_Shipping_Model_Quote_Item_Quantity_CalculatorInterface
     */
    private $itemQuantityCalculator;

    public function __construct() {
        $this->quoteItemValidator = $this->objects()->quoteItemValidator();
        $this->itemQuantityCalculator = $this->objects()->quoteItemQtyCalculator();
    }

    /**
     * @param RateRequest $rateRequest
     *
     * @return array
     */
    public function distribute(RateRequest $rateRequest) : array
    {
        return (array) $this->getUnitItems($rateRequest);
    }

    /**
     * @param RateRequest $rateRequest
     *
     * @return array
     */
    private function getUnitItems(RateRequest $rateRequest) : array
    {
        $unitItems = [];

        /** @var QuoteItem $item */
        foreach ($rateRequest->getAllItems() as $item) {
            if (!$this->quoteItemValidator->validate($item)) {
                continue;
            }

            $qty = $this->itemQuantityCalculator->calculate($item);

            for ($i = 1; $i <= $qty; $i++) {
                $unitItems[] = $item;
            }
        }

        return $unitItems;
    }
}
