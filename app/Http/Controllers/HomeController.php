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
        $sentencesTranslate = Sentence::query()->where('status', 1)->orderBy('id', 'desc')->paginate(10);
        $sentencesTranslateCompleted = Sentence::query()->where('status', 2)->orderBy('id', 'desc')->paginate(10);

        $users = User::query()->where('role', 3)->get();

        $translates = [];

        foreach ($sentencesTranslate as $translate) {
            $translates = Translate::query()->where('sentence_id', $translate->id)->get();
        }


        return view('welcome' , compact('sentences', 'users', 'sentencesTranslate', 'translates', 'sentencesTranslateCompleted'));
    }

    public function deleteSentences(Sentence $sentence)
    {
        DB::table('sentences')->delete();
    }


}
