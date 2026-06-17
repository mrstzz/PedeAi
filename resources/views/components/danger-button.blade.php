<button {{ $attributes->merge(['type' => 'submit'])->class(['btn btn-soft btn-error focus-visible:outline-secondary']) }}>
    {{ $slot }}
</button>
