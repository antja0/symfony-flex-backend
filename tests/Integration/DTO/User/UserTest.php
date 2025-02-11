<?php
declare(strict_types = 1);
/**
 * /tests/Integration/DTO/User/UserTest.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */

namespace App\Tests\Integration\DTO\User;

use App\DTO\User\User as UserDto;
use App\Entity\EntityInterface;
use App\Entity\Role as RoleEntity;
use App\Entity\User as UserEntity;
use App\Entity\UserGroup as UserGroupEntity;
use App\Tests\Integration\DTO\DtoTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;
use function count;

/**
 * Class UserTest
 *
 * @package App\Tests\Integration\DTO
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class UserTest extends DtoTestCase
{
    protected $dtoClass = UserDto::class;

    public function testThatLoadMethodWorks(): void
    {
        // Create Role entity
        $roleEntity = new RoleEntity('test role');

        // Create UserGroup entity
        $userGroupEntity = new UserGroupEntity();
        $userGroupEntity->setName('test user group');
        $userGroupEntity->setRole($roleEntity);

        // Create User entity
        $userEntity = new UserEntity();
        $userEntity->setUsername('username');
        $userEntity->setFirstName('first name');
        $userEntity->setLastName('last name');
        $userEntity->setEmail('firstname.surname@test.com');
        $userEntity->addUserGroup($userGroupEntity);

        /** @var UserDto $dto */
        $dto = new $this->dtoClass();
        $dto->load($userEntity);

        static::assertSame('username', $dto->getUsername());
        static::assertSame('first name', $dto->getFirstName());
        static::assertSame('last name', $dto->getLastName());
        static::assertSame('firstname.surname@test.com', $dto->getEmail());
        static::assertSame([$userGroupEntity], $dto->getUserGroups());
    }

    /**
     * @throws Throwable
     */
    public function testThatUpdateMethodCallsExpectedEntityMethodIfPasswordIsVisited(): void
    {
        /** @var MockObject|EntityInterface $entity */
        $entity = $this->getMockBuilder(UserEntity::class)
            ->setMethods(['getId', 'setPlainPassword'])
            ->getMock();

        $entity
            ->expects(static::once())
            ->method('setPlainPassword')
            ->with('password');

        /** @var UserDto $dto */
        $dto = new $this->dtoClass();
        $dto->setPassword('password');
        $dto->update($entity);
    }

    /**
     * @throws Throwable
     */
    public function testThatUpdateMethodCallsExpectedEntityMethodsIfUserGroupsIsVisited(): void
    {
        $userGroups = [
            $this->getMockBuilder(UserGroupEntity::class)->getMock(),
            $this->getMockBuilder(UserGroupEntity::class)->getMock(),
        ];

        /** @var MockObject|UserEntity $entity */
        $entity = $this->getMockBuilder(UserEntity::class)
            ->setMethods(['getId', 'clearUserGroups', 'addUserGroup'])
            ->getMock();

        $entity
            ->expects(static::once())
            ->method('clearUserGroups');

        $entity
            ->expects(static::exactly(count($userGroups)))
            ->method('addUserGroup')
            ->willReturn($entity);

        /** @var UserDto $dto */
        $dto = new $this->dtoClass();
        $dto->setUserGroups($userGroups);
        $dto->update($entity);
    }
}
