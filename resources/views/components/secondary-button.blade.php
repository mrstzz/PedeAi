<button {{ $attributes->merge(['type' => 'button'])->class(['btn btn-soft btn-primary']) }}>
    {{ $slot }}
</button>
