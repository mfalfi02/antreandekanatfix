@php
    $themeColor = $themeColor ?? '#2563eb';
    $appleTouchIcon = $appleTouchIcon ?? asset('pwa/icons/icon-180x180.png');
    $favicon16 = $favicon16 ?? asset('pwa/icons/icon-16x16.png');
    $favicon32 = $favicon32 ?? asset('pwa/icons/icon-32x32.png');
    $appleTitle = $appleTitle ?? 'SIAP-DEK';
@endphp

<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="{{ $themeColor }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $appleTitle }}">
<link rel="apple-touch-icon" href="{{ $appleTouchIcon }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon32 }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ $favicon16 }}">
