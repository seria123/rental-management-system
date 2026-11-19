```blade
<x-app-layout>
    <div class="max-w-6xl mx-auto py-8 px-4">

        <div class="mb-8">
            <a href="{{ route('support.index') }}" class="text-indigo-600">
                ← Back to Support
            </a>

            <div class="flex flex-col md:flex-row md:justify-between md:items-center mt-4">
                <div>
                    <h1 class="text-3xl font-bold">{{ $ticket->subject }}</h1>
                    <p class="text-gray-500 mt-2">
                        Ticket #{{ $ticket->ticket_number }}
                    </p>
                </div>

                <span class="mt-3 md:mt-0 px-4 py-2 bg-blue-100 text-blue-700 rounded-full">
                    {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-5">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white border rounded-2xl p-6 shadow-sm">

                    <h2 class="text-xl font-bold mb-4">Problem Description</h2>

                    <p class="text-gray-700 whitespace-pre-line">
                        {{ $ticket->description }}
                    </p>

                    @if($ticket->attachments && count($ticket->attachments))
                        <div class="mt-6">
                            <h3 class="font-semibold mb-3">Attachments</h3>

                            @foreach($ticket->attachments as $attachment)
                                <a href="{{ Storage::url($attachment) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="block text-indigo-600 mb-2">
                                    View attachment
                                </a>
                            @endforeach
                        </div>
                    @endif

                </div>

                <div class="bg-white border rounded-2xl p-6 shadow-sm">

                    <h2 class="text-xl font-bold mb-5">Conversation</h2>

                    <div class="space-y-5">

                        @forelse($ticket->messages as $reply)

                            <div class="p-4 rounded-xl
                                {{ $reply->is_internal ? 'bg-amber-50 border border-amber-100' : 'bg-slate-50 border border-slate-200' }}">

                                <div class="flex justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <strong class="{{ $reply->is_internal ? 'text-amber-800' : 'text-slate-900' }}">{{ $reply->user->name ?? 'Support Team' }}</strong>
                                        @if ($reply->is_internal)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700">
                                                @include('layouts.partials.icon', ['name' => 'shield-check'])
                                                Internal
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-slate-500">
                                        {{ $reply->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>

                                <p class="text-gray-700 whitespace-pre-line">{{ $reply->message }}</p>

                                @if ($reply->attachment_path && Storage::disk('local')->exists($reply->attachment_path))
                                    <div class="mt-3">
                                        <a href="{{ Storage::disk('local')->url($reply->attachment_path) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:underline">
                                            @include('layouts.partials.icon', ['name' => 'paperclip'])
                                            View Attachment
                                        </a>
                                    </div>
                                @endif

                            </div>

                        @empty

                            <p class="text-gray-500">
                                No messages yet. Our support team will respond shortly.
                            </p>

                        @endforelse

                    </div>

                    @if(!in_array($ticket->status, ['resolved', 'closed']))
                    <form action="{{ route('support.replies.store', ['ticket' => $ticket]) }}"
                          method="POST" class="mt-6" enctype="multipart/form-data">

                        @csrf

                        <label class="block font-semibold mb-2">Write a Reply</label>

                        <textarea
                            name="message"
                            rows="4"
                            required
                            placeholder="Type your message..."
                            class="w-full rounded-xl border-gray-300">{{ old('message') }}</textarea>

                        @if(Auth::user()->isManagement())
                            <div class="mt-4 flex items-start gap-3">
                                <input type="checkbox" name="is_internal" id="is_internal"
                                    class="mt-1 rounded text-indigo-600 focus:ring-indigo-500">
                                <label for="is_internal" class="text-sm text-slate-600">
                                    Internal note (staff only, tenant cannot see)
                                </label>
                            </div>
                        @endif

                        <div class="mt-4">
                            <label class="block text-sm font-semibold mb-2">Attachment</label>
                            <input type="file" name="attachment"
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                class="w-full block rounded-xl border-gray-300 p-2">
                        </div>

                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                            class="mt-3 bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700">
                            Send Reply
                        </button>

                    </form>
                    @endif

                </div>

            </div>

            <div class="bg-white border rounded-2xl p-6 shadow-sm h-fit">

                <h2 class="text-xl font-bold mb-5">Ticket Information</h2>

                <div class="space-y-5">

                    <div>
                        <p class="text-sm text-gray-500">Category</p>
                        <p class="font-semibold">{{ ucfirst($ticket->category) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Priority</p>
                        <p class="font-semibold">{{ ucfirst($ticket->priority) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Created</p>
                        <p class="font-semibold">{{ $ticket->created_at->format('d M Y') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Last Updated</p>
                        <p class="font-semibold">{{ $ticket->updated_at->diffForHumans() }}</p>
                    </div>

                    @if($ticket->assignedTo)
                        <div>
                            <p class="text-sm text-gray-500">Assigned To</p>
                            <p class="font-semibold">{{ $ticket->assignedTo->name }}</p>
                        </div>
                    @endif

                </div>

                <a href="{{ route('support.edit', $ticket) }}"
                   class="block text-center mt-8 bg-gray-900 text-white p-3 rounded-xl">
                    Edit Ticket
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
```