<div>
    <label for="room_no" class="block text-sm font-medium text-slate-700 mb-1">
        Room Number <span class="text-red-500">*</span>
    </label>
    <input
        type="text"
        name="room_no"
        id="room_no"
        value="{{ old('room_no', $room?->room_no) }}"
        required
        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('room_no') border-red-500 @enderror"
    >
    @error('room_no')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="floor" class="block text-sm font-medium text-slate-700 mb-1">
        Floor <span class="text-red-500">*</span>
    </label>
    <input
        type="number"
        name="floor"
        id="floor"
        value="{{ old('floor', $room?->floor) }}"
        required
        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('floor') border-red-500 @enderror"
    >
    @error('floor')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="capacity" class="block text-sm font-medium text-slate-700 mb-1">
        Capacity <span class="text-red-500">*</span>
    </label>
    <input
        type="number"
        name="capacity"
        id="capacity"
        min="1"
        value="{{ old('capacity', $room?->capacity) }}"
        required
        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('capacity') border-red-500 @enderror"
    >
    @error('capacity')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="status" class="block text-sm font-medium text-slate-700 mb-1">
        Status <span class="text-red-500">*</span>
    </label>
    <select
        name="status"
        id="status"
        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
    >
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $room?->status?->value) === $status->value)>
                {{ ucfirst($status->value) }}
            </option>
        @endforeach
    </select>
    @error('status')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
