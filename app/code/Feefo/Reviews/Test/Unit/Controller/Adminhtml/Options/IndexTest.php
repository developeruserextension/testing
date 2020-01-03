<?php

namespace Feefo\Reviews\Test\Unit\Controller\Adminhtml\Options;

use Feefo\Reviews\Controller\Adminhtml\Options\Index as IndexAction;
use Feefo\Reviews\Model\Feefo\Storage;
use Feefo\Reviews\Test\Unit\AbstractTestCase;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\App\View;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\View\Page\Config as ViewPageConfig;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\Page;

/**
 * Class IndexTest
 */
class IndexTest extends AbstractTestCase
{
    /**
     * @var IndexAction
     */
    protected $controller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestInterface
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Storage
     */
    protected $storageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RedirectFactory
     */
    protected $resultRedirectFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Redirect
     */
    protected $redirectResult;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ViewInterface
     */
    protected $viewMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DecoderInterface
     */
    protected $urlDecoderMock;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $context = $this->createActionContext();
        $this->storageMock = $this->createStorageMock();
        $this->requestMock = $context->getRequest();
        $this->redirectResult = $this->createRedirectResult();
        $this->resultRedirectFactoryMock = $context->getResultRedirectFactory();
        $this->configureRedirectResult($this->redirectResult);
        $this->viewMock = $context->getView();
        $this->urlDecoderMock = $this->basicMock(DecoderInterface::class);

        $arguments = [
            'context' => $context,
            'storage' => $this->storageMock,
            'urlDecoder' => $this->urlDecoderMock,
        ];

        $this->controller = $this->objectManager->getObject(IndexAction::class, $arguments);
    }

    /**
     * @return void
     */
    public function testRedirect()
    {
        $this->configureWebsiteId('12');

        $this->redirectResult->expects($this->once())
            ->method('setPath');

        $this->controller->execute();
    }

    /**
     * @return void
     */
    public function testConfigPageRendering()
    {
        $this->redirectResult->expects($this->never())
            ->method('setPath');

        $this->viewMock->expects($this->once())
            ->method('loadLayout');

        $this->controller->execute();
    }

    /**
     * @return ActionContext
     */
    protected function createActionContext()
    {
        $arguments = $this->objectManager->getConstructArguments(ActionContext::class);
        $arguments['view'] = $this->createViewMock();
        return $this->objectManager->getObject(ActionContext::class, $arguments);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Storage
     */
    protected function createStorageMock()
    {
        return $this->basicMock(Storage::class);
    }

    /**
     * @param string $id
     * @return void
     */
    protected function configureWebsiteId($id)
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->willReturn($id);
        $this->urlDecoderMock->expects($this->once())->method('decode')->willReturn($id);
    }

    /**
     * @param Redirect $result
     */
    protected function configureRedirectResult($result)
    {
        $this->resultRedirectFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Redirect
     */
    protected function createRedirectResult()
    {
        return $this->basicMock(Redirect::class);
    }

    /**
     * @return View
     */
    protected function createViewMock()
    {
        $viewMock = $this->basicMock(View::class);
        $pageMock = $this->basicMock(Page::class);
        $configPageMock = $this->basicMock(ViewPageConfig::class);
        $titleMock = $this->basicMock(Title::class);

        $viewMock->expects($this->any())
            ->method('getPage')
            ->willReturn($pageMock);

        $pageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($configPageMock);

        $configPageMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($titleMock);

        return $viewMock;
    }
}