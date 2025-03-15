<x-filament::page>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="space-y-6">
            {{-- Header with Status --}}
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-primary-600 dark:text-primary-500">Application Details</h2>
                <div
                    class="px-4 py-2 rounded-full text-sm font-bold shadow-sm
                    @if ($record->status === 'accepted') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 border border-green-200 dark:border-green-800
                    @elseif($record->status === 'rejected') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100 border border-red-200 dark:border-red-800
                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-600 @endif">
                    {{ ucfirst($record->status) }}
                </div>
            </div>

            {{-- Job Details --}}
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-lg font-semibold mb-2 text-primary-600 dark:text-primary-500">Applied Position</h3>
                <p class="text-gray-900 dark:text-white">{{ $record->job->title }}</p>
            </div>

            {{-- Applicant Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                <div>
                    <h3 class="text-lg font-semibold mb-2 text-primary-600 dark:text-primary-500">Personal Information
                    </h3>
                    <div class="space-y-2">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Name:</span>
                            <span class="ml-2 text-gray-900 dark:text-white">{{ $record->full_name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Email:</span>
                            <span class="ml-2 text-gray-900 dark:text-white">{{ $record->email }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Phone:</span>
                            <span
                                class="ml-2 text-gray-900 dark:text-white">{{ $record->phone ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-2 text-primary-600 dark:text-primary-500">Application Details
                    </h3>
                    <div class="space-y-2">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Applied Date:</span>
                            <span
                                class="ml-2 text-gray-900 dark:text-white">{{ $record->created_at->format('M d, Y') }}</span>
                        </div>
                        @if ($record->resume_path)
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Resume:</span>
                                <a href="{{ Storage::disk('public')->url($record->resume_path) }}"
                                    class="ml-2 text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                                    target="_blank">
                                    View Resume
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cover Letter --}}
            @if ($record->cover_letter)
                <div>
                    <h3 class="text-lg font-semibold mb-2 text-primary-600 dark:text-primary-500">Cover Letter</h3>
                    <div class="prose dark:prose-invert max-w-none text-gray-900 dark:text-white">
                        {{ $record->cover_letter }}
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-4 mt-6">
                <x-filament::button wire:click="updateStatus('rejected')" color="danger" outlined class="px-6">
                    Reject
                </x-filament::button>

                <x-filament::button wire:click="updateStatus('accepted')" color="success" class="px-6">
                    Accept
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament::page>
