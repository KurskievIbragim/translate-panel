<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use App\Models\Translate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SentenceController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:txt',
            'status'=> '',
        ]);


        $file = $request->file('file');
        $content = file_get_contents($file->getRealPath());
        $sentences = explode('.', $content);



        DB::beginTransaction();
        try {
            foreach ($sentences as $sentence) {
                $trimmedSentence = trim($sentence);
                if (!empty($trimmedSentence)) {
                    $length = mb_strlen($trimmedSentence);

                    // Определение стоимости перевода в зависимости от длины предложения
                    if ($length <= 100) {
                        $price = 5;
                    } elseif ($length <= 200) {
                        $price = 10;
                    } else {
                        $price = 15;
                    }

                    Sentence::create([
                        'sentence' => $trimmedSentence,
                        'price' => $price
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Файл успешно загружен и переведен в базу');
    }


    public function getSentence()
    {
        $sentence = null;

        $sentences = Sentence::all();

        // Проверяем, есть ли предложение в сеансе
        if (Session::has('current_sentence_id')) {
            $sentenceId = Session::get('current_sentence_id');
            $sentence = Sentence::find($sentenceId);

            // Проверяем, если предложение не найдено или его статус изменен, очищаем сеанс
            if (!$sentence || $sentence->status != 0 || $sentence->locked_by != Auth::id()) {
                Session::forget('current_sentence_id');
                $sentence = null;
            }
        }

        // Если предложение не найдено в сеансе, выбираем новое предложение
        if (!$sentence) {
            DB::transaction(function () use (&$sentence) {
                $sentence = Sentence::where('status', 0)
                    ->whereNull('locked_by')
                    ->inRandomOrder() // Выбираем случайное предложение
                    ->first();

                if ($sentence) {
                    $sentence->update(['locked_by' => Auth::id()]);
                    Session::put('current_sentence_id', $sentence->id);
                }
            });
        }

        $sentencesTranslate = Sentence::query()->with(['translations', 'author'])->where('status', 1)->orderBy('id', 'desc')->paginate(10);
        $users = User::query()->where('role', 3)->get();



        if(\auth()->user()->role) {
            $completedSentences = Translate::query()->where('user_id', \auth()->user()->id)->get();
        }

        $deletedSentences = Translate::query()->where('deleted_at', '!=', null)->get();


        return view('translate', [
            'sentence' => $sentence,
            'sentencesTranslate' => $sentencesTranslate,
            'users' => $users,
            'sentences' => $sentences,
            'completedSentences' => $completedSentences,
            'deletedSentences' => $deletedSentences,
        ]);
    }



    public function saveTranslation(Request $request)
    {


        $request->validate([
            'sentence_id' => 'required|exists:sentences,id',
            'translation' => 'required|string',
        ]);


        $sentence = Sentence::find($request->sentence_id);

        if ($sentence->status == 0 && $sentence->locked_by == Auth::id()) {
            Translate::create([
                'sentence_id' => $sentence->id,
                'user_id' => Auth::id(),
                'translation' => $request->translation,
            ]);

            $sentence->update(['status' => 1, 'locked_by' => null]);

            return redirect()->route('translate')->with('success', 'Translation saved successfully.');
        }

        return redirect()->route('translate')->with('error', 'Failed to save translation.');
    }

    public function approveTranslation(Request $request, Sentence $sentence)
    {
        // Проверяем, что статус предложения равен 1
        if ($sentence->status == 1) {
            // Обновляем статус на 2
            $sentence->update(['status' => 2]);

            return redirect()->route('translate')->with('success', 'Translation approved successfully.');
        }

        return redirect()->route('translate')->with('error', 'Failed to approve translation.');
    }

    public function rejectTranslation(Request $request, Sentence $sentence)
    {
        // Проверяем, что статус предложения равен 1
        if ($sentence->status == 1) {
            // Удаляем перевод из таблицы translates
            Translate::where('sentence_id', $sentence->id)->delete();

            // Обновляем статус на 0
            $sentence->update(['status' => 0, 'locked_by' => null]);

            return redirect()->route('translate')->with('success', 'Translation rejected successfully.');
        }

        return redirect()->route('translate')->with('error', 'Failed to reject translation.');
    }
    public function moderate() {

        $sentences = Sentence::query()->where('locked_by', '!=', null)->get();
        $users = User::all();

        $lockedUser = [];
        foreach ($sentences as $sentence) {
            $lockedUser = User::query()->where('id', $sentence->locked_by)->get();
        }



        return view('sentence-moderate', [
            'sentences' => $sentences,
            'users' => $users,
            'lockedUser' => $lockedUser
        ]);
    }

    public function resetTeacherSentence(Sentence $sentence) {

        $sentence->update(['locked_by'=> null]);
        return redirect()->route('home');
    }

}
