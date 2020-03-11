<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\MerchantOms\Business\EventTrigger\MerchantOmsEventTrigger;
use Spryker\Zed\MerchantOms\Business\EventTrigger\MerchantOmsEventTriggerInterface;
use Spryker\Zed\MerchantOms\Business\StateMachineItem\StateMachineItemReader;
use Spryker\Zed\MerchantOms\Business\StateMachineItem\StateMachineItemReaderInterface;
use Spryker\Zed\MerchantOms\Business\StateMachineProcess\StateMachineProcessReader;
use Spryker\Zed\MerchantOms\Business\StateMachineProcess\StateMachineProcessReaderInterface;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantFacadeInterface;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToStateMachineFacadeInterface;
use Spryker\Zed\MerchantOms\MerchantOmsDependencyProvider;

/**
 * @method \Spryker\Zed\MerchantOms\MerchantOmsConfig getConfig()
 * @method \Spryker\Zed\MerchantOms\Persistence\MerchantOmsRepositoryInterface getRepository()
 */
class MerchantOmsBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\MerchantOms\Business\StateMachineProcess\StateMachineProcessReaderInterface
     */
    public function createStateMachineProcessReader(): StateMachineProcessReaderInterface
    {
        return new StateMachineProcessReader(
            $this->getConfig(),
            $this->getMerchantFacade(),
            $this->getStateMachineFacade()
        );
    }

    /**
     * @return \Spryker\Zed\MerchantOms\Business\StateMachineItem\StateMachineItemReaderInterface
     */
    public function createStateMachineItemReader(): StateMachineItemReaderInterface
    {
        return new StateMachineItemReader($this->getRepository());
    }

    /**
     * @return \Spryker\Zed\MerchantOms\Business\EventTrigger\MerchantOmsEventTriggerInterface
     */
    public function createMerchantOmsEventTrigger(): MerchantOmsEventTriggerInterface
    {
        return new MerchantOmsEventTrigger($this->getStateMachineFacade(), $this->createStateMachineProcessReader());
    }

    /**
     * @return \Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToStateMachineFacadeInterface
     */
    public function getStateMachineFacade(): MerchantOmsToStateMachineFacadeInterface
    {
        return $this->getProvidedDependency(MerchantOmsDependencyProvider::FACADE_STATE_MACHINE);
    }

    /**
     * @return \Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantFacadeInterface
     */
    public function getMerchantFacade(): MerchantOmsToMerchantFacadeInterface
    {
        return $this->getProvidedDependency(MerchantOmsDependencyProvider::FACADE_MERCHANT);
    }
}
