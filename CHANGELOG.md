# Changelog - Magento 2 Cron Scheduler Monitor

All notable changes to this project will be documented in this file.

## [1.0.0] - 2026-11-09
### Added
- `bin/magento cron:monitor:status` with formatted table, job filtering, and color-coded status badges.
- `bin/magento cron:monitor:stalled` with automatic detection and recovery of deadlocked cron jobs.
- `bin/magento cron:job:run <job_code>` for isolated on-demand job execution.
- Unit tests for duration calculations and schedule validation.
- Standardized PSR-4 namespace `PriyankJivani\CronSchedulerMonitor`.
