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

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AdminUserServiceTest extends FunctionalTestCase
{
    /**
     * @var non-empty-string[] Have styleguide loaded
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/sbuerk_ensureadmin',
    ];

    /**
     * Just a dummy to show that at least one test is actually executed on mssql
     *
     * @test
     */
    public function dummy(): void
    {
        self::assertTrue(true);
    }
}
