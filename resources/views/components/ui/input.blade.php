@props(['label', 'name', 'type' => 'text', 'value' => null, 'icon' => null, 'help' => null, 'labelHidden' => false, 'wrapperClass' => ''])
<label for="{{ $attributes->get('id', $name) }}" class="ui-field-label {{ $wrapperClass }}">
    <span @class(['sr-only' => $labelHidden])>{{ $label }}</span>
    <span @class(['ui-input-wrap', '!mt-0' => $labelHidden])>
        @if($icon)<x-ui.icon :name="$icon" class="ui-input-icon" />@endif
        <input
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $value }}"
            {{ $attributes->class(['ui-input', 'ui-input-with-icon' => $icon, 'ui-input-with-trailing' => isset($trailing), 'is-invalid' => $errors->has($name)]) }}
        >
        @isset($trailing)<span class="ui-input-trailing">{{ $trailing }}</span>@endisset
    </span>
    @if($help)<span class="ui-field-help">{{ $help }}</span>@endif
    @error($name)<span class="ui-field-error">{{ $message }}</span>@enderror
</label>
