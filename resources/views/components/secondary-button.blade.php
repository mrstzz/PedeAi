<button {{ $attributes->merge(['type' => 'button'])->class(['btn btn-soft btn-primary focus-visible:outline-secondary']) }}>
    {{ $slot }}
</button>
