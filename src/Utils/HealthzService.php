<?php
declare(strict_types = 1);
/**
 * /src/Utils/HealthzService.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */

namespace App\Utils;

use App\Entity\Healthz;
use App\Repository\HealthzRepository;

/**
 * Class HealthzService
 *
 * @package App\Utils
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
final class HealthzService
{
    /**
     * @var HealthzRepository
     */
    private $repository;

    /**
     * HealthzService constructor.
     *
     * @param HealthzRepository $repository
     */
    public function __construct(HealthzRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Healthz|null
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function check(): ?Healthz
    {
        $this->repository->cleanup();
        $this->repository->create();

        return $this->repository->read();
    }
}
