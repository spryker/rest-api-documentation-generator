<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Offer\Business\Model\Hydrator;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OfferTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Offer\Dependency\Facade\OfferToCartFacadeInterface;
use Spryker\Zed\Offer\Dependency\Facade\OfferToMessengerFacadeInterface;
use Spryker\Zed\Offer\OfferConfig;


class OfferSavingAmountHydrator implements OfferSavingAmountHydratorInterface
{
    /**
     * @var \Spryker\Zed\Offer\Dependency\Facade\OfferToCartFacadeInterface
     */
    protected $cartFacade;

    /**
     * @var \Spryker\Zed\Offer\Dependency\Facade\OfferToMessengerFacadeInterface
     */
    protected $messengerFacade;

    /** @var  OfferConfig */
    protected $offerConfig;

    /**a
     *
     * @param \Spryker\Zed\Offer\Dependency\Facade\OfferToCartFacadeInterface $cartFacade
     * @param \Spryker\Zed\Offer\Dependency\Facade\OfferToMessengerFacadeInterface $messengerFacade
     */
    public function __construct(
        OfferToCartFacadeInterface $cartFacade,
        OfferToMessengerFacadeInterface $messengerFacade,
        OfferConfig $offerConfig
    ) {
        $this->cartFacade = $cartFacade;
        $this->messengerFacade = $messengerFacade;
        $this->offerConfig = $offerConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\OfferTransfer $offerTransfer
     *
     * @return \Generated\Shared\Transfer\OfferTransfer
     */
    public function hydrate(OfferTransfer $offerTransfer): OfferTransfer
    {
        $quoteTransfer = $offerTransfer->getQuote();

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $saving = $this->getOriginUnitPrice($itemTransfer, $quoteTransfer->getPriceMode());
            $saving -= $this->getUnitPrice($itemTransfer, $quoteTransfer->getPriceMode());
            $saving *= $itemTransfer->getQuantity();

            $itemTransfer->setSaving($saving);
        }

        $this->messengerFacade->getStoredMessages();

        return $offerTransfer;
    }

    /**
     * @param ItemTransfer $itemTransfer
     * @param string $priceMode
     *
     * @return int
     */
    protected function getOriginUnitPrice(ItemTransfer $itemTransfer, $priceMode)
    {
        if ($priceMode === $this->offerConfig->getPriceModeGross()) {
            return $itemTransfer->getOriginUnitGrossPrice();
        }

        return $itemTransfer->getOriginUnitNetPrice();
    }

    /**
     * @param ItemTransfer $itemTransfer
     * @param string $priceMode
     *
     * @return int
     */
    protected function getUnitPrice(ItemTransfer $itemTransfer, $priceMode)
    {
        if ($priceMode === $this->offerConfig->getPriceModeGross()) {
            return $itemTransfer->getUnitGrossPrice();
        }

        return $itemTransfer->getUnitNetPrice();
    }
}
