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

namespace SBUERK\CliEnsureAdmin\Services;

use Symfony\Component\Console\Input\InputInterface;

class CliOptionService
{
    /**
     * @param InputInterface $input
     * @param non-empty-string $envName
     * @param non-empty-string $optionName
     * @return string
     */
    public function determineOptionValue(InputInterface $input, string $envName, string $optionName): string
    {
        $value = $this->getEnvironmentStringVariable($envName);
        if ($input->hasOption($optionName)) {
            $optionValue = $this->getInputStringOption($input, $optionName);
            $value = $optionValue !== '' ? $optionValue : $value;
        }
        return $value;
    }

    public function getEnvironmentStringVariable(string $envVariableName): string
    {
        $value = getenv($envVariableName);
        if (is_string($value)) {
            return $value;
        }
        return '';
    }

    public function getInputStringOption(InputInterface $input, string $optionName): string
    {
        if ($input->hasOption($optionName)) {
            $value = $input->getOption($optionName);
            if (is_string($value)) {
                return $value;
            }
        }
        return '';
    }
}
