<?php

namespace App\Tests\Controller;

use ApiTestCase\JsonApiTestCase;
use App\Entity\User;
use App\Tests\DataBasePrimer;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


abstract class BaseTestCase extends JsonApiTestCase
{
    protected UserPasswordHasher $passwordHasher;
    protected EntityManager $entityManager;
    private User $authenticatedUser;
    protected Loader $loader;
    protected ORMExecutor $executor;
    protected static Application $application;

    protected function setFixtureFromSourceName(array $sources): void
    {
        $loader = new Loader();
        $this->loader = $loader;
        foreach ($sources as $source) {
            $path = 'App\Tests\DataFixtures\ORM\\' . $source;
            if (method_exists($path, '__construct'))
                $this->loader->addFixture(new $path($this->passwordHasher));
            else
                $this->loader->addFixture(new $path());
        }
        $this->executor->execute($this->loader->getFixtures());
    }

    /**
     * @throws \Exception
     */
    protected static function runCommand(string $command, KernelInterface $kernel): int
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication($kernel)->run(new StringInput($command));
    }

    protected static function getApplication(KernelInterface $kernel): Application
    {
        self::$application = new Application($kernel);
        self::$application->setAutoExit(false);
        return self::$application;
    }

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        static::$kernel = static::createKernel(['environment' => 'test', true]);
        static::$kernel->boot();
        self::runCommand('doctrine:database:drop --env=test --force --if-exists', static::$kernel);
        self::runCommand('doctrine:database:create --env=test', static::$kernel);
        DataBasePrimer::prime(static::$kernel);
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->passwordHasher = static::$kernel->getContainer()
            ->get('security.user_password_hasher');
        $purger = new ORMPurger($this->entityManager);
        $this->executor = new ORMExecutor($this->entityManager, $purger);
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        static::$kernel->shutdown();
        $this->entityManager->close();
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function getToken(string $userRole): string
    {
        if ($userRole == User::ROLE_ADMIN) {
            $phoneNumber = '09225075485';
            $role = 'role_admin';
            $this->authenticatedUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['phoneNumber' => $phoneNumber]);
        } else if ($userRole == User::ROLE_HOST) {
            $phoneNumber = '09919979109';
            $role = 'role_host';
            $this->authenticatedUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['phoneNumber' => $phoneNumber]);
        } else if ($userRole == User::ROLE_EXPERIENCER) {
            $phoneNumber = '09136971826';
            $role = 'role_experiencer';
            $this->authenticatedUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['phoneNumber' => $phoneNumber]);
        } else {
            $phoneNumber = '09136971826';
            $this->authenticatedUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['phoneNumber' => $phoneNumber]);
        }
        $content = [
            'phoneNumber' => $phoneNumber,
            'password' => 'pass_1234',
            'role' => $role
        ];

        $this->client->request('POST', '/api/v1/auth/login', []
            , [], [
                'CONTENT_TYPE' => 'application/json',
                'Connection' => 'keep-alive'
            ], json_encode($content));
        $response = $this->client->getResponse();
        $response = json_decode($response->getContent());
        return $response->data->token;
    }

    public function getUser(): User
    {
        return $this->authenticatedUser;
    }

}