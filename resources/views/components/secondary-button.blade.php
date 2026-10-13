<button {{ $attributes->merge(['type' => 'button', 'class' => 'ui-button ui-button-secondary ui-button-md']) }}>
    {{ $slot }}
</button>
