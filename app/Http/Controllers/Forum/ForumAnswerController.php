<?php

namespace App\Http\Controllers\Forum;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreForumAnswerRequest;
use App\Models\Forum\ForumAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Forum\Forum;

class ForumAnswerController extends Controller
{
    public function store(StoreForumAnswerRequest $request)
    {
        $answers = ForumAnswer::where('forum_id', $request->forum_id)
            ->count();
        if ($answers >= 3) {
            return redirect()->back()
                ->with('error', 'No se pueden agregar más respuestas a esta discusión.');
        }

        DB::Transaction(function () use ($request) {
            $answer = new ForumAnswer();
            $answer->forum_id = $request->forum_id;
            $answer->user_id = Auth::user() ? Auth::user()->id : null;
            $answer->answer = $request->answer;
            $answer->save();

            $answers = ForumAnswer::where('forum_id', $request->forum_id)
                ->count();
            if ($answers >= 3) {
                Forum::where('id', $request->forum_id)
                    ->update(['forum_status_id' => 2]);
            }
        });
        return redirect()->back()
            ->with('success', 'Respuesta agregada correctamente.');
    }
}
