@extends('layouts.admin')

@section('title', 'Students | Hall Management System')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Students</h1>
</div>

<form method="GET" action="{{ route('admin.students.index') }}" class="mb-4">
    <div class="flex gap-2">
        <input
            type="search"
            name="search"
            value="{{ $search }}"
            placeholder="Search by name, email, roll, registration no, department, or phone"
            class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <button type="submit" class="rounded-lg bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-medium">
            Search
        </button>
        @if ($search !== '')
            <a href="{{ route('admin.students.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Clear
            </a>
        @endif
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Roll</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Department</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Session</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($students as $student)
                    <tr
                        class="cursor-pointer hover:bg-slate-50"
                        role="button"
                        tabindex="0"
                        onclick="openStudentModal({{ $student->id }})"
                        onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openStudentModal({{ $student->id }})}"
                    >
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $student->roll }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $student->user->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $student->department }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $student->academic_session }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $student->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($student->status->value) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            @if ($search !== '')
                                No students matched your search.
                            @else
                                No students found.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($students->hasPages())
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $students->links() }}
        </div>
    @endif
</div>

@foreach ($students as $student)
    <div
        id="student-modal-{{ $student->id }}"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        onclick="closeStudentModal({{ $student->id }})"
    >
        <div class="absolute inset-0 bg-slate-900/50"></div>
        <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl border border-slate-200" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">{{ $student->user->name }}</h2>
                <button
                    type="button"
                    class="text-slate-400 hover:text-slate-600 text-xl leading-none"
                    aria-label="Close"
                    onclick="closeStudentModal({{ $student->id }})"
                >&times;</button>
            </div>
            <div class="p-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500">Email</dt>
                        <dd class="font-medium text-slate-800 mt-1">{{ $student->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Roll</dt>
                        <dd class="font-medium text-slate-800 mt-1">{{ $student->roll }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Registration Number</dt>
                        <dd class="font-medium text-slate-800 mt-1">{{ $student->registration_no }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Department</dt>
                        <dd class="font-medium text-slate-800 mt-1">{{ $student->department }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Academic Session</dt>
                        <dd class="font-medium text-slate-800 mt-1">{{ $student->academic_session }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Phone</dt>
                        <dd class="font-medium text-slate-800 mt-1">{{ $student->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Student Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $student->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($student->status->value) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Current Seat</dt>
                        <dd class="font-medium text-slate-800 mt-1">
                            @if ($student->currentAllocation)
                                Room {{ $student->currentAllocation->seat->room->room_no }}, Seat {{ $student->currentAllocation->seat->seat_no }}
                            @else
                                Not allocated
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            <div class="px-5 py-4 border-t border-slate-200 text-right">
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    onclick="closeStudentModal({{ $student->id }})"
                >Close</button>
            </div>
        </div>
    </div>
@endforeach

<script>
    function openStudentModal(id) {
        document.getElementById('student-modal-' + id)?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeStudentModal(id) {
        document.getElementById('student-modal-' + id)?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('[id^="student-modal-"]').forEach(function (el) {
                el.classList.add('hidden');
            });
            document.body.classList.remove('overflow-hidden');
        }
    });
</script>
@endsection
