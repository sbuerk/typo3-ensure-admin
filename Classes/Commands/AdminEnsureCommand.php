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

namespace SBUERK\CliEnsureAdmin\Commands;

use SBUERK\CliEnsureAdmin\Services\AdminPasswordService;
use SBUERK\CliEnsureAdmin\Services\Exceptions\CanNotUpdateAdminUserWithoutForcingException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminEnsureCommand extends Command
{
    /**
     * @var AdminPasswordService
     */
    protected $adminPasswordService;

    public function __construct(AdminPasswordService $adminPasswordService)
    {
        $this->adminPasswordService = $adminPasswordService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Admin username - ENV: TYPO3_ENSUREADMIN_USERNAME')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Admin email - ENV: TYPO3_ENSUREADMIN_EMAIL')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Admin password (plain) - ENV: TYPO3_ENSUREADMIN_PASSWORD')
            ->addOption('firstname', null, InputOption::VALUE_REQUIRED, 'Admin firstname - ENV: TYPO3_ENSUREADMIN_FIRSTNAME')
            ->addOption('lastname', null, InputOption::VALUE_REQUIRED, 'Admin lastname - ENV: TYPO3_ENSUREADMIN_LASTNAME')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Response with json for tool usages.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force admin user creation, means updating existing one.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $asJson = (bool)($input->getOption('json'));
        $force = (bool)($input->getOption('force'));
        $name = $this->determineOptionValue($input, 'TYPO3_ENSUREADMIN_USERNAME', 'name');
        $email = mb_strtolower($this->determineOptionValue($input, 'TYPO3_ENSUREADMIN_EMAIL', 'email'));
        $password = $this->determineOptionValue($input, 'TYPO3_ENSUREADMIN_PASSWORD', 'password');
        $firstname = $this->determineOptionValue($input, 'TYPO3_ENSUREADMIN_FIRSTNAME', 'firstname');
        $lastname = $this->determineOptionValue($input, 'TYPO3_ENSUREADMIN_LASTNAME', 'lastame');

        if (!$this->validateOptions($io, $force, $asJson, $name, $email, $password, $firstname, $lastname)) {
            return 1;
        }

        try {
            $hashedPassword = $this->adminPasswordService->getHashedPassword($password);
            $createdOrUpdated = $this->adminPasswordService->ensureAdminUser(
                $force,
                $name,
                $hashedPassword,
                $email,
                $firstname,
                $lastname
            );
            $this->message(
                $io,
                $asJson,
                $createdOrUpdated,
                ($createdOrUpdated ? sprintf(
                    'Admin user %s created or updated',
                    $name
                ) : sprintf('Admin user %s failed tp create or update', $name))
            );
        } catch (CanNotUpdateAdminUserWithoutForcingException $e) {
            $this->message($io, $asJson, false, $e->getMessage());
        } catch (\Throwable $t) {
            $this->message($io, $asJson, false, (string)$t);
            return 1;
        }
        return 0;
    }

    protected function validateOptions(SymfonyStyle $io, bool $force, bool $asJson, string $name, string $email, string $password, string $firstname, string $lastname): bool
    {
        if ($name === '') {
            return $this->message($io, $asJson, false, 'No admin username provided');
        }
        if ($password === '') {
            return $this->message($io, $asJson, false, 'No admin password provided');
        }
        if (strlen($password) < 8) {
            return $this->message($io, $asJson, false, 'Administrator password not secure enough! You are setting an important password here! It gives an attacker full control over your instance if cracked.  It should be strong (include lower and upper case characters, special characters and numbers) and must be at least eight characters long.');
        }
        if (!$force && $this->adminPasswordService->adminExists($name)) {
            return $this->message($io, $asJson, false, sprintf('Failed to create admin user %s - already exists. Use --force to enforce updating.', $name));
        }
        return true;
    }

    /**
     * @param SymfonyStyle $io
     * @param bool $asJson
     * @param bool $success
     * @param string $message
     * @return bool
     */
    protected function message(SymfonyStyle $io, bool $asJson, bool $success, string $message): bool
    {
        if ($asJson) {
            $io->writeln((string)\json_encode([
                'success' => $success,
                'message' => $message,
            ]));
        } elseif ($success) {
            $io->success($message);
        } else {
            $io->error($message);
        }
        return $success;
    }

    /**
     * @param InputInterface $input
     * @param non-empty-string $envName
     * @param non-empty-string $optionName
     * @return string
     */
    protected function determineOptionValue(InputInterface $input, string $envName, string $optionName): string
    {
        $value = (string)getenv($envName);
        if ($input->hasOption($optionName)) {
            $optionValue = (string)$input->getOption($optionName); // @phpstan-ignore-line
            $value = $optionValue !== '' ? $optionValue : $value;
        }
        return $value;
    }
}
