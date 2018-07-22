<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Storage;
use Illuminate\Http\File;
use Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $tasks = Task::get()->toArray();
        } catch (\Exception $e) {
            return response()->json(["message"=>"Can't connect to database server. Contact site administration."], 500);
        }

        return response()->json($tasks, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|max:255|min:1',
            'description'   => 'required|min:1',
            'starts_at'     => 'bail|required|date',
            'ends_at'       => 'required|date|after:starts_at',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $errorarray) {
                foreach ($errorarray as $error) {
                    $errors[] = $error;
                }
            }
            return response()->json(["message"=>"Request contains errors.", "errors"=>$errors], 400);
        }
        $task               = new Task();
        $task->title        = $request->input('title');
        $task->description  = $request->input('description');
        $task->starts_at    = $request->input('starts_at');
        $task->ends_at      = $request->input('ends_at');
        try {
            $task->save();
        } catch (\Exception $e) {
            return response()->json(["message"=>"Can't connect to database server. Contact site administration."], 500);
        }

        // Save attachment if there is one
        if (!empty($_FILES)) {
            if (!empty($_FILES['attachment'])) {
                $att = $_FILES['attachment'];
                if (!$att['error']) {
                    try {
                        $file_path = Storage::putFileAs('public', new File($att['tmp_name']), $task->id . '_' . $att['name']);
                    } catch (\Exception $e) {
                        return response()->json(["message"=>"Can't write to storage. Contact site administration."], 500);
                    }

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
        try {
            $task = Task::get()->where('id', $id)->first();
        } catch (\Exception $e) {
            return response()->json(["message"=>"Can't connect to database server. Contact site administration."], 500);
        }
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
        $validator = Validator::make($request->all(), [
            'title'         => 'required|max:255|min:1',
            'description'   => 'required|min:1',
            'starts_at'     => 'bail|required|date',
            'ends_at'       => 'required|date|after:starts_at',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->getMessages() as $errorarray) {
                foreach ($errorarray as $error) {
                    $errors[] = $error;
                }
            }
            return response()->json(["message"=>"Request contains errors.", "errors"=>$errors], 400);
        }
        try {
            $task               = Task::find($id);
            $task->title        = $request->input('title');
            $task->description  = $request->input('description');
            $task->starts_at    = $request->input('starts_at');
            $task->ends_at      = $request->input('ends_at');
            $task->save();
        } catch (\Exception $e) {
            return response()->json(["message"=>"Can't connect to database server. Contact site administration."], 500);
        }

        // Save attachment if there is one
        if (!empty($_FILES)) {
            if (!empty($_FILES['attachment'])) {
                $att = $_FILES['attachment'];
                if (!$att['error']) {
                    try {
                        $file_path = Storage::putFileAs('public', new File($att['tmp_name']), $task->id . '_' . $att['name']);
                    } catch (\Exception $e) {
                        return response()->json(["message"=>"Can't write to storage. Contact site administration."], 500);
                    }
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
        try {
            $task = Task::find($id);
            $task->delete();
        } catch (\Exception $e) {
            return response()->json(["message"=>"Can't connect to database server. Contact site administration."], 500);
        }

        return response()->json(["message" => "deleted the tas"], 200);
    }
}
