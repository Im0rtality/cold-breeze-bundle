<?php

namespace Im0rtality\ColdBreezeBundle\Command;

use Im0rtality\ColdBreezeBundle\Security\TokenManager;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTokenCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('coldbreeze:token:generate')
            ->setDescription('Generates and updates token for given user')
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'User to generate token for');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var TokenManager $manager */
        $manager = $this->getContainer()->get('im0rtality_cold_breeze.token_manager');
        /** @var EntityRepository $userRepo */
        $userRepo = $this->getContainer()->get('sylius.repository.user');
        /** @var User $user */
        $user = $userRepo->findOneBy(['usernameCanonical' => $input->getOption('user')]);

        $token = $manager->generateToken();
        $output->writeln(sprintf('<info>New token for %s:</info> %s', $user->getUsernameCanonical(), $token));
        $manager->updateTokenForUser($user, $token);
        $output->writeln('Token written to database');
    }
}
