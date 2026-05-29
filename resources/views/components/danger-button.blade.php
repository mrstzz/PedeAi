<button {{ $attributes->merge(['type' => 'submit'])->class(['btn btn-soft btn-error']) }}>
    {{ $slot }}
</button>
