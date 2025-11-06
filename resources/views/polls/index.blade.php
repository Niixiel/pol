<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">Daftar Polling</h1>
            <a href="{{ route('polls.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                Buat Polling Baru
            </a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($polls->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500 mb-4">Belum ada polling yang dibuat</p>
                <a href="{{ route('polls.create') }}" class="text-blue-600 hover:underline">Buat polling pertama Anda</a>
            </div>
        @else
            <div class="grid gap-6">
                @foreach($polls as $poll)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $poll->title }}</h2>
                                @if($poll->description)
                                    <p class="text-gray-600 mb-3">{{ $poll->description }}</p>
                                @endif
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $poll->questions->count() }} Pertanyaan
                                    </span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $poll->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $poll->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('polls.show', $poll) }}" class="text-blue-600 hover:text-blue-800 p-2" title="Lihat">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('polls.edit', $poll) }}" class="text-yellow-600 hover:text-yellow-800 p-2" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('polls.destroy', $poll) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus polling ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400">
                            Dibuat: {{ $poll->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>