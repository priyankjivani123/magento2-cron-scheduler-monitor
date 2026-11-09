# Magento 2 Cron Scheduler Monitor & Diagnostics CLI

[![Magento 2.4.x](https://img.shields.io/badge/Magento-2.4.x-orange.svg?logo=magento)](https://business.adobe.com/products/magento/magento-commerce.html)
[![PHP 8.1 - 8.4 Compatible](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3%20%7C%208.4-777BB4.svg?logo=php)](https://www.php.net/)
[![Composer Package](https://img.shields.io/badge/Composer-priyankjivani%2Fmodule--cron--scheduler--monitor-blue.svg?logo=composer)](https://packagist.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Upwork](https://img.shields.io/badge/Upwork-Available%20for%20Hire-14a800.svg?logo=upwork)](https://www.upwork.com/freelancers/priyankjivani)

Essential developer diagnostics and operational tool for **Magento 2 & Adobe Commerce**. Inspect live cron schedule states, uncover stalled or deadlocked workers, analyze execution durations, and trigger specific cron jobs on-demand.

Developed and maintained by **[Priyank Jivani](https://github.com/priyankjivani123)** (Senior Magento 2 Backend Developer).

---

## 🚀 Key Features

* **Live Status Dashboard in Console:** Formatted console table displaying Job Code, Status (`running`, `pending`, `success`, `error`, `missed`), Schedule Time, Execution Time, and Output Messages.
* **Deadlock & Stalled Job Detector:** Automatically locates orphaned jobs that have been stuck in `running` state beyond a configurable threshold and offers safe reset to prevent queue lockup.
* **On-Demand Job Execution:** Run any isolated cron job immediately (`bin/magento cron:job:run <job_code>`) without triggering the entire Magento cron cycle.
* **Lightweight & Non-Intrusive:** Zero frontend overhead, zero additional database tables — queries native `cron_schedule` collection.

---

## 🌐 Magento & PHP Compatibility Matrix

| Magento / Adobe Commerce Release | Supported PHP Versions | Compatibility Status | Notes |
| :--- | :--- | :---: | :--- |
| **Magento 2.4.4 - 2.4.5-p1** | PHP 8.1 | ✅ Supported | Tested on 2.4.4-p9 & 2.4.5-p1 |
| **Magento 2.4.5-p2 - 2.4.6-pX** | PHP 8.1, PHP 8.2 | ✅ Supported | Tested on 2.4.6-p8 |
| **Magento 2.4.7 - 2.4.7-pX** | PHP 8.2, PHP 8.3 | ✅ Supported | Officially verified on 2.4.7-p3 |
| **Magento 2.4.8 (Roadmap)** | PHP 8.3, PHP 8.4 | 🚀 Forward-Compatible | Strict types, zero implicit nullable deprecations |
| **PHP 8.5 (Future Release)** | *N/A (Pending Adobe Core)* | ⏳ Preview / Audited | Extension syntax passes PHP 8.5 compilation; awaiting Adobe Commerce core framework update |


---

## 📦 Composer Installation Instructions

### Step 1: Register GitHub VCS Repository
```bash
composer config repositories.priyankjivani-cron-scheduler-monitor vcs https://github.com/priyankjivani123/magento2-cron-scheduler-monitor.git
```

### Step 2: Require the Package
```bash
composer require priyankjivani/module-cron-scheduler-monitor:^1.0.0
```

### Step 3: Enable the Module
```bash
bin/magento module:enable PriyankJivani_CronSchedulerMonitor
bin/magento setup:upgrade
bin/magento cache:flush
```

---

## 💻 CLI Commands

### 1. View Cron Status Table
```bash
bin/magento cron:monitor:status
```
Filter by status or specific job:
```bash
bin/magento cron:monitor:status --status=error
bin/magento cron:monitor:status --job=catalog_product_out_of_stock
```

### 2. Detect and Reset Stalled Jobs
```bash
bin/magento cron:monitor:stalled --reset
```

### 3. Run Specific Cron Job On-Demand
```bash
bin/magento cron:job:run newsletter_send_all
```

---

## 🧪 Automated Testing

```bash
vendor/bin/phpunit -c phpunit.xml.dist
```

---

## 📋 Changelog

See [CHANGELOG.md](CHANGELOG.md).

---

## 📄 License

MIT License. See [LICENSE](LICENSE).

---

## Developing a Magento 2 project?

I help businesses build, customize, optimize and maintain Magento 2 / Adobe Commerce stores.

**Available for:**
- Magento 2 Custom Development
- Magento Extensions
- Bug Fixes and Troubleshooting
- Performance Optimization
- Hyvä Theme Development
- API Integrations
- Ongoing Magento Maintenance

**Hire me on Upwork:** [Priyank Jivani Upwork Profile](https://www.upwork.com/freelancers/priyankjivani)  
**GitHub:** [github.com/priyankjivani123](https://github.com/priyankjivani123)

Contact me for freelance projects and long-term collaborations.

