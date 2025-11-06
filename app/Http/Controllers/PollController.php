<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::with(['questions.answers'])->latest()->get();
        return view('polls.index', compact('polls'));
    }

    public function create()
    {
        return view('polls.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.text' => 'required|string',
        ], [
            'title.required' => 'Judul polling harus diisi',
            'questions.required' => 'Minimal harus ada 1 pertanyaan',
            'questions.*.text.required' => 'Pertanyaan harus diisi',
            'questions.*.answers.required' => 'Minimal harus ada 2 jawaban',
            'questions.*.answers.*.text.required' => 'Jawaban harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $poll = Poll::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => $validated['status'],
            ]);

            foreach ($validated['questions'] as $index => $questionData) {
                $question = $poll->questions()->create([
                    'text' => $questionData['text'],
                    'order' => $index
                ]);

                foreach ($questionData['answers'] as $answerIndex => $answerData) {
                    $question->answers()->create([
                        'text' => $answerData['text'],
                        'order' => $answerIndex
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('polls.index')
                ->with('success', 'Polling berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal membuat polling: ' . $e->getMessage()]);
        }
    }

    public function show(Poll $poll)
    {
        $poll->load(['questions.answers']);
        return view('polls.show', compact('poll'));
    }

    public function edit(Poll $poll)
    {
        $poll->load(['questions.answers']);

        // Siapkan data untuk JavaScript
        $questionsForJs = $poll->questions->map(function ($q) {
            return [
                'text'    => $q->text,
                'answers' => $q->answers->map(fn($a) => ['text' => $a->text])->toArray(),
            ];
        })->toArray();

        return view('polls.edit', compact('poll', 'questionsForJs'));
    }

    public function update(Request $request, Poll $poll)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.text' => 'required|string',
        ], [
            'title.required' => 'Judul polling harus diisi',
            'questions.required' => 'Minimal harus ada 1 pertanyaan',
            'questions.*.text.required' => 'Pertanyaan harus diisi',
            'questions.*.answers.required' => 'Minimal harus ada 2 jawaban',
            'questions.*.answers.*.text.required' => 'Jawaban harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $poll->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => $validated['status'],
            ]);

            // Hapus pertanyaan & jawaban lama
            $poll->questions()->delete();

            // Buat yang baru
            foreach ($validated['questions'] as $index => $questionData) {
                $question = $poll->questions()->create([
                    'text' => $questionData['text'],
                    'order' => $index
                ]);

                foreach ($questionData['answers'] as $answerIndex => $answerData) {
                    $question->answers()->create([
                        'text' => $answerData['text'],
                        'order' => $answerIndex
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('polls.index')
                ->with('success', 'Polling berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal mengupdate polling: ' . $e->getMessage()]);
        }
    }

    public function destroy(Poll $poll)
    {
        try {
            $poll->delete();
            return redirect()->route('polls.index')
                ->with('success', 'Polling berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus polling: ' . $e->getMessage()]);
        }
    }
}