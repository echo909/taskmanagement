<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    static $priority = 1;
    $projects = ['API integrations','Billing system','Marketing emails'];
    return [
        'priority' => $priority++,
        'task' => $faker->sentence(),
        'project' => $projects[array_rand($projects)]
    ];
});
