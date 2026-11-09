<?php
declare(strict_types=1);

namespace PriyankJivani\CronSchedulerMonitor\Model;

use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Cron\Model\ConfigInterface as CronConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Cron\Model\Schedule;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

class CronManager
{
    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var CronConfigInterface
     */
    private $cronConfig;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ScheduleCollectionFactory $scheduleCollectionFactory,
        CronConfigInterface $cronConfig,
        ObjectManagerInterface $objectManager,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->cronConfig = $cronConfig;
        $this->objectManager = $objectManager;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Get recent cron jobs schedule with optional status/job filter
     */
    public function getRecentSchedules(?string $status = null, ?string $jobCode = null, int $limit = 50): array
    {
        $collection = $this->scheduleCollectionFactory->create();
        $collection->setOrder('scheduled_at', 'DESC');
        $collection->setPageSize($limit);

        if ($status) {
            $collection->addFieldToFilter('status', $status);
        }

        if ($jobCode) {
            $collection->addFieldToFilter('job_code', ['like' => '%' . $jobCode . '%']);
        }

        $results = [];
        /** @var Schedule $schedule */
        foreach ($collection as $schedule) {
            $duration = '-';
            if ($schedule->getExecutedAt() && $schedule->getFinishedAt()) {
                $start = strtotime($schedule->getExecutedAt());
                $end = strtotime($schedule->getFinishedAt());
                $duration = max(0, $end - $start) . 's';
            } elseif ($schedule->getStatus() === Schedule::STATUS_RUNNING && $schedule->getExecutedAt()) {
                $start = strtotime($schedule->getExecutedAt());
                $now = $this->dateTime->gmtTimestamp();
                $duration = max(0, $now - $start) . 's (running)';
            }

            $results[] = [
                'schedule_id'  => $schedule->getId(),
                'job_code'     => $schedule->getJobCode(),
                'status'       => $schedule->getStatus(),
                'messages'     => $schedule->getMessages() ?: '-',
                'created_at'   => $schedule->getCreatedAt(),
                'scheduled_at' => $schedule->getScheduledAt(),
                'executed_at'  => $schedule->getExecutedAt() ?: '-',
                'finished_at'  => $schedule->getFinishedAt() ?: '-',
                'duration'     => $duration
            ];
        }

        return $results;
    }

    /**
     * Find cron jobs that have been in 'running' status for more than $thresholdMinutes
     */
    public function getStalledJobs(int $thresholdMinutes = 60): array
    {
        $collection = $this->scheduleCollectionFactory->create();
        $collection->addFieldToFilter('status', Schedule::STATUS_RUNNING);

        $stalled = [];
        $now = $this->dateTime->gmtTimestamp();

        /** @var Schedule $schedule */
        foreach ($collection as $schedule) {
            if (!$schedule->getExecutedAt()) {
                continue;
            }

            $execTime = strtotime($schedule->getExecutedAt());
            $elapsedMinutes = ($now - $execTime) / 60;

            if ($elapsedMinutes >= $thresholdMinutes) {
                $stalled[] = [
                    'schedule_id'    => $schedule->getId(),
                    'job_code'       => $schedule->getJobCode(),
                    'executed_at'    => $schedule->getExecutedAt(),
                    'elapsed_minutes'=> round($elapsedMinutes, 1)
                ];
            }
        }

        return $stalled;
    }

    /**
     * Terminate or reset stalled cron jobs
     */
    public function resetStalledJob(int $scheduleId): bool
    {
        $collection = $this->scheduleCollectionFactory->create();
        $schedule = $collection->getItemById($scheduleId);

        if ($schedule && $schedule->getStatus() === Schedule::STATUS_RUNNING) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            $schedule->setMessages('Manually reset by PriyankJivani_CronSchedulerMonitor tool due to hanging process.');
            $schedule->save();
            return true;
        }

        return false;
    }

    /**
     * Run a single specific cron job by its job_code immediately
     */
    public function runSingleJob(string $jobCode): array
    {
        $jobs = $this->cronConfig->getJobs();
        $jobConfig = null;

        foreach ($jobs as $group => $groupJobs) {
            if (isset($groupJobs[$jobCode])) {
                $jobConfig = $groupJobs[$jobCode];
                break;
            }
        }

        if (!$jobConfig) {
            throw new \InvalidArgumentException("Cron job with code '{$jobCode}' is not registered in Magento.");
        }

        $instance = $jobConfig['instance'] ?? null;
        $method = $jobConfig['method'] ?? null;

        if (!$instance || !$method) {
            throw new \RuntimeException("Job configuration for '{$jobCode}' is missing instance or method declaration.");
        }

        $startTime = microtime(true);
        $model = $this->objectManager->create($instance);

        if (!method_exists($model, $method)) {
            throw new \BadMethodCallException("Method '{$method}' does not exist on class '{$instance}'.");
        }

        // Execute job
        $model->$method();
        $duration = microtime(true) - $startTime;

        return [
            'job_code'  => $jobCode,
            'instance'  => $instance,
            'method'    => $method,
            'duration'  => round($duration, 3)
        ];
    }
}
