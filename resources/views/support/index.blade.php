```blade
<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Support Centre</h1>
                <p class="text-gray-500 mt-1">Manage your support requests and get assistance.</p>
            </div>

            <a href="{{ route('support.create') }}"
               class="mt-4 md:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold">
                + Create Ticket
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-5">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">

            @foreach([
                ['Total Tickets', $tickets->total(), 'blue'],
                ['Open', $openTickets ?? 0, 'yellow'],
                ['In Progress', $inProgressTickets ?? 0, 'purple'],
                ['Resolved', $resolvedTickets ?? 0, 'green']
            ] as $stat)

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <p class="text-sm text-gray-500">{{ $stat[0] }}</p>
                    <h2 class="text-3xl font-bold mt-2 text-gray-900">{{ $stat[1] }}</h2>
                </div>

            @endforeach

        </div>

        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-900">Support Tickets</h2>
            </div>

            <form method="GET" action="{{ route('support.index') }}"
                  class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">

                <input type="text" name="search"
                       value="{{ request('search') }}"
                       placeholder="Search tickets..."
                       class="rounded-xl border-gray-300">

                <select name="status" class="rounded-xl border-gray-300">
                    <option value="">All Statuses</option>
                    @foreach(['open', 'assigned', 'in_progress', 'awaiting_response', 'resolved', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>

                <button class="bg-gray-900 text-white rounded-xl px-5 py-2">
                    Filter Tickets
                </button>

            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left">

                    <thead class="bg-gray-50 text-gray-500 text-sm">
                        <tr>
                            <th class="p-4">Ticket</th>
                            <th class="p-4">Subject</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Priority</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse($tickets as $ticket)

                            <tr class="hover:bg-gray-50">

                                <td class="p-4 font-semibold text-indigo-600">
                                    {{ $ticket->ticket_number }}
                                </td>

                                <td class="p-4">
                                    {{ $ticket->subject }}
                                </td>

                                <td class="p-4">
                                    {{ ucfirst($ticket->category) }}
                                </td>

                                <td class="p-4">
                                    {{ ucfirst($ticket->priority) }}
                                </td>

                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                        {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>

                                <td class="p-4 text-gray-500">
                                    {{ $ticket->created_at->format('d M Y') }}
                                </td>

                                <td class="p-4">
                                    <a href="{{ route('support.show', $ticket) }}"
                                       class="text-indigo-600 hover:underline font-semibold">
                                        View
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="p-10 text-center text-gray-500">
                                    No support tickets found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            <div class="p-5">
                {{ $tickets->links() }}
            </div>

        </div>

    </div>
</x-app-layout>
```