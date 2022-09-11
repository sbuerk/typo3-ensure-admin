<?php

declare(strict_types=1);

/*
 * This file is part of the `sbuerk/typo3-ensure-admin` extension.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace SBUERK\EnsureAdmin\Tests\Functional\Services;

use SBUERK\EnsureAdmin\Services\AdminPasswordService;
use SBUERK\EnsureAdmin\Services\Exceptions\CanNotUpdateAdminUserWithoutForcingException;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AdminPasswordServiceTest extends FunctionalTestCase
{
    /**
     * @var non-empty-string[] Have styleguide loaded
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/cli_ensure_admin',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/be_users.csv');
    }

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        /** @var AdminPasswordService $adminPasswordService */
        $adminPasswordService = $this->getContainer()->get(AdminPasswordService::class);
        self::assertInstanceOf(AdminPasswordService::class, $adminPasswordService); // @phpstan-ignore-line
    }

    /**
     * @test
     */
    public function passwordGetsHashed(): void
    {
        $plainPassword = 'some-plain-password';
        $validPasswordHash = '$argon2i$v=19$m=65536,t=16,p=1$Um9jLjJPdnAwVFFua2RINQ$WUw1QbLWxi5cXm6ylZrM+RH/Gz0BFoZEASKqf0Cz604';

        /** @var AdminPasswordService $adminPasswordService */
        $adminPasswordService = $this->getContainer()->get(AdminPasswordService::class);
        $hashedPassword = $adminPasswordService->getHashedPassword($plainPassword);

        self::assertTrue($adminPasswordService->checkPassword($plainPassword, $hashedPassword), 'Hashed password valid');
        self::assertTrue($adminPasswordService->checkPassword($plainPassword, $validPasswordHash), 'Previous password hash valid');
    }

    public function adminExistsReturnsExpectedResultDataProvider(): \Generator
    {
        yield 'Normal admin user' => [
            'username' => 'admin',
            'expectedResult' => true,
        ];

        yield 'Disabled admin user' => [
            'username' => 'test_disabled',
            'expectedResult' => true,
        ];

        yield 'Deleted admin user' => [
            'username' => 'test_deleted',
            'expectedResult' => false,
        ];
    }

    /**
     * @test
     * @dataProvider adminExistsReturnsExpectedResultDataProvider
     */
    public function adminExistsReturnsExpectedResult(string $username, bool $expectedResult): void
    {
        /** @var AdminPasswordService $adminPasswordService */
        $adminPasswordService = $this->getContainer()->get(AdminPasswordService::class);
        $exists = $adminPasswordService->adminExists($username);

        self::assertSame($expectedResult, $exists);
    }

    public function getExistingAdminUserIdReturnsValidIdDataProvider(): \Generator
    {
        yield 'admin' => [
            'username' => 'admin',
            'expectedAdminUserId' => 1,
        ];

        yield 'test_deleted' => [
            'username' => 'test_disabled',
            'expectedAdminUserId' => 2,
        ];

        yield 'test_disabled' => [
            'username' => 'test_deleted',
            'expectedAdminUserId' => 0,
        ];
    }

    /**
     * @test
     * @dataProvider getExistingAdminUserIdReturnsValidIdDataProvider
     */
    public function getExistingAdminUserIdReturnsValidId(string $username, int $expectedAdminUserId): void
    {
        /** @var AdminPasswordService $adminPasswordService */
        $adminPasswordService = $this->getContainer()->get(AdminPasswordService::class);
        $adminUserId = $this->getMethod(AdminPasswordService::class, 'getExistingAdminUserId')->invokeArgs($adminPasswordService, [$username]);

        self::assertSame($expectedAdminUserId, $adminUserId);
    }

    /**
     * @test
     */
    public function ensureAdminUserThrowsExceptionIfExistingUserAndNotForced(): void
    {
        /** @var AdminPasswordService $adminPasswordService */
        $adminPasswordService = $this->getContainer()->get(AdminPasswordService::class);
        $hashedPassword = '$argon2i$v=19$m=65536,t=16,p=1$Um9jLjJPdnAwVFFua2RINQ$WUw1QbLWxi5cXm6ylZrM+RH/Gz0BFoZEASKqf0Cz604';

        $this->expectException(CanNotUpdateAdminUserWithoutForcingException::class);
        $this->expectExceptionCode(1662904770);
        $this->expectExceptionMessageMatches('/\[1\]"admin"/');

        $adminPasswordService->ensureAdminUser(false, 'admin', $hashedPassword, 'admin@example.org', 'Max', 'Mustermann');
    }

    public function ensureAdminUserWorksAsExpectedDataProvider(): \Generator
    {
        $hashedPassword = '$argon2i$v=19$m=65536,t=16,p=1$Um9jLjJPdnAwVFFua2RINQ$WUw1QbLWxi5cXm6ylZrM+RH/Gz0BFoZEASKqf0Cz604';
        yield 'admin updated' => [
            'force' => true,
            'username' => 'admin',
            'password' => $hashedPassword,
            'email' => 'admin@example.org',
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'expectedResult' => true,
            'expectedDataSet' => __DIR__ . '/Fixtures/DataSets/ensureAdminUser_forced_admin-updated.csv',
        ];

        yield 'test_disabled updated' => [
            'force' => true,
            'username' => 'test_disabled',
            'password' => $hashedPassword,
            'email' => 'admin@example.org',
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'expectedResult' => true,
            'expectedDataSet' => __DIR__ . '/Fixtures/DataSets/ensureAdminUser_forced_test_disabled-updated.csv',
        ];

        yield 'test_deleted new user' => [
            'force' => false,
            'username' => 'test_deleted',
            'password' => $hashedPassword,
            'email' => 'admin@example.org',
            'firstname' => 'Max',
            'lastname' => 'Mustermann',
            'expectedResult' => true,
            'expectedDataSet' => __DIR__ . '/Fixtures/DataSets/ensureAdminUser_forced_test_deleted-created.csv',
        ];
    }

    /**
     * @test
     * @dataProvider ensureAdminUserWorksAsExpectedDataProvider
     */
    public function ensureAdminUserWorksAsExpected(bool $force, string $username, string $password, string $email, string $firstname, string $lastname, bool $expectedResult, string $expectedDataSetFile): void
    {
        /** @var AdminPasswordService $adminPasswordService */
        $adminPasswordService = $this->getContainer()->get(AdminPasswordService::class);
        $result = $adminPasswordService->ensureAdminUser($force, $username, $password, $email, $firstname, $lastname);

        self::assertSame($expectedResult, $result);
        $this->assertCSVDataSet($expectedDataSetFile);
    }

    /**
     * @param class-string $className
     * @param string $methodName
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    protected function getMethod(string $className, string $methodName): \ReflectionMethod
    {
        $reflectionClassInstance = new \ReflectionClass($className);
        $reflectionMethod = $reflectionClassInstance->getMethod($methodName);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod;
    }
}
