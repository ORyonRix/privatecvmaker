<?php
require 'config/helpers.php';
session_destroy();
redirect('login.php');
