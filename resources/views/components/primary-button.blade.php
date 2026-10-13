<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-button ui-button-primary ui-button-md']) }}>
    {{ $slot }}
</button>
