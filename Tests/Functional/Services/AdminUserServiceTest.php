<?php

declare(strict_types=1);

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
