<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantFacadeBridge;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantSalesOrderFacadeBridge;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToStateMachineFacadeBridge;

/**
 * @method \Spryker\Zed\MerchantOms\MerchantOmsConfig getConfig()
 */
class MerchantOmsDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_STATE_MACHINE = 'FACADE_STATE_MACHINE';
    public const FACADE_MERCHANT_SALES_ORDER = 'FACADE_MERCHANT_SALES_ORDER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addMerchantFacade($container);
        $container = $this->addStateMachineFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addMerchantSalesOrderFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStateMachineFacade(Container $container): Container
    {
        $container->set(static::FACADE_STATE_MACHINE, function (Container $container) {
            return new MerchantOmsToStateMachineFacadeBridge($container->getLocator()->stateMachine()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantFacade(Container $container): Container
    {
        $container->set(static::FACADE_MERCHANT, function (Container $container) {
            return new MerchantOmsToMerchantFacadeBridge($container->getLocator()->merchant()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantSalesOrderFacade(Container $container): Container
    {
        $container->set(static::FACADE_MERCHANT_SALES_ORDER, function (Container $container) {
            return new MerchantOmsToMerchantSalesOrderFacadeBridge($container->getLocator()->merchantSalesOrder()->facade());
        });

        return $container;
    }
}
