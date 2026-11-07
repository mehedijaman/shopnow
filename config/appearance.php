<?php

return [
    'version' => 1,
    'neutrals' => [
        'slate' => '#64748B', 'gray' => '#6B7280', 'stone' => '#78716C', 'zinc' => '#71717A',
    ],
    'presets' => [
        'default' => ['primary' => '#2563EB', 'accent' => '#7C3AED', 'neutral' => 'slate'],
        'emerald' => ['primary' => '#047857', 'accent' => '#B45309', 'neutral' => 'stone'],
        'violet' => ['primary' => '#7C3AED', 'accent' => '#0369A1', 'neutral' => 'zinc'],
        'rose' => ['primary' => '#BE123C', 'accent' => '#7C3AED', 'neutral' => 'gray'],
        'warm' => ['primary' => '#92400E', 'accent' => '#047857', 'neutral' => 'stone'],
    ],
    'modes' => [
        'light' => [
            'background' => '#FCFCFD', 'surface' => '#FFFFFF',
            'text' => '#111827', 'muted' => '#4B5563', 'border' => '#6B7280',
            'topbar' => '#020617', 'topbar_text' => '#CBD5E1',
            'header' => '#FFFFFF', 'header_text' => '#334155',
            'footer' => '#020617', 'footer_text' => '#CBD5E1',
        ],
        'dark' => [
            'background' => '#111113', 'surface' => '#1F2937',
            'text' => '#F9FAFB', 'muted' => '#CBD5E1', 'border' => '#94A3B8',
            'topbar' => '#000000', 'topbar_text' => '#CBD5E1',
            'header' => '#0F172A', 'header_text' => '#E2E8F0',
            'footer' => '#000000', 'footer_text' => '#CBD5E1',
        ],
    ],
];
