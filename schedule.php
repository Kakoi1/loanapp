<?php
require 'vendor/autoload.php';

$jobby = new Jobby\Jobby();

$jobby->add('forUser', [
    'command' => 'php C:\xampp\htdocs\loanSystem\taskSched.php',
    'schedule' => '* * * * *',
    'output' => 'C:\xampp\htdocs\loanSystem\cron_log.txt',
    'enabled' => true,
]);

$jobby->add('DistributeMoneyBack', [
    'command' => 'php C:\xampp\htdocs\loanSystem\moneyBack.php',
    'schedule' => '42 13 * 6 1',
    'output' => 'C:\xampp\htdocs\loanSystem\cron_log.txt',
    'enabled' => true,
]);

$jobby->run();
