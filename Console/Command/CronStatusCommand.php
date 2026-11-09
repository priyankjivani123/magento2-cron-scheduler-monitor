<?php
declare(strict_types=1);

namespace Priyank\CronMonitor\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Priyank\CronMonitor\Model\CronManager;

class CronStatusCommand extends Command
{
    private const OPTION_STATUS = 'status';
    private const OPTION_JOB = 'job';
    private const OPTION_LIMIT = 'limit';

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
        $this->setName('cron:monitor:status')
            ->setDescription('Display Magento 2 cron schedule history, statuses, and execution durations.')
            ->addOption(
                self::OPTION_STATUS,
                's',
                InputOption::VALUE_OPTIONAL,
                'Filter by status (pending, running, success, error, missed)'
            )
            ->addOption(
                self::OPTION_JOB,
                'j',
                InputOption::VALUE_OPTIONAL,
                'Filter by partial job code (e.g. catalog, index, sales)'
            )
            ->addOption(
                self::OPTION_LIMIT,
                'l',
                InputOption::VALUE_OPTIONAL,
                'Limit number of records returned (default 30)',
                30
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $status = $input->getOption(self::OPTION_STATUS);
        $job = $input->getOption(self::OPTION_JOB);
        $limit = (int) $input->getOption(self::OPTION_LIMIT);

        $output->writeln('<info>Fetching Magento 2 Cron Schedule Status...</info>');

        $schedules = $this->cronManager->getRecentSchedules($status, $job, $limit);

        if (empty($schedules)) {
            $output->writeln('<comment>No cron schedule entries found matching the criteria.</comment>');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['ID', 'Job Code', 'Status', 'Scheduled At', 'Executed At', 'Duration', 'Messages']);

        foreach ($schedules as $row) {
            $statusFormatted = $row['status'];
            if ($row['status'] === 'success') {
                $statusFormatted = "<info>{$row['status']}</info>";
            } elseif ($row['status'] === 'error' || $row['status'] === 'missed') {
                $statusFormatted = "<error>{$row['status']}</error>";
            } elseif ($row['status'] === 'running') {
                $statusFormatted = "<comment>{$row['status']}</comment>";
            }

            $table->addRow([
                $row['schedule_id'],
                $row['job_code'],
                $statusFormatted,
                $row['scheduled_at'],
                $row['executed_at'],
                $row['duration'],
                substr($row['messages'], 0, 35) . (strlen($row['messages']) > 35 ? '...' : '')
            ]);
        }

        $table->render();
        return Command::SUCCESS;
    }
}
