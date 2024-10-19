<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use App\Models\Translate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $sentences = Sentence::all();
        $sentencesTranslate = Sentence::query()->with('translations')->where('status', 1)->orderBy('id', 'desc')->get();
        $sentencesTranslateCompleted = Sentence::query()->with('translations')->where('status', 2)->orderBy('id', 'desc')->get();

        $users = User::query()->where('role', 3)->get();


        return view('welcome' , compact('sentences', 'users', 'sentencesTranslate', 'sentencesTranslateCompleted'));
    }

    public function deleteSentences(Sentence $sentence)
    {
        DB::table('sentences')->delete();
    }

    public function completedSentences()
    {



        $sentencesTranslateCompleted = Sentence::query()->where('status', 2)->orderBy('id', 'desc')->paginate(10);

        $users = User::query()->where('role', 3)->get();




        return view('sentences.completed', compact('sentencesTranslateCompleted', 'users'));
    }

    public function search(Request $request)
    {

        $query = $request->input('search');



        // Поиск по предложениям
        $sentences = Sentence::query()
            ->where('sentence', 'LIKE', "%{$query}%")
            ->orderBy('id', 'desc')
            ->get();

        // Поиск по переводам со статусом 1
        $sentencesTranslate = Sentence::query()
            ->where('status', 1)
            ->where('sentence', 'LIKE', "%{$query}%")
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Поиск по переводам со статусом 2
        $sentencesTranslateCompleted = Sentence::query()
            ->where('status', 2)
            ->where('sentence', 'LIKE', "%{$query}%")
            ->orderBy('id', 'desc')
            ->paginate(10);

        $translates = [];

        foreach ($sentencesTranslate as $translate) {
            $translates = Translate::query()->where('sentence_id', $translate->id)->get();
        }

        return view('sentences.search', compact('sentences', 'sentencesTranslate', 'sentencesTranslateCompleted', 'query', 'translates'));
    }


}
