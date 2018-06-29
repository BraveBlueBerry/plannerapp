<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Storage;
use Illuminate\Http\File;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::get()->toArray();
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $task               = new Task();
        $task->title        = $request->input('title');
        $task->description  = $request->input('description');
        $task->starts_at    = $request->input('starts_at');
        $task->ends_at      = $request->input('ends_at');
        $task->save();

        // Save attachment if there is one
        if (!empty($_FILES)) {
            if (!empty($_FILES['attachment'])) {
                $att = $_FILES['attachment'];
                if (!$att['error']) {
                    $file_path = Storage::putFileAs('public', new File($att['tmp_name']), $task->id . '_' . $att['name']);
                    $task->attachment = $file_path;
                    $task->save();
                }
            }
        }

        return response()->json(["message"=>"Stored a new task"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::get()->where('id', $id)->first();
        $task->attachment = str_replace('public/', 'storage/', $task->attachment);
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //TODO: Validate postdata
        $task               = Task::find($id);
        $task->title        = $request->input('title');
        $task->description  = $request->input('description');
        $task->starts_at    = $request->input('starts_at');
        $task->ends_at      = $request->input('ends_at');
        $task->save();

        // Save attachment if there is one
        if (!empty($_FILES)) {
            if (!empty($_FILES['attachment'])) {
                $att = $_FILES['attachment'];
                if (!$att['error']) {
                    $file_path = Storage::putFileAs('public', new File($att['tmp_name']), $task->id . '_' . $att['name']);
                    $task->attachment = $file_path;
                    $task->save();
                }
            }
        }

        return response()->json(["message"=>"saved the task"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
