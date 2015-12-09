<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\SprykerFeature\Zed\Newsletter\Business;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\NewsletterSubscriberTransfer;
use Generated\Shared\Transfer\NewsletterSubscriptionRequestTransfer;
use Generated\Shared\Transfer\NewsletterTypeTransfer;
use SprykerEngine\Zed\Kernel\Container;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerEngine\Zed\Kernel\Persistence\QueryContainerLocator;
use SprykerEngine\Zed\Propel\Communication\Plugin\Connection;
use SprykerFeature\Zed\Newsletter\Business\NewsletterFacade;
use SprykerFeature\Zed\Newsletter\NewsletterDependencyProvider;
use SprykerFeature\Zed\Newsletter\Persistence\NewsletterQueryContainer;
use Orm\Zed\Newsletter\Persistence\SpyNewsletterType;

/**
 * @group Zed
 * @group Business
 * @group Newsletter
 * @group NewsletterFacadeTest
 */
class NewsletterFacadeTest extends Test
{

    const TEST_TYPE1 = 'TEST_TYPE1';
    const TEST_TYPE2 = 'TEST_TYPE2';

    /**
     * @var NewsletterFacade
     */
    protected $newsletterFacade;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->setNewsletterFacade();
        $this->setTestNewsletterTypes();
    }

    /**
     * @return void
     */
    public function testSubscribeWithSingleOptInShouldSucceed()
    {
        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();
        $request->setNewsletterSubscriber($subscriber);

        $this->addTestType1ToSubscriptionRequest($request);
        $this->addTestType2ToSubscriptionRequest($request);

        $response = $this->newsletterFacade->subscribeWithSingleOptIn($request);

        foreach ($response->getSubscriptionResults() as $result) {
            $this->assertTrue($result->getIsSuccess(), $result->getErrorMessage());
        }
    }

    /**
     * @return void
     */
    public function testSubscribeForAlreadySubscribedTypeShouldFail()
    {
        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();
        $request->setNewsletterSubscriber($subscriber);

        $this->addTestType1ToSubscriptionRequest($request);

        $this->newsletterFacade->subscribeWithSingleOptIn($request);

        $response = $this->newsletterFacade->subscribeWithSingleOptIn($request);

        $this->assertFalse($response->getSubscriptionResults()[0]->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testSubscribeWithDoubleOptInShouldSucceed()
    {
        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();
        $request->setNewsletterSubscriber($subscriber);

        $this->addTestType1ToSubscriptionRequest($request);
        $this->addTestType2ToSubscriptionRequest($request);

        $response = $this->newsletterFacade->subscribeWithDoubleOptIn($request);

        foreach ($response->getSubscriptionResults() as $result) {
            $this->assertTrue($result->getIsSuccess(), $result->getErrorMessage());
        }
    }

    /**
     * @return void
     */
    public function testApproveDoubleOptInSubscriberShouldSucceed()
    {
        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();
        $request->setNewsletterSubscriber($subscriber);

        $this->addTestType1ToSubscriptionRequest($request);

        $this->newsletterFacade->subscribeWithDoubleOptIn($request);

        $response = $this->newsletterFacade->approveDoubleOptInSubscriber($subscriber);

        $this->assertTrue($response->getIsSuccess(), $response->getErrorMessage());
    }

    /**
     * @return void
     */
    public function testApproveNonExistentDoubleOptInSubscriberShouldFail()
    {
        $subscriber = $this->createSubscriber();

        $response = $this->newsletterFacade->approveDoubleOptInSubscriber($subscriber);

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testUnsubscribeFromTypesShouldSucceed()
    {
        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();
        $request->setNewsletterSubscriber($subscriber);

        $this->addTestType1ToSubscriptionRequest($request);
        $this->addTestType2ToSubscriptionRequest($request);

        $this->newsletterFacade->subscribeWithSingleOptIn($request);

        $response = $this->newsletterFacade->unsubscribe($request);

        foreach ($response->getSubscriptionResults() as $result) {
            $this->assertTrue($result->getIsSuccess(), $result->getErrorMessage());
        }
    }

    /**
     * @return void
     */
    public function testUnsubscribeFromNotSubscribedTypesShouldFail()
    {
        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();
        $request->setNewsletterSubscriber($subscriber);

        $this->addTestType1ToSubscriptionRequest($request);

        $this->newsletterFacade->subscribeWithSingleOptIn($request);

        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();

        $request->setNewsletterSubscriber($subscriber);
        $this->addTestType2ToSubscriptionRequest($request);

        $response = $this->newsletterFacade->unsubscribe($request);

        foreach ($response->getSubscriptionResults() as $result) {
            $this->assertFalse($result->getIsSuccess(), $result->getErrorMessage());
        }
    }

    /**
     * @return void
     */
    public function testCheckSubscriptionForSubscribedTypesShouldSucceed()
    {
        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();
        $request->setNewsletterSubscriber($subscriber);

        $this->addTestType1ToSubscriptionRequest($request);

        $this->newsletterFacade->subscribeWithSingleOptIn($request);

        $response = $this->newsletterFacade->checkSubscription($request);

        $result = $response->getSubscriptionResults()[0];
        $this->assertTrue($result->getIsSuccess(), $result->getErrorMessage());
    }

    /**
     * @return void
     */
    public function testCheckSubscriptionForNotSubscribedTypesShouldFail()
    {
        $request = new NewsletterSubscriptionRequestTransfer();
        $subscriber = $this->createSubscriber();
        $request->setNewsletterSubscriber($subscriber);

        $this->addTestType1ToSubscriptionRequest($request);

        $response = $this->newsletterFacade->checkSubscription($request);

        $result = $response->getSubscriptionResults()[0];
        $this->assertFalse($result->getIsSuccess());
    }

    /**
     * @return void
     */
    protected function setNewsletterFacade()
    {
        $locator = Locator::getInstance();
        $container = new Container();

        $container[QueryContainerLocator::PROPEL_CONNECTION] = (new Connection())->get();

        $dependencyProvider = new NewsletterDependencyProvider();
        $dependencyProvider->provideBusinessLayerDependencies($container);
        $dependencyProvider->provideCommunicationLayerDependencies($container);
        $dependencyProvider->providePersistenceLayerDependencies($container);

        $this->newsletterFacade = new NewsletterFacade();
        $queryContainer = new NewsletterQueryContainer();
        $queryContainer->setExternalDependencies($container);
        $this->newsletterFacade->setOwnQueryContainer($queryContainer);
        $this->newsletterFacade->setExternalDependencies($container);
    }

    /**
     * @return void
     */
    protected function setTestNewsletterTypes()
    {
        $typeEntity = new SpyNewsletterType();
        $typeEntity->setName(self::TEST_TYPE1);
        $typeEntity->save();

        $typeEntity = new SpyNewsletterType();
        $typeEntity->setName(self::TEST_TYPE2);
        $typeEntity->save();
    }

    /**
     * @return NewsletterSubscriberTransfer
     */
    protected function createSubscriber()
    {
        $subscriber = new NewsletterSubscriberTransfer();
        $subscriber->setEmail('example@spryker.com');
        $subscriber->setSubscriberKey('example@spryker.com');

        return $subscriber;
    }

    /**
     * @param NewsletterSubscriptionRequestTransfer $request
     *
     * @return void
     */
    protected function addTestType1ToSubscriptionRequest(NewsletterSubscriptionRequestTransfer $request)
    {
        $type1 = new NewsletterTypeTransfer();
        $type1->setName(self::TEST_TYPE1);

        $request->addSubscriptionType($type1);
    }

    /**
     * @param NewsletterSubscriptionRequestTransfer $request
     *
     * @return void
     */
    protected function addTestType2ToSubscriptionRequest(NewsletterSubscriptionRequestTransfer $request)
    {
        $type2 = new NewsletterTypeTransfer();
        $type2->setName(self::TEST_TYPE2);

        $request->addSubscriptionType($type2);
    }

}
