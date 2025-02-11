<?php
declare(strict_types = 1);
/**
 * /tests/Integration/Rest/ControllerCollectionTest.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */

namespace App\Tests\Integration\Rest;

use App\Controller\ApiKeyController;
use App\Controller\AuthController;
use App\Controller\RoleController;
use App\Controller\UserController;
use App\Controller\UserGroupController;
use App\Rest\ControllerCollection;
use Generator;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PropertyAccess\Tests\Fixtures\TraversableArrayObject;

/**
 * Class ControllerCollectionTest
 *
 * @package App\Tests\Integration\Rest
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class ControllerCollectionTest extends KernelTestCase
{
    public function testThatGetMethodThrowsAnException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('REST controller \'FooBar\' does not exists');

        $collection = new ControllerCollection(new TraversableArrayObject());
        $collection->get('FooBar');

        unset($collection);
    }

    public function testThatGetAllReturnsCorrectCountOfRestControllers(): void
    {
        $collection = $this->getCollection();

        static::assertCount(11, $collection->getAll());
    }

    /**
     * @dataProvider dataProviderTestThatGetReturnsExpectedController
     *
     * @param string $controllerName
     */
    public function testThatGetReturnsExpectedController(string $controllerName): void
    {
        $collection = $this->getCollection();

        /** @noinspection UnnecessaryAssertionInspection */
        static::assertInstanceOf($controllerName, $collection->get($controllerName));
    }

    /**
     * @dataProvider dataProviderTestThatHasReturnsExpected
     *
     * @param bool        $expected
     * @param string|null $resource
     */
    public function testThatHasReturnsExpected(bool $expected, ?string $resource): void
    {
        $collection = $this->getCollection();

        static::assertSame($expected, $collection->has($resource));
    }

    /**
     * @return Generator
     */
    public function dataProviderTestThatGetReturnsExpectedController(): Generator
    {
        yield [ApiKeyController::class];
        yield [RoleController::class];
        yield [UserController::class];
        yield [UserGroupController::class];
    }

    /**
     * @return Generator
     */
    public function dataProviderTestThatHasReturnsExpected(): Generator
    {
        yield [true, ApiKeyController::class];
        yield [true, RoleController::class];
        yield [true, UserController::class];
        yield [true, UserGroupController::class];
        yield [false, null];
        yield [false, 'foobar'];
        yield [false, AuthController::class];
    }

    /**
     * @return ControllerCollection
     */
    private function getCollection(): ControllerCollection
    {
        static::bootKernel();

        return static::$container->get(ControllerCollection::class);
    }
}
