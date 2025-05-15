<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    <!-- Include Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl">
        <h1 class="text-2xl font-bold mb-2">{{ $form->title }}</h1>

        @if($form->description)
            <p class="text-gray-600 mb-6">{{ $form->description }}</p>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('form.submit', $form) }}" method="POST" enctype="multipart/form-data">
            @csrf

            @foreach($form->fields as $field)
                <div class="mb-6">
                    <label for="{{ $field['name'] }}" class="block text-gray-700 font-medium mb-2">
                        {{ $field['label'] }}
                        @if(!empty($field['required']) && $field['required'])
                            <span class="text-red-500">*</span>
                        @endif
                    </label>

                    @if(!empty($field['help_text']))
                        <p class="text-gray-500 text-sm mb-2">{{ $field['help_text'] }}</p>
                    @endif

                    @error($field['name'])
                        <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
                    @enderror

                    @switch($field['type'])
                        @case('text')
                            <input
                                type="text"
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                value="{{ old($field['name']) }}"
                                @if(!empty($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                @if(!empty($field['required']) && $field['required']) required @endif
                                @if(!empty($field['min_length'])) minlength="{{ $field['min_length'] }}" @endif
                                @if(!empty($field['max_length'])) maxlength="{{ $field['max_length'] }}" @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                            >
                            @break

                        @case('textarea')
                            <textarea
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                @if(!empty($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                @if(!empty($field['required']) && $field['required']) required @endif
                                @if(!empty($field['min_length'])) minlength="{{ $field['min_length'] }}" @endif
                                @if(!empty($field['max_length'])) maxlength="{{ $field['max_length'] }}" @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                                rows="4"
                            >{{ old($field['name']) }}</textarea>
                            @break

                        @case('number')
                            <input
                                type="number"
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                value="{{ old($field['name']) }}"
                                @if(!empty($field['min'])) min="{{ $field['min'] }}" @endif
                                @if(!empty($field['max'])) max="{{ $field['max'] }}" @endif
                                @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                                @if(!empty($field['required']) && $field['required']) required @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                            >
                            @break

                        @case('select')
                            <select
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                @if(!empty($field['required']) && $field['required']) required @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                            >
                                <option value="">Select an option</option>
                                @if(!empty($field['options']) && is_array($field['options']))
                                    @foreach($field['options'] as $option)
                                        <option value="{{ $option['value'] }}" {{ old($field['name']) == $option['value'] ? 'selected' : '' }}>
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @break

                        @case('multiselect')
                            <select
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}[]"
                                @if(!empty($field['required']) && $field['required']) required @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                                multiple
                            >
                                @if(!empty($field['options']) && is_array($field['options']))
                                    @foreach($field['options'] as $option)
                                        <option value="{{ $option['value'] }}" {{ is_array(old($field['name'])) && in_array($option['value'], old($field['name'])) ? 'selected' : '' }}>
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @break

                        @case('checkbox')
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    value="1"
                                    {{ old($field['name']) ? 'checked' : '' }}
                                    @if(!empty($field['required']) && $field['required']) required @endif
                                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded @error($field['name']) border-red-500 @enderror"
                                >
                                <label for="{{ $field['name'] }}" class="ml-2 block text-gray-700">
                                    {{ $field['label'] }}
                                </label>
                            </div>
                            @break

                        @case('radio')
                            <div class="space-y-2">
                                @if(!empty($field['options']) && is_array($field['options']))
                                    @foreach($field['options'] as $option)
                                        <div class="flex items-center">
                                            <input
                                                type="radio"
                                                id="{{ $field['name'] }}_{{ $option['value'] }}"
                                                name="{{ $field['name'] }}"
                                                value="{{ $option['value'] }}"
                                                {{ old($field['name']) == $option['value'] ? 'checked' : '' }}
                                                @if(!empty($field['required']) && $field['required']) required @endif
                                                class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 @error($field['name']) border-red-500 @enderror"
                                            >
                                            <label for="{{ $field['name'] }}_{{ $option['value'] }}" class="ml-2 block text-gray-700">
                                                {{ $option['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @break

                        @case('date')
                            <input
                                type="date"
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                value="{{ old($field['name']) }}"
                                @if(!empty($field['required']) && $field['required']) required @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                            >
                            @break

                        @case('datetime')
                            <input
                                type="datetime-local"
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                value="{{ old($field['name']) }}"
                                @if(!empty($field['required']) && $field['required']) required @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                            >
                            @break

                        @case('file')
                            <input
                                type="file"
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                @if(!empty($field['required']) && $field['required']) required @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                            >
                            @break

                        @default
                            <input
                                type="text"
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                value="{{ old($field['name']) }}"
                                @if(!empty($field['required']) && $field['required']) required @endif
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error($field['name']) border-red-500 @enderror"
                            >
                    @endswitch
                </div>
            @endforeach

            <div class="mt-8">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Submit
                </button>
            </div>
        </form>
    </div>
</body>
</html>
