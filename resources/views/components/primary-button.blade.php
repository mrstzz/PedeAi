<button {{ $attributes->merge(['type' => 'submit'])->class(['btn btn-soft btn-primary focus-visible:outline-secondary']) }}>
    {{ $slot }}
</button>
