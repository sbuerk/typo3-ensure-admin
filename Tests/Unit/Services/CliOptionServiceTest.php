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

namespace SBUERK\CliEnsureAdmin\Tests\Unit\Services;

use SBUERK\CliEnsureAdmin\Services\CliOptionService;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CliOptionServiceTest extends UnitTestCase
{
    /**
     * @var CliOptionService
     */
    protected $cliOptionService;

    /**
     * @var array<string, mixed>
     */
    protected $originalEnvVars = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->cliOptionService = new CliOptionService();
    }

    protected function tearDown(): void
    {
        $this->restoreEnvVars();
        parent::tearDown();
    }

    public function returnsExpectedValueDataProvider(): \Generator
    {
        yield 'only env variable set returns env value' => [
            'envSetVars' => ['TYPO3_ENSUREADMIN_ENVVALUE' => 'env_value'],
            'setOptions' => [],
            'optionName' => 'option',
            'envVariableName' => 'TYPO3_ENSUREADMIN_ENVVALUE',
            'expectedValue' => 'env_value',
        ];

        yield 'env variable and option value set returns option value' => [
            'envSetVars' => ['TYPO3_ENSUREADMIN_ENVVALUE' => 'env_value'],
            'setOptions' => ['--option' => 'option_value'],
            'optionName' => 'option',
            'envVariableName' => 'TYPO3_ENSUREADMIN_ENVVALUE',
            'expectedValue' => 'option_value',
        ];
    }

    /**
     * @test
     * @dataProvider returnsExpectedValueDataProvider
     *
     * @param array<string, mixed> $envSetVars
     * @param array<string, mixed> $setOptions
     * @param non-empty-string $optionName
     * @param non-empty-string $envVariableName
     * @param string $expectedValue
     */
    public function returnsExpectedValue(array $envSetVars, array $setOptions, string $optionName, string $envVariableName, string $expectedValue): void
    {
        $this->setEnvVars($envSetVars);
        $input = $this->buildInputInterface($setOptions);
        $result = $this->cliOptionService->determineOptionValue($input, $envVariableName, $optionName);
        self::assertSame($expectedValue, $result);
    }

    /**
     * @param array<string, mixed> $setOptions
     * @return InputInterface
     */
    protected function buildInputInterface(array $setOptions): InputInterface
    {
        return new ArrayInput(
            $setOptions,
            new InputDefinition([
                new InputOption('option', null, InputOption::VALUE_REQUIRED, 'test option'),
            ])
        );
    }

    /**
     * @param array<string, mixed> $envSetVars
     */
    protected function setEnvVars(array $envSetVars): void
    {
        if ($envSetVars === []) {
            return;
        }
        foreach ($envSetVars as $key => $value) {
            $this->originalEnvVars[$key] = getenv($key);
            $_ENV[$key] = $value;
            putenv($key . '=' . $value);
        }
    }

    protected function restoreEnvVars(): void
    {
        if ($this->originalEnvVars === []) {
            return;
        }
        foreach ($this->originalEnvVars as $key => $value) {
            $_ENV[$key] = $value;
            putenv($key . '=' . $value);
        }
        $this->originalEnvVars = [];
    }
}
