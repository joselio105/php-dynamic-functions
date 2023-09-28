<?php

use Plugse\Dynf\Model;

require_once './vendor/autoload.php';

$model = new Model;

$usersByEmail = $model->findByemail('julia.lima@example.com');

$usersByGenderAndLastName = $model->findByGenderAndLastName('2', 'Santos');

var_dump($usersByEmail, $usersByGenderAndLastName);
