```blade
<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4">

        <div class="mb-8">
            <a href="{{ route('support.index') }}" class="text-indigo-600">
                ← Back to Support
            </a>

            <h1 class="text-3xl font-bold mt-4">Create Support Ticket</h1>

            <p class="text-gray-500 mt-2">
                Describe your issue and our support team will assist you.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-8">

            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('support.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-6">
                    <label class="block font-semibold mb-2">Subject</label>

                    <input type="text" name="subject"
                           value="{{ old('subject') }}"
                           required maxlength="255"
                           placeholder="Briefly describe your problem"
                           class="w-full rounded-xl border-gray-300">

                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-6">

                    <div>
                        <label class="block font-semibold mb-2">Category</label>

                        <select name="category" required class="w-full rounded-xl border-gray-300">

                            <option value="">Select category</option>

                            @foreach([
                                'payments' => 'Payments',
                                'mpesa' => 'M-Pesa',
                                'account' => 'Account',
                                'properties' => 'Properties',
                                'tenants' => 'Tenants',
                                'leases' => 'Lease Agreements',
                                'maintenance' => 'Maintenance',
                                'technical' => 'Technical Issue',
                                'other' => 'Other'
                            ] as $value => $label)

                                <option value="{{ $value }}" @selected(old('category') == $value)>
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Priority</label>

                        <select name="priority" required class="w-full rounded-xl border-gray-300">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                </div>

                <div class="mb-6">
                    <label class="block font-semibold mb-2">Description</label>

                    <textarea name="description" rows="6" required
                              placeholder="Explain your problem in detail..."
                              class="w-full rounded-xl border-gray-300">{{ old('description') }}</textarea>
                </div>

                <div class="mb-8">
                    <label class="block font-semibold mb-2">Attachments</label>

                    <input type="file" name="attachments[]" multiple
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border rounded-xl p-3">

                    <p class="text-sm text-gray-500 mt-2">
                        You can attach screenshots or PDF documents.
                    </p>
                </div>

                <div class="flex justify-end gap-3">

                    <a href="{{ route('support.index') }}"
                       class="px-5 py-3 rounded-xl border">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                        Submit Ticket
                    </button>

                </div>

            </form>

        </div>
    </div>
</x-app-layout>
```