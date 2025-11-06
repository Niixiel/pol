<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-800">Buat Polling Baru</h1>
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

        <form action="{{ route('polls.store') }}" method="POST" id="pollForm">
            @csrf
            
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Polling</h2>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Judul Polling *</label>
                    <input type="text" name="title" value="{{ old('title') }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Status *</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
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
                    <!-- Questions will be added here -->
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                    Simpan Polling
                </button>
                <a href="{{ route('polls.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
    let questionIndex = 0;

    function addQuestion() {
        const container = document.getElementById('questionsContainer');
        const questionDiv = document.createElement('div');
        questionDiv.className = 'border rounded-lg p-4 mb-4 question-item';
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
                <div class="space-y-2" id="answers-${questionIndex}">
                    <div class="flex gap-2 answer-item">
                        <input type="text" name="questions[${questionIndex}][answers][0][text]" 
                            placeholder="Jawaban 1" 
                            class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required>
                        <button type="button" onclick="removeAnswer(this, ${questionIndex})" 
                            class="text-red-600 hover:text-red-800 px-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex gap-2 answer-item">
                        <input type="text" name="questions[${questionIndex}][answers][1][text]" 
                            placeholder="Jawaban 2" 
                            class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required>
                        <button type="button" onclick="removeAnswer(this, ${questionIndex})" 
                            class="text-red-600 hover:text-red-800 px-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(questionDiv);
        questionIndex++;
        updateQuestionNumbers();
    }

    function removeQuestion(button) {
        if (document.querySelectorAll('.question-item').length > 1) {
            button.closest('.question-item').remove();
            updateQuestionNumbers();
        } else {
            alert('Minimal harus ada 1 pertanyaan');
        }
    }

    function addAnswer(questionIdx) {
        const answersContainer = document.getElementById(`answers-${questionIdx}`);
        const answerCount = answersContainer.querySelectorAll('.answer-item').length;
        
        const answerDiv = document.createElement('div');
        answerDiv.className = 'flex gap-2 answer-item';
        answerDiv.innerHTML = `
            <input type="text" name="questions[${questionIdx}][answers][${answerCount}][text]" 
                placeholder="Jawaban ${answerCount + 1}" 
                class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                required>
            <button type="button" onclick="removeAnswer(this, ${questionIdx})" 
                class="text-red-600 hover:text-red-800 px-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        answersContainer.appendChild(answerDiv);
    }

    function removeAnswer(button, questionIdx) {
        const answersContainer = document.getElementById(`answers-${questionIdx}`);
        if (answersContainer.querySelectorAll('.answer-item').length > 2) {
            button.closest('.answer-item').remove();
        } else {
            alert('Minimal harus ada 2 jawaban');
        }
    }

    function updateQuestionNumbers() {
        document.querySelectorAll('.question-item').forEach((item, index) => {
            item.querySelector('h3').textContent = `Pertanyaan ${index + 1}`;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        addQuestion();
    });
    </script>
</x-app-layout>