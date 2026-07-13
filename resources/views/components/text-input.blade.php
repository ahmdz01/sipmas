@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'border border-gray-300 focus:border-blue-500 focus:ring-blue-500
                rounded-md shadow-sm text-sm text-gray-800 px-3 py-2 w-full
                transition duration-150'
]) !!}>