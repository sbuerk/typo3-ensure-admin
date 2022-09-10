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
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AdminPasswordServiceTest extends FunctionalTestCase
{
    /**
     * @var non-empty-string[] Have styleguide loaded
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/sbuerk_ensureadmin',
    ];

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $adminPasswordService = $this->getContainer()->get(AdminPasswordService::class);
        self::assertInstanceOf(AdminPasswordService::class, $adminPasswordService);
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
}
