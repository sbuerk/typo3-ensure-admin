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

use SBUERK\CliEnsureAdmin\Services\Exceptions\CanNotUpdateAdminUserWithoutForcingException;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AdminPasswordService
{
    /**
     * @var PasswordHashInterface
     */
    protected $hashInstance;

    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(PasswordHashInterface $hashInstance, Connection $connection)
    {
        $this->hashInstance = $hashInstance;
        $this->connection = $connection;
    }

    public function getHashedPassword(string $plainPassword): string
    {
        return $this->hashInstance->getHashedPassword($plainPassword);
    }

    public function checkPassword(string $plainPassword, string $hashedPassword): bool
    {
        return $this->hashInstance->checkPassword($plainPassword, $hashedPassword);
    }

    public function adminExists(string $username): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $count = $queryBuilder
            ->count('*')
            ->from('be_users')
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($username))
            )
            ->executeQuery()
            ->fetchOne();
        if (is_string($count)) {
            $count = (int)$count;
        }
        if (is_int($count)) {
            return $count > 0;
        }
        return false;
    }

    public function ensureAdminUser(bool $force, string $username, string $password, string $email, string $firstname, string $lastname): bool
    {
        $adminUserFields = [
            'username' => $username,
            'password' => $password,
            'email' => GeneralUtility::validEmail($email) ? $email : '',
            'admin' => 1,
            'tstamp' => $GLOBALS['EXEC_TIME'],
            'crdate' => $GLOBALS['EXEC_TIME'],
            'disable' => 0,
            'deleted' => 0,
            'realName' => trim($firstname . ' ' . $lastname, ' '),
        ];
        if ($this->adminExists($username)) {
            $adminUserUid = $this->getExistingAdminUserId($username);
            if (!$force) {
                throw CanNotUpdateAdminUserWithoutForcingException::create($username, $adminUserUid);
            }
            if ($adminUserUid > 0) {
                $this->connection->update(
                    'be_users',
                    $adminUserFields,
                    ['uid' => $adminUserUid]
                );
                return true;
            }
        }
        $this->connection->insert('be_users', $adminUserFields);
        $adminUserUid = (int)$this->connection->lastInsertId('be_users');
        return $adminUserUid > 0;
    }

    protected function getExistingAdminUserId(string $username): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $user = $queryBuilder
            ->select('uid')
            ->from('be_users')
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($username))
            )
            ->executeQuery()
            ->fetchAssociative();
        if (is_array($user)) {
            /** @var int|string|null $value */
            $value = $user['uid'] ?? 0;
            return (int)$value;
        }
        return 0;
    }
}
