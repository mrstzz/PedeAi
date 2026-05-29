<button {{ $attributes->merge(['type' => 'submit'])->class(['btn btn-soft btn-primary']) }}>
    {{ $slot }}
</button>
