```blade
<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4">

        <div class="mb-8">
            <a href="{{ route('support.show', $ticket) }}" class="text-indigo-600">
                ← Back to Ticket
            </a>

            <h1 class="text-3xl font-bold mt-4">Edit Support Ticket</h1>

            <p class="text-gray-500 mt-2">
                Ticket #{{ $ticket->ticket_number }}
            </p>
        </div>

        <div class="bg-white border rounded-2xl shadow-sm p-8">

            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('support.update', $ticket) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block font-semibold mb-2">Subject</label>

                    <input type="text" name="subject"
                           value="{{ old('subject', $ticket->subject) }}"
                           required
                           class="w-full rounded-xl border-gray-300">
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-6">

                    <div>
                        <label class="block font-semibold mb-2">Category</label>

                        <select name="category" class="w-full rounded-xl border-gray-300">

                            @foreach(['payments', 'mpesa', 'account', 'properties', 'tenants', 'leases', 'maintenance', 'technical', 'other'] as $category)

                                <option value="{{ $category }}"
                                    @selected(old('category', $ticket->category) == $category)>
                                    {{ ucfirst($category) }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Priority</label>

                        <select name="priority" class="w-full rounded-xl border-gray-300">

                            @foreach(['low', 'medium', 'high', 'critical'] as $priority)

                                <option value="{{ $priority }}"
                                    @selected(old('priority', $ticket->priority) == $priority)>
                                    {{ ucfirst($priority) }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                </div>

                <div class="mb-6">
                    <label class="block font-semibold mb-2">Description</label>

                    <textarea name="description" rows="6" required
                              class="w-full rounded-xl border-gray-300">{{ old('description', $ticket->description) }}</textarea>
                </div>

                <div class="mb-8">
                    <label class="block font-semibold mb-2">Status</label>

                    <select name="status" class="w-full rounded-xl border-gray-300">

                        @foreach(['open', 'assigned', 'in_progress', 'awaiting_response', 'resolved', 'closed'] as $status)

                            <option value="{{ $status }}"
                                @selected(old('status', $ticket->status) == $status)>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="flex justify-end gap-3">

                    <a href="{{ route('support.show', $ticket) }}"
                       class="px-5 py-3 border rounded-xl">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-6 py-3 bg-indigo-600 text-white rounded-xl">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>
    </div>
</x-app-layout>
```