<?php
declare(strict_types=1);

namespace PriyankJivani\CronSchedulerMonitor\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PriyankJivani\CronSchedulerMonitor\Model\CronManager;

class RunSpecificJobCommand extends Command
{
    private const ARG_JOB_CODE = 'job_code';

    /**
     * @var CronManager
     */
    private $cronManager;

    public function __construct(CronManager $cronManager, ?string $name = null)
    {
        parent::__construct($name);
        $this->cronManager = $cronManager;
    }

    protected function configure(): void
    {
        $this->setName('cron:job:run')
            ->setDescription('Execute a single specific cron job immediately on-demand.')
            ->addArgument(
                self::ARG_JOB_CODE,
                InputArgument::REQUIRED,
                'The Magento cron job code to execute (e.g. catalog_index_refresh_price)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobCode = (string) $input->getArgument(self::ARG_JOB_CODE);

        $output->writeln("<info>Initiating execution of cron job: [{$jobCode}]...</info>");

        try {
            $result = $this->cronManager->runSingleJob($jobCode);

            $output->writeln("<info>✓ Success! Cron job completed in {$result['duration']}s</info>");
            $output->writeln("<comment>Executed: {$result['instance']}::{$result['method']}()</comment>");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>Execution Failed: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}
