@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-heading">
            <div>
                <p class="text-sm font-medium text-indigo-600">Tenant portal</p>
                <h1 class="page-title">New maintenance request</h1>
            </div>
            <a href="{{ route('portal.dashboard') }}" class="ghost-button">Back</a>
        </div>

        <div class="panel p-6">
            <form action="{{ route('portal.maintenance.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control" required></textarea>
                    </div>

                    <div>
                        <label for="priority" class="form-label">Priority</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="submit" class="action-button">Submit request</button>
                </div>
            </form>
        </div>
    </div>
@endsection
