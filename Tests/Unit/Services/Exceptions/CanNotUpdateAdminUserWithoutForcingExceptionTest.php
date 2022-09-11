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

namespace SBUERK\EnsureAdmin\Tests\Unit\Services\Exceptions;

use SBUERK\EnsureAdmin\Services\Exceptions\CanNotUpdateAdminUserWithoutForcingException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CanNotUpdateAdminUserWithoutForcingExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createReturnsExpectedExceptionClass(): void
    {
        $exception = CanNotUpdateAdminUserWithoutForcingException::create('admin', 1);
        self::assertSame(1662904770, $exception->getCode());
    }

    public function createReturnsExpectedExceptionMessageDataProvider(): \Generator
    {
        yield 'admin[1]' => [
            'username' => 'admin',
            'uid' => 1,
            'expectedMessage' => 'Updating existing admin user [1]"admin" not allowed. Use --force to update existing user.',
        ];

        yield 'some_username[123]' => [
            'username' => 'some_username',
            'uid' => 123,
            'expectedMessage' => 'Updating existing admin user [123]"some_username" not allowed. Use --force to update existing user.',
        ];
    }

    /**
     * @test
     * @dataProvider createReturnsExpectedExceptionMessageDataProvider
     */
    public function createReturnsExpectedExceptionMessage(string $username, int $uid, string $expectedMessage): void
    {
        $exception = CanNotUpdateAdminUserWithoutForcingException::create($username, $uid);
        self::assertSame($expectedMessage, $exception->getMessage());
        self::assertSame(1662904770, $exception->getCode());
    }
}
