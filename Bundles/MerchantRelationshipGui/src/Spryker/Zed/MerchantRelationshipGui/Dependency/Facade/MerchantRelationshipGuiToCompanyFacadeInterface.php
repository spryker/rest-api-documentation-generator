<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantRelationshipGui\Dependency\Facade;

use Generated\Shared\Transfer\CompanyCollectionTransfer;

interface MerchantRelationshipGuiToCompanyFacadeInterface
{
    /**
     * @return \Generated\Shared\Transfer\CompanyCollectionTransfer
     */
    public function getCompanies(): CompanyCollectionTransfer;
}
