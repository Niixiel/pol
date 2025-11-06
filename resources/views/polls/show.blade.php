<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <a href="{{ route('polls.index') }}" class="text-blue-600 hover:underline text-sm mb-2 inline-block">
                    Kembali ke Daftar Polling
                </a>
                <h1 class="text-3xl font-bold text-gray-800">{{ $poll->title }}</h1>
                @if($poll->description)
                    <p class="text-gray-600 mt-2">{{ $poll->description }}</p>
                @endif
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $poll->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $poll->status === 'active' ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Pertanyaan dan Jawaban</h2>
            
            @foreach($poll->questions as $index => $question)
                <div class="mb-6 pb-6 {{ !$loop->last ? 'border-b' : '' }}">
                    <div class="flex items-start mb-3">
                        <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center font-semibold mr-3 flex-shrink-0">
                            {{ $index + 1 }}
                        </span>
                        <h3 class="text-lg font-medium text-gray-800">{{ $question->text }}</h3>
                    </div>
                    
                    <div class="ml-11 space-y-2">
                        @foreach($question->answers as $answerIndex => $answer)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <span class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mr-3 flex-shrink-0">
                                    <span class="text-xs text-gray-600">{{ chr(65 + $answerIndex) }}</span>
                                </span>
                                <span class="text-gray-700">{{ $answer->text }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex gap-4">
            <a href="{{ route('polls.edit', $poll) }}" 
                class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg transition">
                Edit Polling
            </a>
            <form action="{{ route('polls.destroy', $poll) }}" method="POST" class="inline" 
                onsubmit="return confirm('Yakin ingin menghapus polling ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition">
                    Hapus Polling
                </button>
            </form>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <p>Dibuat: {{ $poll->created_at->format('d M Y H:i') }}</p>
            <p>Terakhir diupdate: {{ $poll->updated_at->format('d M Y H:i') }}</p>
        </div>
    </div>
</x-app-layout>