<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShoppingListNote\Communication\Plugin;

use Generated\Shared\Transfer\ShoppingListItemTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ShoppingListExtension\Dependency\Plugin\ItemExpanderPluginInterface;

/**
 * @method \Spryker\Zed\ShoppingListNote\Business\ShoppingListNoteFacade getFacade()
 */
class ShoppingListItemExpanderPlugin extends AbstractPlugin implements ItemExpanderPluginInterface
{
    /**
     * @inheritdoc
     * Specification:
     * - This plugin extend shopping item transfer with shopping item note transfer.
     *
     * @param \Generated\Shared\Transfer\ShoppingListItemTransfer $shoppingListItemTransfer
     *
     * @return \Generated\Shared\Transfer\ShoppingListItemTransfer
     */
    public function expandItem(ShoppingListItemTransfer $shoppingListItemTransfer): ShoppingListItemTransfer
    {
        $expandedShoppingListItemTransfer = $this->getFacade()->expandItem($shoppingListItemTransfer);

        return $expandedShoppingListItemTransfer;
    }
}
