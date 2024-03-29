<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantOms\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer;
use Generated\Shared\Transfer\MerchantOrderItemCollectionTransfer;
use Generated\Shared\Transfer\MerchantOrderItemTransfer;
use Generated\Shared\Transfer\MerchantOrderTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StateMachineItemStateHistoryTransfer;
use Generated\Shared\Transfer\StateMachineItemStateTransfer;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Spryker\Zed\MerchantOms\Business\Exception\MerchantNotFoundException;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantFacadeBridge;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantFacadeInterface;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToStateMachineFacadeBridge;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToStateMachineFacadeInterface;
use Spryker\Zed\MerchantOms\MerchantOmsConfig;
use Spryker\Zed\MerchantOms\MerchantOmsDependencyProvider;
use Spryker\Zed\StateMachine\Business\StateMachineFacade;
use SprykerTest\Zed\MerchantOms\MerchantOmsBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantOms
 * @group Business
 * @group Facade
 * @group MerchantOmsFacadeTest
 *
 * Add your own group annotations below this line
 */
class MerchantOmsFacadeTest extends Unit
{
    /**
     * @var string
     */
    protected const TEST_STATE_MACHINE = 'Test01';

    /**
     * @var string
     */
    protected const TEST_STATE_MACHINE_EVENT = 'test';

    /**
     * @var string
     */
    protected const TEST_PROCESS_NAME = 'processName';

    /**
     * @var array
     */
    protected const TEST_STATE_NAMES = ['new', 'canceled'];

    /**
     * @var array
     */
    protected const TEST_MANUAL_EVENTS = ['ship', 'cancel'];

    /**
     * @var array
     */
    protected const TEST_MANUAL_EVENT_NAMES = [
        [
            'ship',
            'cancel by merchant',
        ],
        [
            'deliver',
        ],
    ];

    /**
     * @var int
     */
    protected const TEST_ID_STATE_MACHINE_PROCESS = 888;

    /**
     * @var \SprykerTest\Zed\MerchantOms\MerchantOmsBusinessTester
     */
    protected MerchantOmsBusinessTester $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $stateMachineFacadeMock = $this->createMock(StateMachineFacade::class);
        $stateMachineFacadeMock->method('triggerEventForItems')->willReturn(1);
        $stateMachineFacadeMock->method('getManualEventsForStateMachineItems')->willReturn(static::TEST_MANUAL_EVENT_NAMES);

