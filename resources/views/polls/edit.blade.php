<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-800">Edit Polling</h1>
    </x-slot>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('polls.update', $poll) }}" method="POST" id="pollForm">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Polling</h2>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Judul Polling *</label>
                    <input type="text" name="title" value="{{ old('title', $poll->title) }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $poll->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Status *</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="active" {{ old('status', $poll->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $poll->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Pertanyaan</h2>
                    <button type="button" onclick="addQuestion()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                        + Tambah Pertanyaan
                    </button>
                </div>

                <div id="questionsContainer">
                    <!-- Questions will be loaded here -->
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                    Update Polling
                </button>
                <a href="{{ route('polls.show', $poll) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- DATA UNTUK JAVASCRIPT (AMAN, TIDAK ADA @json) --}}
    <div id="poll-data"
         data-questions="{{ json_encode($questionsForJs) }}"
         style="display: none;">
    </div>

    <script>
        let questionIndex = 0;

        // Baca data dari HTML
        const pollDataEl = document.getElementById('poll-data');
        const existingQuestions = pollDataEl.dataset.questions 
            ? JSON.parse(pollDataEl.dataset.questions) 
            : [];

        // Escape HTML untuk keamanan
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function addQuestion(questionData = null) {
            const container = document.getElementById('questionsContainer');
            const questionDiv = document.createElement('div');
            questionDiv.className = 'border rounded-lg p-4 mb-4 question-item';

            const questionText = questionData?.text || '';
            const answers = questionData?.answers || [];

            questionDiv.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-700">Pertanyaan ${questionIndex + 1}</h3>
                    <button type="button" onclick="removeQuestion(this)" 
                        class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-3">
                    <input type="text" name="questions[${questionIndex}][text]" 
                        placeholder="Masukkan pertanyaan" 
                        value="${escapeHtml(questionText)}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
                </div>

                <div class="answers-container" data-question="${questionIndex}">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-sm font-medium text-gray-600">Jawaban</label>
                        <button type="button" onclick="addAnswer(${questionIndex})" 
                            class="text-blue-600 hover:text-blue-800 text-sm">
                            + Tambah Jawaban
                        </button>
                    </div>
                    <div class="space-y-2" id="answers-${questionIndex}"></div>
                </div>
            `;
            container.appendChild(questionDiv);

            if (answers.length > 0) {
                answers.forEach(a => addAnswerWithData(questionIndex, a.text));
            } else {
                addAnswerWithData(questionIndex, '');
                addAnswerWithData(questionIndex, '');
            }

            questionIndex++;
            updateQuestionNumbers();
        }

        function addAnswerWithData(questionIdx, answerText = '') {
            const container = document.getElementById(`answers-${questionIdx}`);
            const count = container.querySelectorAll('.answer-item').length;

            const div = document.createElement('div');
            div.className = 'flex gap-2 answer-item';
            div.innerHTML = `
                <input type="text" name="questions[${questionIdx}][answers][${count}][text]" 
                    placeholder="Jawaban ${count + 1}" 
                    value="${escapeHtml(answerText)}"
                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    required>
                <button type="button" onclick="removeAnswer(this, ${questionIdx})" 
                    class="text-red-600 hover:text-red-800 px-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(div);
        }

        function removeQuestion(btn) {
            if (document.querySelectorAll('.question-item').length > 1) {
                btn.closest('.question-item').remove();
                updateQuestionNumbers();
            } else {
                alert('Minimal harus ada 1 pertanyaan');
            }
        }

        function removeAnswer(btn, qIdx) {
            const container = document.getElementById(`answers-${qIdx}`);
            if (container.querySelectorAll('.answer-item').length > 2) {
                btn.closest('.answer-item').remove();
            } else {
                alert('Minimal harus ada 2 jawaban');
            }
        }

        function addAnswer(qIdx) {
            addAnswerWithData(qIdx);
        }

        function updateQuestionNumbers() {
            document.querySelectorAll('.question-item').forEach((el, i) => {
                el.querySelector('h3').textContent = `Pertanyaan ${i + 1}`;
            });
        }

        // INIT: Load existing questions
        document.addEventListener('DOMContentLoaded', () => {
            if (existingQuestions.length > 0) {
                existingQuestions.forEach(q => addQuestion(q));
            } else {
                addQuestion();
            }
        });
    </script>
</x-app-layout>