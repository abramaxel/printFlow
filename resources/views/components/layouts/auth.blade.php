<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Masuk' }} — {{ config('app.brand.name', config('app.name', 'FC Murni Jaya')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --press-900:#04231d; --press-800:#062e25; --press-700:#0a3f33;
            --press-600:#125142; --press-500:#1d6247; --press-300:#6fae99;
            --lime:#c8ec3f; --cyan:#22d3ee; --magenta:#f0468c; --amber:#fbbf24;
            --paper:#ffffff; --ice-50:#f6fafb; --ice-100:#eef5f7; --ice-200:#e2edf0; --ice-300:#cfe0e4;
            --ink:#0a3f33; --ink-soft:#4b6b63; --ink-mute:#7d9a93;
        }
        html, body { font-family:'Plus Jakarta Sans', system-ui, sans-serif; }
        .mono { font-family:'JetBrains Mono', ui-monospace, monospace; }

        .ax-grain::after{
            content:''; position:absolute; inset:0; pointer-events:none; opacity:.16; mix-blend-mode:overlay;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='160' height='160' filter='url(%23n)' opacity='.55'/%3E%3C/svg%3E");
        }
        .ax-halftone{
            background-image:radial-gradient(rgba(200,236,63,.30) 1.1px, transparent 1.2px);
            background-size:13px 13px;
        }
        .ax-grid-light{
            background-image:linear-gradient(rgba(10,63,51,.045) 1px, transparent 1px),
                             linear-gradient(90deg, rgba(10,63,51,.045) 1px, transparent 1px);
            background-size:34px 34px;
        }
        .ax-mesh{
            background:
              radial-gradient(60% 55% at 12% 8%, rgba(34,211,238,.24), transparent 62%),
              radial-gradient(52% 48% at 88% 18%, rgba(240,70,140,.22), transparent 60%),
              radial-gradient(70% 60% at 72% 92%, rgba(200,236,63,.20), transparent 64%),
              linear-gradient(150deg, #04231d 0%, #062e25 42%, #0a3f33 78%, #04231d 100%);
        }
        @keyframes axDrift { 0%,100%{transform:translate3d(0,0,0) scale(1)} 50%{transform:translate3d(26px,-30px,0) scale(1.14)} }
        .ax-drift{ animation:axDrift 24s ease-in-out infinite; }
        @keyframes axPan { from{background-position:0 0} to{background-position:130px 90px} }
        .ax-pan{ animation:axPan 34s linear infinite; }
        @keyframes axSpin { to { transform:rotate(360deg) } }
        .ax-spin{ animation:axSpin 46s linear infinite; }
        @keyframes axRise { from{opacity:0; transform:translateY(16px)} to{opacity:1; transform:none} }
        .ax-rise{ animation:axRise .62s cubic-bezier(.2,.75,.2,1) both; }
        @keyframes axSheet { 0%,100%{transform:translateY(0) rotate(var(--rot))} 50%{transform:translateY(-9px) rotate(var(--rot))} }
        .ax-sheet{ animation:axSheet 7.5s ease-in-out infinite; }
        @keyframes axSweep { 0%{transform:translateX(-130%) skewX(-16deg)} 62%,100%{transform:translateX(150%) skewX(-16deg)} }
        .ax-sweep{ animation:axSweep 8s ease-in-out infinite; }
        @keyframes axShake { 10%,90%{transform:translateX(-2px)} 30%,70%{transform:translateX(3px)} 50%{transform:translateX(-3px)} }
        .ax-shake{ animation:axShake .42s ease-in-out both; }
        @keyframes axPop { from{transform:scale(.72); opacity:0} to{transform:scale(1); opacity:1} }
        .ax-pop{ animation:axPop .22s cubic-bezier(.2,1.5,.4,1) both; }
        @keyframes axMarquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }
        .ax-marquee{ animation:axMarquee 26s linear infinite; }

        .ax-field{ transition:border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
        .ax-field:focus{ outline:none; border-color:var(--press-500); box-shadow:0 0 0 4px rgba(29,98,71,.14); background:#fff; }
        .ax-btn{ transition:transform .16s cubic-bezier(.2,.8,.2,1), box-shadow .2s ease, background-color .2s ease; }
        .ax-btn:hover:not(:disabled){ transform:translateY(-2px); }
        .ax-btn:active:not(:disabled){ transform:translateY(0) scale(.99); }

        .ax-scroll{ scrollbar-width:thin; scrollbar-color:rgba(29,98,71,.45) rgba(10,63,51,.06); }
        .ax-scroll::-webkit-scrollbar{ width:10px; height:10px }
        .ax-scroll::-webkit-scrollbar-track{ background:rgba(10,63,51,.06); border-radius:10px }
        .ax-scroll::-webkit-scrollbar-thumb{ background:rgba(29,98,71,.45); border-radius:10px; border:2px solid transparent; background-clip:padding-box }
        .ax-scroll::-webkit-scrollbar-thumb:hover{ background:rgba(10,63,51,.7); background-clip:padding-box }

        .ax-otp{ caret-color:transparent; }
        .ax-otp::-webkit-outer-spin-button,.ax-otp::-webkit-inner-spin-button{ -webkit-appearance:none; margin:0 }

        @media (prefers-reduced-motion: reduce){
            .ax-drift,.ax-pan,.ax-spin,.ax-rise,.ax-sheet,.ax-sweep,.ax-shake,.ax-pop,.ax-marquee{ animation:none !important }
        }
    </style>
</head>
<body class="h-full bg-ice-200 antialiased text-[color:var(--ink)] ax-scroll">
    {{ $slot }}
</body>
</html>