        $this->tester->setDependency(
            MerchantOmsDependencyProvider::FACADE_STATE_MACHINE,
            new MerchantOmsToStateMachineFacadeBridge($stateMachineFacadeMock),
        );
    }

    /**
     * @return void
     */
    public function testGetStateMachineItemsByStateIdsReturnsCorrectData(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        $saveOrderTransfer = $this->tester->getSaveOrderTransfer($merchantTransfer, static::TEST_STATE_MACHINE);
        /** @var \Generated\Shared\Transfer\ItemTransfer $itemTransfer */
        $itemTransfer = $saveOrderTransfer->getOrderItems()->offsetGet(0);

        $merchantOrderTransfer = $this->tester->haveMerchantOrder([MerchantOrderTransfer::ID_ORDER => $saveOrderTransfer->getIdSalesOrder()]);

        $processEntity = $this->tester->haveStateMachineProcess();

        $stateEntity = $this->tester->haveStateMachineItemState([
            StateMachineItemStateTransfer::FK_STATE_MACHINE_PROCESS => $processEntity->getIdStateMachineProcess(),
        ]);

        $merchantOrderItemTransfer = $this->tester->haveMerchantOrderItem([
            MerchantOrderItemTransfer::FK_STATE_MACHINE_ITEM_STATE => $stateEntity->getIdStateMachineItemState(),
            MerchantOrderItemTransfer::ID_MERCHANT_ORDER => $merchantOrderTransfer->getIdMerchantOrder(),
            MerchantOrderItemTransfer::ID_ORDER_ITEM => $itemTransfer->getIdSalesOrderItem(),
        ]);

        // Act
        $stateMachineItemTransfers = $this->tester->getFacade()->getStateMachineItemsByStateIds([$stateEntity->getIdStateMachineItemState()]);
        $stateMachineItemTransfer = $stateMachineItemTransfers[0] ?? null;

        // Assert
        $this->assertCount(1, $stateMachineItemTransfers);
        $this->assertInstanceOf(StateMachineItemTransfer::class, $stateMachineItemTransfer);
        $this->assertSame((int)$stateMachineItemTransfer->getIdItemState(), (int)$stateEntity->getIdStateMachineItemState());
        $this->assertSame((int)$stateMachineItemTransfer->getIdentifier(), (int)$merchantOrderItemTransfer->getIdMerchantOrderItem());
    }

    /**
     * @return void
     */
    public function testExpandMerchantOrderWithMerchantOmsDataReturnsCorrectData(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        $saveOrderTransfer = $this->tester->getSaveOrderTransfer($merchantTransfer, static::TEST_STATE_MACHINE);
        /** @var \Generated\Shared\Transfer\ItemTransfer $itemTransfer */
        $itemTransfer = $saveOrderTransfer->getOrderItems()->offsetGet(0);

        $expectedMerchantOrderTransfer = $this->tester->haveMerchantOrder([MerchantOrderTransfer::ID_ORDER => $saveOrderTransfer->getIdSalesOrder()]);

        $stateMachineProcessEntity = $this->tester->haveStateMachineProcess();

        $stateMachineItemStateTransfer = $this->tester->haveStateMachineItemState([
            StateMachineItemStateTransfer::FK_STATE_MACHINE_PROCESS => $stateMachineProcessEntity->getIdStateMachineProcess(),
        ]);

        $expectedMerchantOrderTransfer->setItemStates([$stateMachineItemStateTransfer->getName()]);

        $merchantOrderItemTransfer = $this->tester->haveMerchantOrderItem([
            MerchantOrderItemTransfer::FK_STATE_MACHINE_ITEM_STATE => $stateMachineItemStateTransfer->getIdStateMachineItemState(),
            MerchantOrderItemTransfer::ID_MERCHANT_ORDER => $expectedMerchantOrderTransfer->getIdMerchantOrder(),
            MerchantOrderItemTransfer::ID_ORDER_ITEM => $itemTransfer->getIdSalesOrderItem(),
        ]);
        $expectedMerchantOrderTransfer->addMerchantOrderItem($merchantOrderItemTransfer);
        $expectedManualEvents = array_unique(array_merge([], ...static::TEST_MANUAL_EVENT_NAMES));

        // Act
        $merchantOrderTransfer = $this->tester->getFacade()->expandMerchantOrderWithMerchantOmsData($expectedMerchantOrderTransfer);

        // Assert
        $this->assertSame($expectedMerchantOrderTransfer->getItemStates(), $merchantOrderTransfer->getItemStates());
        $this->assertSame($merchantOrderTransfer->getManualEvents(), $expectedManualEvents);
    }

    /**
     * @return void
     */
    public function testTriggerEventForMerchantOrderItemReturnsSuccess(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        $saveOrderTransfer = $this->tester->getSaveOrderTransfer($merchantTransfer, static::TEST_STATE_MACHINE);
        /** @var \Generated\Shared\Transfer\ItemTransfer $itemTransfer */
        $itemTransfer = $saveOrderTransfer->getOrderItems()->offsetGet(0);

        $merchantOrderTransfer = $this->tester->haveMerchantOrder([MerchantOrderTransfer::ID_ORDER => $saveOrderTransfer->getIdSalesOrder()]);

        $processEntity = $this->tester->haveStateMachineProcess();

        $stateEntity = $this->tester->haveStateMachineItemState([
            StateMachineItemStateTransfer::FK_STATE_MACHINE_PROCESS => $processEntity->getIdStateMachineProcess(),
        ]);

        $merchantOrderItemTransfer = $this->tester->haveMerchantOrderItem([
            MerchantOrderItemTransfer::FK_STATE_MACHINE_ITEM_STATE => $stateEntity->getIdStateMachineItemState(),
            MerchantOrderItemTransfer::ID_MERCHANT_ORDER => $merchantOrderTransfer->getIdMerchantOrder(),
            MerchantOrderItemTransfer::ID_ORDER_ITEM => $itemTransfer->getIdSalesOrderItem(),
        ]);

        $merchantOmsTriggerRequestTransfer = (new MerchantOmsTriggerRequestTransfer())
            ->setMerchantOrderItemReference($merchantOrderItemTransfer->getMerchantOrderItemReference())
            ->setMerchantOmsEventName(static::TEST_STATE_MACHINE_EVENT);

        // Act
        $merchantOmsTriggerResponseTransfer = $this->tester->getFacade()->triggerEventForMerchantOrderItem($merchantOmsTriggerRequestTransfer);

        // Assert
        $this->assertTrue($merchantOmsTriggerResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testTriggerEventForMerchantOrderItemReturnsFalseWithInvalidItemReference(): void
    {
        // Arrange
        $merchantOmsTriggerRequestTransfer = (new MerchantOmsTriggerRequestTransfer())
            ->setMerchantOrderItemReference('invalid reference')
            ->setMerchantOmsEventName(static::TEST_STATE_MACHINE_EVENT);

        // Act
        $merchantOmsTriggerResponseTransfer = $this->tester->getFacade()->triggerEventForMerchantOrderItem($merchantOmsTriggerRequestTransfer);

        // Assert
        $this->assertFalse($merchantOmsTriggerResponseTransfer->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testGetMerchantOmsProcessByMerchantThrowsExceptionIfMerchantNotFound(): void
    {
        // Arrange
        $this->setMerchantFacadeMockDependency(null);

        // Assert
        $this->expectException(MerchantNotFoundException::class);

        // Act
        $this->tester->getFacade()->getMerchantOmsProcessByMerchant(new MerchantCriteriaTransfer());
    }

    /**
     * @return void
     */
    public function testGetMerchantOmsProcessByMerchantReturnsStateMachineProcessWithStateNames(): void
    {
        // Arrange
        $stateMachineProcessTransfer = (new StateMachineProcessTransfer())
            ->setProcessName(static::TEST_PROCESS_NAME)
            ->setStateMachineName(static::TEST_STATE_MACHINE);
        $this->setStateMachineFacadeMockDependency(static::TEST_STATE_NAMES, $stateMachineProcessTransfer);

        $this->setMerchantFacadeMockDependency((new MerchantTransfer())
            ->setFkStateMachineProcess(static::TEST_ID_STATE_MACHINE_PROCESS));

        // Act
        $stateMachineProcessTransfer = $this->tester->getFacade()->getMerchantOmsProcessByMerchant(new MerchantCriteriaTransfer());

        // Assert
        $this->assertSame(static::TEST_STATE_NAMES, $stateMachineProcessTransfer->getStateNames());
        $this->assertSame(static::TEST_PROCESS_NAME, $stateMachineProcessTransfer->getProcessName());
        $this->assertSame(static::TEST_STATE_MACHINE, $stateMachineProcessTransfer->getStateMachineName());
    }

    /**
     * @return void
     */
    public function testGetMerchantOmsProcessByMerchantReturnsDefaultStateMachineProcessWithStateNames(): void
    {
        // Arrange
        $this->setMerchantFacadeMockDependency(new MerchantTransfer());
        $this->setStateMachineFacadeMockDependency(static::TEST_STATE_NAMES);

        // Act
        $stateMachineProcessTransfer = $this->tester->getFacade()->getMerchantOmsProcessByMerchant(new MerchantCriteriaTransfer());

        // Assert
        $this->assertSame(static::TEST_STATE_NAMES, $stateMachineProcessTransfer->getStateNames());
        $this->assertSame($this->tester->getModuleConfig()->getMerchantOmsDefaultProcessName(), $stateMachineProcessTransfer->getProcessName());
        $this->assertSame(MerchantOmsConfig::MERCHANT_OMS_STATE_MACHINE_NAME, $stateMachineProcessTransfer->getStateMachineName());
    }

    /**
     * @return void
     */
    public function testGetMerchantOmsProcessByMerchantReturnsDefaultStateMachineProcessWhenProcessIsNotFoundById(): void
    {
        // Arrange
        $this->setMerchantFacadeMockDependency((new MerchantTransfer())
            ->setFkStateMachineProcess(static::TEST_ID_STATE_MACHINE_PROCESS));
        $this->setStateMachineFacadeMockDependency(static::TEST_STATE_NAMES);

        // Act
        $stateMachineProcessTransfer = $this->tester->getFacade()
            ->getMerchantOmsProcessByMerchant(new MerchantCriteriaTransfer());

        // Assert
        $this->assertSame(static::TEST_STATE_NAMES, $stateMachineProcessTransfer->getStateNames());
        $this->assertSame($this->tester->getModuleConfig()->getMerchantOmsDefaultProcessName(), $stateMachineProcessTransfer->getProcessName());
        $this->assertSame(MerchantOmsConfig::MERCHANT_OMS_STATE_MACHINE_NAME, $stateMachineProcessTransfer->getStateMachineName());
    }

    /**
     * @param array<string> $stateNames
     * @param \Generated\Shared\Transfer\StateMachineProcessTransfer|null $stateMachineProcessTransfer
     * @param array<string> $manualEvents
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToStateMachineFacadeInterface
     */
    protected function setStateMachineFacadeMockDependency(
        array $stateNames = [],
        ?StateMachineProcessTransfer $stateMachineProcessTransfer = null,
        $manualEvents = []
    ): MerchantOmsToStateMachineFacadeInterface {
        $stateMachineFacadeMock = $this->getMockBuilder(MerchantOmsToStateMachineFacadeBridge::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stateMachineFacadeMock->method('findStateMachineProcess')->willReturn($stateMachineProcessTransfer);
        $stateMachineFacadeMock->method('getProcessStateNames')->willReturn($stateNames);
        $stateMachineFacadeMock->method('getManualEventsForStateMachineItem')->willReturn($manualEvents);

        $this->tester->setDependency(MerchantOmsDependencyProvider::FACADE_STATE_MACHINE, $stateMachineFacadeMock);

        return $stateMachineFacadeMock;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer|null $merchantTransfer
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantFacadeInterface
     */
    protected function setMerchantFacadeMockDependency(?MerchantTransfer $merchantTransfer): MerchantOmsToMerchantFacadeInterface
    {
        $merchantFacadeMock = $this->getMockBuilder(MerchantOmsToMerchantFacadeBridge::class)
            ->disableOriginalConstructor()
            ->getMock();
        $merchantFacadeMock->method('findOne')->willReturn($merchantTransfer);

        $this->tester->setDependency(MerchantOmsDependencyProvider::FACADE_MERCHANT, $merchantFacadeMock);

        return $merchantFacadeMock;
    }

    /**
     * @return void
     */
    public function testFindCurrentStateByIdSalesOrderItemReturnsStateForExistingOrderItem(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        $saveOrderTransfer = $this->tester->getSaveOrderTransfer($merchantTransfer, static::TEST_STATE_MACHINE);
        /** @var \Generated\Shared\Transfer\ItemTransfer $itemTransfer */
        $itemTransfer = $saveOrderTransfer->getOrderItems()->offsetGet(0);

        $merchantOrderTransfer = $this->tester->haveMerchantOrder([MerchantOrderTransfer::ID_ORDER => $saveOrderTransfer->getIdSalesOrder()]);

        $processEntity = $this->tester->haveStateMachineProcess();

        $stateEntity = $this->tester->haveStateMachineItemState([
            StateMachineItemStateTransfer::FK_STATE_MACHINE_PROCESS => $processEntity->getIdStateMachineProcess(),
        ]);

        $merchantOrderItemTransfer = $this->tester->haveMerchantOrderItem([
            MerchantOrderItemTransfer::FK_STATE_MACHINE_ITEM_STATE => $stateEntity->getIdStateMachineItemState(),
            MerchantOrderItemTransfer::ID_MERCHANT_ORDER => $merchantOrderTransfer->getIdMerchantOrder(),
            MerchantOrderItemTransfer::ID_ORDER_ITEM => $itemTransfer->getIdSalesOrderItem(),
        ]);

        // Act
        $stateMachineItemTransfer = $this->tester->getFacade()->findCurrentStateByIdSalesOrderItem($itemTransfer->getIdSalesOrderItem());

        // Assert
        $this->assertInstanceOf(StateMachineItemTransfer::class, $stateMachineItemTransfer);
        $this->assertSame($stateEntity->getName(), $stateMachineItemTransfer->getStateName());
    }

    /**
     * @return void
     */
    public function testFindCurrentStateByIdSalesOrderItemReturnsNullForNotExistingOrderItem(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        $saveOrderTransfer = $this->tester->getSaveOrderTransfer($merchantTransfer, static::TEST_STATE_MACHINE);
        /** @var \Generated\Shared\Transfer\ItemTransfer $itemTransfer */
        $itemTransfer = $saveOrderTransfer->getOrderItems()->offsetGet(0);

        $merchantOrderTransfer = $this->tester->haveMerchantOrder([MerchantOrderTransfer::ID_ORDER => $saveOrderTransfer->getIdSalesOrder()]);

        $processEntity = $this->tester->haveStateMachineProcess();

        $stateEntity = $this->tester->haveStateMachineItemState([
            StateMachineItemStateTransfer::FK_STATE_MACHINE_PROCESS => $processEntity->getIdStateMachineProcess(),
        ]);

        $this->tester->haveMerchantOrderItem([
            MerchantOrderItemTransfer::FK_STATE_MACHINE_ITEM_STATE => $stateEntity->getIdStateMachineItemState(),
            MerchantOrderItemTransfer::ID_MERCHANT_ORDER => $merchantOrderTransfer->getIdMerchantOrder(),
            MerchantOrderItemTransfer::ID_ORDER_ITEM => $itemTransfer->getIdSalesOrderItem(),
        ]);

        // Act
        $stateMachineItemTransfer = $this->tester->getFacade()->findCurrentStateByIdSalesOrderItem(999);

        // Assert
        $this->assertNull($stateMachineItemTransfer);
    }

    /**
     * @return void
     */
    public function testExpandMerchantOrderItemsWithManualEventsExpandsMerchantOrderItems(): void
    {
        // Arrange
        $this->setStateMachineFacadeMockDependency([], null, static::TEST_MANUAL_EVENTS);

        $merchantOrderTransfer = $this->tester->createMerchantOrderWithItems();
        $merchantOrderItemCollectionTransfer = (new MerchantOrderItemCollectionTransfer())
            ->setMerchantOrderItems($merchantOrderTransfer->getMerchantOrderItems());

        // Act
        $expandedMerchantOrderItemCollectionTransfer = $this->tester->getFacade()->expandMerchantOrderItemsWithManualEvents($merchantOrderItemCollectionTransfer);

        // Assert
        $this->assertSame(static::TEST_MANUAL_EVENTS, $expandedMerchantOrderItemCollectionTransfer->getMerchantOrderItems()[0]->getManualEvents());
    }

    /**
     * @return void
     */
    public function testGetMerchantOrderItemsStateHistory(): void
    {
        // Arrange
        $merchantOrderTransfer = $this->tester->createMerchantOrderWithItems();
        $merchantOrderItemTransfer = $merchantOrderTransfer
            ->getMerchantOrderItems()
            ->getIterator()
            ->current();

        $this->tester->haveStateMachineItemStateHistory([
            StateMachineItemStateHistoryTransfer::FK_STATE_MACHINE_ITEM_STATE => $merchantOrderItemTransfer->getFkStateMachineItemState(),
            StateMachineItemStateHistoryTransfer::IDENTIFIER => $merchantOrderItemTransfer->getIdMerchantOrderItem(),
        ]);

        $merchantOrderItemIds = array_map(
            function (MerchantOrderItemTransfer $merchantOrderItemTransfer) {
                return $merchantOrderItemTransfer->getIdMerchantOrderItem();
            },
            $merchantOrderTransfer->getMerchantOrderItems()->getArrayCopy(),
        );

        // Act
        $stateMachineItemTransfers = $this->tester->getFacade()->getMerchantOrderItemsStateHistory($merchantOrderItemIds);

        // Assert
        $this->assertIsArray($stateMachineItemTransfers);
        $this->assertCount(1, $stateMachineItemTransfers);
    }
}
