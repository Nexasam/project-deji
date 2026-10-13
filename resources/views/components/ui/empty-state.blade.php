@props(['icon' => 'bell', 'title', 'description'])

<div {{ $attributes->class(['ui-empty-state']) }}>
    <span class="ui-empty-state-icon"><x-ui.icon :name="$icon" /></span>
    <h2 class="ui-empty-state-title">{{ $title }}</h2>
    <p class="ui-empty-state-description">{{ $description }}</p>
    @isset($action)
        <div class="ui-empty-state-action">{{ $action }}</div>
    @endisset
</div>
