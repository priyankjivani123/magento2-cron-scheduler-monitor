# Magento 2 Cron Scheduler & Health Monitor CLI

[![Magento 2.4.x](https://img.shields.io/badge/Magento-2.4.x-orange.svg?logo=magento)](https://business.adobe.com/products/magento/magento-commerce.html)
[![PHP 8.1 | 8.2 | 8.3](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3-777BB4.svg?logo=php)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Upwork Top Rated](https://img.shields.io/badge/Upwork-Available%20for%20Hire-green.svg?logo=upwork)](https://www.upwork.com/freelancers/priyankjivani)

A powerful developer diagnostics and operational tool for **Magento 2 & Adobe Commerce**. Inspect cron schedules in real-time, diagnose performance bottlenecks, detect and unblock hung/stalled processes, and trigger isolated cron jobs on-demand without executing the entire cron queue.

Developed and maintained by **[Priyank Jivani](https://github.com/priyankjivani123)** (Senior Magento 2 Backend Developer).

---

## 🚀 Why Use This Tool?

In production Magento 2 environments, standard cron jobs frequently encounter:
* **Silent Failures & Long Queues**: Jobs failing without immediate notification.
* **Hanging / Stalled Tasks**: A single hung cron job blocks other vital jobs in the same group.
* **Debugging Friction**: Running `bin/magento cron:run` triggers all scheduled jobs simultaneously, making it impossible to step-debug or profile a single specific worker.

This module provides 3 dedicated, lightweight CLI commands to solve these issues.

---

## 💻 CLI Commands & Usage

### 1. View Live Cron Schedule Status

Display a formatted console table showing job execution times, durations, and output messages:

```bash
bin/magento cron:monitor:status
```

#### Filtering Options:
```bash
# Filter by status (pending, running, success, error, missed)
bin/magento cron:monitor:status --status=error

# Filter by partial job code (e.g. index, catalog, sales)
bin/magento cron:monitor:status --job=index

# Limit output rows
bin/magento cron:monitor:status --limit=50
```

---

### 2. Detect & Reset Stalled Cron Jobs

Scan the `cron_schedule` table for processes that have been stuck in `running` status longer than a given threshold:

```bash
# Check for jobs running longer than 45 minutes
bin/magento cron:monitor:stalled

# Customize threshold in minutes (e.g., 30 min)
bin/magento cron:monitor:stalled --threshold=30

# Automatically reset stalled jobs to unblock Magento cron runners
bin/magento cron:monitor:stalled --threshold=30 --reset
```

---

### 3. Run a Specific Cron Job On-Demand

Execute a single job directly via CLI without waiting for the next cron cycle or triggering other jobs:

```bash
bin/magento cron:job:run <job_code>
```

**Example:**
```bash
bin/magento cron:job:run catalog_index_refresh_price
```

---

## 🛠 Technical Requirements

| Stack Component | Requirement |
| :--- | :--- |
| **Magento / Adobe Commerce** | 2.4.4, 2.4.5, 2.4.6, 2.4.7+ |
| **PHP** | 8.1, 8.2, 8.3 |
| **Composer** | 2.x |

---

## 📦 Installation Instructions

### Option 1: Composer Installation (Recommended)

```bash
composer require priyank/module-cron-scheduler-monitor
bin/magento module:enable Priyank_CronMonitor
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

### Option 2: Manual Installation

1. Create directory `app/code/Priyank/CronMonitor`.
2. Extract the repository files into `app/code/Priyank/CronMonitor`.
3. Enable and compile:
```bash
bin/magento module:enable Priyank_CronMonitor
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

---

## 🏗 Architecture & Code Structure

```
├── Console/
│   └── Command/
│       ├── CronStatusCommand.php      # bin/magento cron:monitor:status
│       ├── CronStalledCommand.php     # bin/magento cron:monitor:stalled
│       └── RunSpecificJobCommand.php  # bin/magento cron:job:run <job_code>
├── Model/
│   └── CronManager.php                # Business logic, schedule query, and execution wrapper
├── etc/
│   ├── di.xml                         # Console CommandList registration
│   └── module.xml
├── composer.json
├── registration.php
└── README.md
```

---

## 📄 License

Open-source module released under the [MIT License](LICENSE).

---

## 💼 Developing a Magento 2 project?

I help businesses build, customize, optimize, and maintain **Magento 2 / Adobe Commerce** stores.

**Available for:**
* Magento 2 Custom Development & Architecture
* Custom Magento Extensions & Modules
* Bug Fixes, Audits, and Troubleshooting
* Performance Optimization & Core Web Vitals
* Hyvä Theme Development & Customization
* Headless GraphQL & REST API Integrations
* Ongoing Magento Store Maintenance

👉 **Hire me on Upwork:** [Priyank Jivani on Upwork](https://www.upwork.com/freelancers/priyankjivani)  
👉 **GitHub Profile:** [priyankjivani123](https://github.com/priyankjivani123)

Contact me for freelance projects and long-term enterprise collaborations.
