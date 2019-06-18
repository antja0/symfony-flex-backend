<?php
declare(strict_types = 1);
/**
 * /tests/Integration/Rest/Traits/Methods/IdsMethodTest.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */

namespace App\Tests\Integration\Rest\Traits\Methods;

use App\DTO\RestDtoInterface;
use App\Rest\ResponseHandlerInterface;
use App\Rest\RestResourceInterface;
use App\Tests\Integration\Rest\Traits\Methods\src\PatchMethodInvalidTestClass;
use App\Tests\Integration\Rest\Traits\Methods\src\PatchMethodTestClass;
use Exception;
use Generator;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class PatchMethodTest
 *
 * @package App\Tests\Integration\Rest\Traits\Methods
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class PatchMethodTest extends KernelTestCase
{
    /**
     * @throws Throwable
     */
    public function testThatTraitThrowsAnException():void
    {
        $this->expectException(LogicException::class);

        /** @codingStandardsIgnoreStart */
        $this->expectExceptionMessageRegExp(
            '/You cannot use (.*) controller class with REST traits if that does not implement (.*)ControllerInterface\'/'
        );
        /** @codingStandardsIgnoreEnd */

        /**
         * @var MockObject|PatchMethodInvalidTestClass $testClass
         * @var MockObject|RestDtoInterface            $restDtoInterface
         */
        $testClass = $this->getMockForAbstractClass(PatchMethodInvalidTestClass::class);
        $restDtoInterface = $this->getMockBuilder(RestDtoInterface::class)->getMock();

        $uuid = Uuid::uuid4()->toString();

        $request = Request::create('/' . $uuid, 'PATCH');

        $testClass->patchMethod($request, $restDtoInterface, 'some-id');
    }

    /**
     * @dataProvider dataProviderTestThatTraitThrowsAnExceptionWithWrongHttpMethod
     *
     * @param string $httpMethod
     *
     * @throws Throwable
     */
    public function testThatTraitThrowsAnExceptionWithWrongHttpMethod(string $httpMethod): void
    {
        $this->expectException(MethodNotAllowedHttpException::class);

        $resource = $this->createMock(RestResourceInterface::class);
        $responseHandler = $this->createMock(ResponseHandlerInterface::class);

        /**
         * @var MockObject|RestDtoInterface     $restDtoInterface
         * @var MockObject|PatchMethodTestClass $testClass
         */
        $restDtoInterface = $this->getMockBuilder(RestDtoInterface::class)->getMock();
        $testClass = $this->getMockForAbstractClass(
            PatchMethodTestClass::class,
            [$resource, $responseHandler]
        );

        $uuid = Uuid::uuid4()->toString();

        // Create request and response
        $request = Request::create('/' . $uuid, $httpMethod);

        $testClass->patchMethod($request, $restDtoInterface, 'some-id')->getContent();
    }

    /**
     * @throws Throwable
     */
    public function testThatTraitCallsServiceMethods(): void
    {
        $resource = $this->createMock(RestResourceInterface::class);
        $responseHandler = $this->createMock(ResponseHandlerInterface::class);

        /**
         * @var MockObject|RestDtoInterface     $restDtoInterface
         * @var MockObject|Request              $request
         * @var MockObject|PatchMethodTestClass $testClass
         */
        $restDtoInterface = $this->getMockBuilder(RestDtoInterface::class)->getMock();
        $request = $this->createMock(Request::class);
        $testClass = $this->getMockForAbstractClass(
            PatchMethodTestClass::class,
            [$resource, $responseHandler]
        );

        $uuid = Uuid::uuid4()->toString();

        $request
            ->expects(static::once())
            ->method('getMethod')
            ->willReturn('PATCH');

        $testClass->patchMethod($request, $restDtoInterface, $uuid);
    }

    /**
     * @return Generator
     */
    public function dataProviderTestThatTraitThrowsAnExceptionWithWrongHttpMethod(): Generator
    {
        yield ['HEAD'];
        yield ['DELETE'];
        yield ['GET'];
        yield ['POST'];
        yield ['PUT'];
        yield ['OPTIONS'];
        yield ['CONNECT'];
        yield ['foobar'];
    }

    /**
     * @return Generator
     */
    public function dataProviderTestThatTraitHandlesException(): Generator
    {
        yield [new HttpException(400), 0];
        yield [new NotFoundHttpException(), 0];
        yield [new Exception(), 400];
    }
}
