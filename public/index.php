<?php

date_default_timezone_set('GMT');

require_once('./../vendor/autoload.php');

use App\Bootstrap;

new Bootstrap($_POST['action'] ?? '');