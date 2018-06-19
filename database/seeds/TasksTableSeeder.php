<?php

use Illuminate\Database\Seeder;
use App\Models\Task;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Make sure the seeder always displays current tasks instead of ones in the past
        $day = 3600 * 24; // Seconds per day
        $right_now = time(); // Seconds since 1970-01-01
        $tasks = [
            [ // Today
                'title' => 'learning vue',
                'description' => 'learning vue for beeproger coding challenge. interesting parts: vue router, components',
                'starts_at' => gmdate('Y-m-d', $right_now ) . ' 14:00:00',
                'ends_at' => gmdate('Y-m-d', $right_now) . ' 16:00:00',
            ],
            [ // Tomorrow
                'title' => 'learning angular',
                'description' => 'apart from vue, also angular is being used by the company. so thats also something to learn',
                'starts_at' => gmdate('Y-m-d', $right_now + $day * 1) . ' 14:00:00',
                'ends_at' => gmdate('Y-m-d', $right_now + $day * 1) . ' 16:00:00',
            ],
            [ // Day after tomorrow
                'title' => 'doing the coding challenge',
                'description' => 'the coding challenge consists of making a planner application where tasks can be seen and edited. also the user should be able to add a attachment',
                'starts_at' => gmdate('Y-m-d', $right_now + $day * 2) . ' 14:00:00',
                'ends_at' => gmdate('Y-m-d', $right_now + $day * 2) . ' 16:00:00',
            ],
        ];
        foreach ($tasks as $task_array) {
            $task = new Task();
            $task->fill($task_array);
            $task->save();
        }
    }
}
