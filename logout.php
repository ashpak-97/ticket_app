<?php
session_start();
session_destroy();
header('location: http://localhost:84/quote_master/');
