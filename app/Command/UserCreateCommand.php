<?php

declare(strict_types=1);

namespace Energycalculator\Command;

use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Energycalculator\Model\User;
use Energycalculator\Repository\RepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

final class UserCreateCommand extends Command
{
    /**
     * @var PasswordManagerInterface
     */
    private $passwordManager;

    /**
     * @var RepositoryInterface
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param PasswordManagerInterface $passwordManager
     * @param RepositoryInterface      $userRepository
     * @param ValidatorInterface       $validator
     */
    public function __construct(
        PasswordManagerInterface $passwordManager,
        RepositoryInterface $userRepository,
        ValidatorInterface $validator
    ) {
        parent::__construct();

        $this->passwordManager = $passwordManager;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this->setName('energycalculator2:user:create');
        $this->addArgument('email', InputArgument::REQUIRED);
        $this->addArgument('password', InputArgument::REQUIRED);
        $this->addArgument('roles', InputArgument::IS_ARRAY);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordManager->hash($password));
        $user->setRoles($roles);

        $errors = $this->validator->validate($user);
        if ([] !== $errors) {
            foreach ($errors as $error) {
                $output->writeln(sprintf('<error>%s: %s</error>', $error->getPath(), $error->getKey()));
            }

            return 1;
        }

        $this->userRepository->persist($user);
        $this->userRepository->flush();

        $output->writeln(
            sprintf(
                '<info>User with email "%s", password "%s" and roles "%s" created</info>',
                $email,
                $password,
                implode(', ', $roles)
            )
        );

        return 0;
    }
}
