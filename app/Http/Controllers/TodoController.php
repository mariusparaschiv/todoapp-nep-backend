<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TodoController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
      $todos = Todo::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
      return response()->json($todos);
  }

  public function store(Request $request)
  {
      $this->validate($request, [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'completed' => 'required|boolean',
      ]);
      $todo = new Todo();
      $todo->title = $request->input('title');
      $todo->description = $request->input('description');
      $todo->user_id = Auth::user()->id;
      $todo->save();
      return response()->json($todo);
  }

  public function show($id)
  {
      $todo = Todo::find($id);
      if ($todo->user_id == Auth::user()->id) {
          return response()->json($todo);
      } else {
          return response()->json(['error' => 'Unauthorised'], 401);
      }
  }

  public function update(Request $request, $id)
  {
      $this->validate($request, [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'completed' => 'required|boolean',
      ]);
      $todo = Todo::find($id);
      if ($todo->user_id == Auth::user()->id) {
          $todo->title = $request->input('title');
          $todo->description = $request->input('description');
          $todo->completed = $request->input('completed');
          $todo->save();
          return response()->json($todo);
      } else {
          return response()->json(['error' => 'Unauthorised'], 401);
      }
  }

  public function destroy($id)
  {
      $todo = Todo::find($id);
      if ($todo->user_id == Auth::user()->id) {
          $todo->delete();
          return response()->json(['success' => 'Todo deleted successfully']);
      } else {
          return response()->json(['error' => 'Unauthorised'], 401);
      }
  }

  public function complete($id)
  {
      $todo = Todo::find($id);
      if ($todo->user_id == Auth::user()->id) {
          $todo->completed = true;
          $todo->save();
          return response()->json($todo);
      } else {
          return response()->json(['error' => 'Unauthorised'], 401);
      }
  }
}
