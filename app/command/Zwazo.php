<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Zwazo extends Command {

	protected function configure() {
		$this
			->setName('zwazo:hello')
			->setDescription('Saluez quelqu\'un')
			->addArgument(
				'name',
				InputArgument::OPTIONAL,
				'Qui voulez-vous saluez?'
			)
			->addOption(
				'yell',
				null,
				InputOption::VALUE_NONE,
				'Si d�fini, la r�ponse est affich�e en majuscules'
			)
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$name = $input->getArgument('name');
		if ($name) {
			$text = 'Salut, '.$name;
		} else {
			$text = 'Salut';
		}

		if ($input->getOption('yell')) {
			$text = strtoupper($text);
		}

		$output->writeln($text);
	}

}