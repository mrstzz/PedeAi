@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->class(['input input-bordered w-full bg-base-100']) }}>
