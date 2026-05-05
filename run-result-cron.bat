@echo off
cd /d C:\xampp\htdocs\Predictionwebsite
C:\xampp\php\php.exe C:\xampp\htdocs\Predictionwebsite\wp-cron.php >> C:\xampp\htdocs\Predictionwebsite\cron-result.log 2>&1
