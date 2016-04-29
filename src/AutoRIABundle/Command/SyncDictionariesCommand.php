<?php
namespace AutoRIABundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncDictionariesCommand extends ContainerAwareCommand {
	protected function configure()
	{
		$this
			->setName('autoria:sync_dictionaries')
			->setDescription('Syncing Auto RIA dictionaries via API')
			;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$startTime = new \DateTime();
		$output->writeln('Started syncing dictionaries at '.$startTime->format('H:i:s'));

		/** @var \AutoRIABundle\Service\AutoRIAClient $autoRIAClient */
		$autoRIAClient = $this->getContainer()->get('auto_ria_client');
		$autoRIAClient->syncDictionaries();

		$endTime = new \DateTime();
		$totalTime = $endTime->diff($startTime);
		$output->writeln('Syncing dictionaries ended at '.$endTime->format('H:i:s'));
		$output->writeln('Total time of sync process '.$totalTime->format('%H hours %i minutes %s seconds'));
	}
}