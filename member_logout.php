<?php
require_once __DIR__.'/config/bootstrap.php';
unset($_SESSION['member_id']);
redirect('index.php');
