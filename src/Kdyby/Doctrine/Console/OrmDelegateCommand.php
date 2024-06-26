<?php
/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Kdyby\Doctrine\Console;

use Closure;
use Kdyby\Doctrine\Tools\CacheCleaner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command Delegate.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 */
abstract class OrmDelegateCommand extends Command
{

    use \Nette\SmartObject;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

    /**
     * @var CacheCleaner
     */
    public $cacheCleaner;

    public function __construct(CacheCleaner $cacheCleaner)
    {
        parent::__construct();
        $this->cacheCleaner = $cacheCleaner;
    }

    /**
     * @return \Symfony\Component\Console\Command\Command
     */
    abstract protected function createCommand();

    /**
     * @param string[]|bool|string|null $entityManagerName
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function wrapCommand($entityManagerName)
    {
        CommandHelper::setApplicationEntityManager($this->getHelper('container'), $entityManagerName);
        $this->command->setApplication($this->getApplication());
        return $this->command;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->command = $this->createCommand();

        $this->setName($this->command->getName());
        $this->setHelp($this->command->getHelp());
        $this->setDefinition($this->command->getDefinition());
        $this->setDescription($this->command->getDescription());
        if (!$this->getDefinition()->hasOption('em')) {
            $this->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $wrappingCommand = $this->wrapCommand($input->getOption('em'));
        $res = Closure::bind(
                fn($class) => $class->execute($input, $output), null, get_class($wrappingCommand)
            )($wrappingCommand);
        return $res;
    }

    /**
     * {@inheritDoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $wrappingCommand = $this->wrapCommand($input->getOption('em'));
        Closure::bind(
            fn($class) => $class->interact($input, $output), null, get_class($wrappingCommand)
        )($wrappingCommand);
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $wrappingCommand = $this->wrapCommand($input->getOption('em'));
        Closure::bind(
            fn($class) => $class->initialize($input, $output), null, get_class($wrappingCommand)
        )($wrappingCommand);
    }

}
