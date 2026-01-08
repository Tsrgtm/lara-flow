@props([
    'size' => 64,
    'primary' => '#F9322C',
    'secondary' => '#6C5CE7',
    'class' => ''
])

<svg
    xmlns="http://www.w3.org/2000/svg"
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 42 42"
    {{ $attributes->merge(['class' => 'laraflow-logo ' . $class]) }}
>
    <style>
        @keyframes flowPulse {
            0%, 100% { transform: scale(1); opacity: 0.85; }
            50% { transform: scale(1.12); opacity: 1; }
        }
        @keyframes flowPulseSecondary {
            0%, 100% { transform: scale(1); opacity: 0.85; }
            50% { transform: scale(1.12); opacity: 1; }
        }
        .node {
            transform-origin: center;
            animation-duration: 2.2s;
            animation-iteration-count: infinite;
            animation-timing-function: ease-in-out;
        }
        .node-1 { animation-name: flowPulse; animation-delay: 0s; }
        .node-2 { animation-name: flowPulseSecondary; animation-delay: 0.5s; }
        .node-3 { animation-name: flowPulse; animation-delay: 1s; }
    </style>

    <!-- Nodes perfectly fill 56x56 viewBox -->
    <rect class="node node-1" x="4" y="4" width="12" height="12" rx="2" fill="{{ $primary }}"/>
    <rect class="node node-2" x="16" y="16" width="12" height="12" rx="2" fill="{{ $secondary }}"/>
    <rect class="node node-3" x="28" y="28" width="12" height="12" rx="2" fill="{{ $primary }}"/>
</svg>
