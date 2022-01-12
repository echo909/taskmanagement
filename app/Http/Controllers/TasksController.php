<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Task;
use Log;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        if(!\Schema::hasTable('tasks')){
          try{
        Artisan::call('migrate');
            Task::truncate();
        factory(Task::class,100)->create();
          }catch(\Throwable $ex){
            return $ex->getMessage();
          }
        }
        $tasks = Task::orderBy('priority')->get();
        $data = ['page_title' => 'Task Management', 'tasks' => $tasks];
        return view('tasks',$data);
    }

    public function datasource(){

      $tasks = Task::orderBy('priority')->get();
      return response()->json($tasks);
    }

    public function sort(){
      $request = request();
      $request->from_index++;
      $request->drop_index++;
      if($request->from_index != $request->drop_index){
        if($request->from_index < $request->drop_index){
          Task::where('priority','<=',$request->drop_index)->decrement('priority');
          Task::find($request->id)->update(['priority' => $request->drop_index]);
        }
        if($request->from_index > $request->drop_index){
          Task::where('priority','>=',$request->drop_index)->increment('priority');
          Task::find($request->id)->update(['priority' => $request->drop_index]);
        }
      }
      $this->resort_priority();
    }

    public function resort_priority(){
      $tasks = Task::orderBy('priority')->get();
      $priority = 1;
      foreach ($tasks as $task) {
        Task::find($task->id)->update(['priority' => $priority]);
        $priority++;
      }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Task::increment('priority');
        $task = new Task;

        $task->task = $request->task;
        $task->project = $request->project;
        $task->priority = 1;

        $task->save();

        $this->resort_priority();
        return response()->json(['message' => 'created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
      Task::find($id)->update($request->all());
      return response()->json(['message' => 'updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Task::find($id)->delete();
      $this->resort_priority();
      return response()->json(['message' => 'deleted']);
    }

}
