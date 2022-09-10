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

namespace SBUERK\EnsureAdmin\Commands;

use SBUERK\EnsureAdmin\Services\AdminPasswordService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PasswordEncodeCommand extends Command
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
        $this->addArgument('plain', InputArgument::REQUIRED, 'Plain password to encode to be used for install tool / .env setting');
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output json result');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $plainPassword = (string)$input->getArgument('plain'); // @phpstan-ignore-line
        $asJson = (bool)$input->getOption('json');
        if ($plainPassword === '') {
            if ($asJson) {
                $io->writeln((string)\json_encode(['success' => false, 'password' => '', 'message' => 'Plain password cannot be empty.']));
            } else {
                $io->error('Plain password cannot be empty.');
            }
            return 1;
        }

        $hashedPassword = $this->adminPasswordService->getHashedPassword($plainPassword);
        $message = sprintf('Hashed pasword for %s : %s', $plainPassword, $hashedPassword);
        if ($asJson) {
            $io->writeln((string)\json_encode(['success' => true, 'password' => $hashedPassword, 'message' => $message]));
        } else {
            $io->error($message);
        }
        return 0;
    }
}
