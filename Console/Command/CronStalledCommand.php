<?php
declare(strict_types=1);

namespace PriyankJivani\CronSchedulerMonitor\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use PriyankJivani\CronSchedulerMonitor\Model\CronManager;

class CronStalledCommand extends Command
{
    private const OPTION_THRESHOLD = 'threshold';
    private const OPTION_RESET = 'reset';

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
        $this->setName('cron:monitor:stalled')
            ->setDescription('Detect and optionally unblock/reset hung or stalled cron jobs.')
            ->addOption(
                self::OPTION_THRESHOLD,
                't',
                InputOption::VALUE_OPTIONAL,
                'Threshold duration in minutes to consider a job stalled (default 45 min)',
                45
            )
            ->addOption(
                self::OPTION_RESET,
                'r',
                InputOption::VALUE_NONE,
                'Automatically reset stalled jobs to error status to unblock the cron runner'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $threshold = (int) $input->getOption(self::OPTION_THRESHOLD);
        $doReset = (bool) $input->getOption(self::OPTION_RESET);

        $output->writeln("<info>Scanning for cron jobs running longer than {$threshold} minutes...</info>");

        $stalled = $this->cronManager->getStalledJobs($threshold);

        if (empty($stalled)) {
            $output->writeln('<info>✓ All running cron jobs are healthy and executing within normal limits.</info>');
            return Command::SUCCESS;
        }

        $output->writeln("<error>Detected " . count($stalled) . " stalled cron job(s)!</error>");

        $table = new Table($output);
        $table->setHeaders(['Schedule ID', 'Job Code', 'Executed At', 'Elapsed Time']);

        foreach ($stalled as $job) {
            $table->addRow([
                $job['schedule_id'],
                $job['job_code'],
                $job['executed_at'],
                "{$job['elapsed_minutes']} minutes"
            ]);

            if ($doReset) {
                $this->cronManager->resetStalledJob((int) $job['schedule_id']);
                $output->writeln("<comment>Reset Schedule ID #{$job['schedule_id']} ({$job['job_code']})</comment>");
            }
        }

        $table->render();

        if (!$doReset) {
            $output->writeln("\n<comment>Tip: Run with --reset flag to unblock stuck schedules automatically.</comment>");
        }

        return Command::SUCCESS;
    }
}
