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

namespace SBUERK\CliEnsureAdmin\Services\Exceptions;

class CanNotUpdateAdminUserWithoutForcingException extends \LogicException
{
    public static function create(string $username, int $uid): CanNotUpdateAdminUserWithoutForcingException
    {
        return new self(
            sprintf('Updating existing admin user [%s]"%s" not allowed. Use --force to update existing user.', $uid, $username),
            1662904770
        );
    }
}
