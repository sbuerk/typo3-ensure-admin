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

namespace SBUERK\EnsureAdmin\Services;

use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;

class AdminPasswordService
{
    /**
     * @var PasswordHashInterface
     */
    protected $hashInstance;

    public function __construct(PasswordHashInterface $hashInstance)
    {
        $this->hashInstance = $hashInstance;
    }

    public function getHashedPassword(string $plainPassword): string
    {
        return $this->hashInstance->getHashedPassword($plainPassword);
    }

    public function checkPassword(string $plainPassword, string $hashedPassword): bool
    {
        return $this->hashInstance->checkPassword($plainPassword, $hashedPassword);
    }
}
