@php
    $paths = [
        'home' => '<path d="M3 10.5 12 3l9 7.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 9.5V20h14V9.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke-linecap="round" stroke-linejoin="round"/>',
        'user' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" stroke-linecap="round"/><path d="M3 9l9-3 9 3" stroke-linecap="round"/><circle cx="12" cy="7" r="4" stroke-linecap="round"/>',
        'building' => '<path d="M3 21h18" stroke-linecap="round"/><path d="M5 21V7.5A1.5 1.5 0 0 1 6.5 6H17.5A1.5 1.5 0 0 1 19 7.5V21" stroke-linecap="round"/><path d="M8 11h2M14 11h2M8 15h2M14 15h2" stroke-linecap="round"/>',
        'layers' => '<path d="m12 3 8 4.5-8 4.5-8-4.5L12 3Z" stroke-linecap="round" stroke-linejoin="round"/><path d="m4 10.5 8 4.5 8-4.5" stroke-linecap="round" stroke-linejoin="round"/><path d="m4 15 8 4.5 8-4.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'users' => '<path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1" stroke-linecap="round"/><path d="M12 10a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke-linecap="round"/><path d="M20 19v-1a4 4 0 0 0-3-3.87" stroke-linecap="round"/><path d="M16 3.13A4 4 0 0 1 16 10.5" stroke-linecap="round"/>',
        'credit-card' => '<rect x="1.5" y="5" width="21" height="14" rx="2"/><path d="M1.5 10h21" stroke-linecap="round"/><path d="M7 15h3" stroke-linecap="round"/>',
        'receipt' => '<path d="M6 3h12a2 2 0 0 1 2 2v16l-3-2-3 2-3-2-3 2-3-2V5a2 2 0 0 1 2-2Z" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 8h6M9 12h6" stroke-linecap="round"/>',
        'currency' => '<path d="M12 2v20" stroke-linecap="round"/><path d="M16 6.5c0-1.66-1.79-3-4-3s-4 1.34-4 3 1.79 3 4 3 4 1.34 4 3-1.79 3-4 3-4-1.34-4-3" stroke-linecap="round"/>',
        'alert' => '<path d="M12 9v4" stroke-linecap="round"/><path d="M12 17h.01" stroke-linecap="round"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'key' => '<circle cx="8" cy="15" r="3"/><path d="M10.5 15h10.5v3H10.5" stroke-linecap="round"/><path d="M13 13V8.5A2.5 2.5 0 0 1 15.5 6H18" stroke-linecap="round"/>',
        'arrow-right' => '<path d="M5 12h14" stroke-linecap="round"/><path d="m13 5 7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>',
        'search' => '<circle cx="11" cy="11" r="6"/><path d="m20 20-4.35-4.35" stroke-linecap="round"/>',
        'filter' => '<path d="M4 6h16" stroke-linecap="round"/><path d="M7 12h10" stroke-linecap="round"/><path d="M10 18h4" stroke-linecap="round"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18" stroke-linecap="round"/>',
        'file' => '<path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7Z" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v5h5M9 13h6M9 17h6" stroke-linecap="round"/>',
        'check' => '<path d="m5 12 4 4L19 2" stroke-linecap="round" stroke-linejoin="round"/>',
        'trash' => '<path d="M3 6h18" stroke-linecap="round"/><path d="M8 6V4h8v2" stroke-linecap="round"/><path d="M19 6l-1 14H6L5 6" stroke-linecap="round"/><path d="M10 11v5M14 11v5" stroke-linecap="round"/>',
        'edit' => '<path d="M12 20h9" stroke-linecap="round"/><path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'plus' => '<path d="M12 5v14M5 12h14" stroke-linecap="round"/>',
        'dots' => '<circle cx="5" cy="12" r="1.25"/><circle cx="12" cy="12" r="1.25"/><circle cx="19" cy="12" r="1.25"/>',
    ];

    $svg = $paths[$name] ?? $paths['home'];
@endphp

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" class="icon-inline">{!! $svg !!}</svg>
