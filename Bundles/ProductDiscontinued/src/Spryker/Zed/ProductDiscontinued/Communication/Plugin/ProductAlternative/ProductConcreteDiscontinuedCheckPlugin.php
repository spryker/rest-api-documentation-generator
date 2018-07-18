<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductDiscontinued\Communication\Plugin\ProductAlternative;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductAlternativeExtension\Dependency\Plugin\ProductConcreteDiscontinuedCheckPluginInterface;

/**
 * @method \Spryker\Zed\ProductDiscontinued\Business\ProductDiscontinuedBusinessFactory getFactory()
 * @method \Spryker\Zed\ProductDiscontinued\Business\ProductDiscontinuedFacadeInterface getFacade()
 */
class ProductConcreteDiscontinuedCheckPlugin extends AbstractPlugin implements ProductConcreteDiscontinuedCheckPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idProduct
     *
     * @return bool
     */
    public function checkConcreteProductDiscontinued(int $idProduct): bool
    {
        return $this->getFacade()->findProductDiscontinuedByProductId($idProduct)->getIsSuccessful();
    }
}
