<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AvoCato Admin</title>
    <style>
        :root {
            --bg: #090909;
            --panel: #151515;
            --line: #2a2a2a;
            --text: #f5f5f0;
            --muted: #aaa;
            --green: #b9dc3d;
            --danger: #e35b5b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        a { color: inherit; text-decoration: none; }
        .container { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
        header {
            border-bottom: 1px solid var(--line);
            background: #0e0e0e;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .header {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .brand { font-size: 22px; font-weight: 800; }
        .brand span { color: var(--green); }
        .admin-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .admin-nav a {
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            color: var(--muted);
            font-weight: 700;
            font-size: 14px;
        }
        .admin-nav a.active,
        .admin-nav a:hover {
            color: var(--text);
            border-color: #4a4a4a;
            background: #171717;
        }
        main { padding: 40px 0 70px; }
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px;
        }
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }
        .toolbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }
        .admin-search {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .admin-search input {
            width: min(300px, 100%);
        }
        .btn {
            border: 0;
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            cursor: pointer;
            display: inline-block;
        }
        .btn-primary { background: var(--green); color: #111; }
        .btn-secondary { background: #252525; color: white; }
        .btn-danger { background: #3a1818; color: #ffaaaa; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 14px 10px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
        }
        th { color: var(--muted); font-size: 13px; }
        .muted { color: var(--muted); }
        .flash {
            background: #1d2815;
            color: #dff0af;
            border: 1px solid #43542b;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .errors {
            background: #321919;
            color: #ffc2c2;
            border: 1px solid #6d3333;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        .field { margin-bottom: 18px; }
        label { display: block; margin-bottom: 8px; font-weight: 700; }
        input, textarea, select {
            width: 100%;
            padding: 12px 13px;
            background: #0f0f0f;
            color: white;
            border: 1px solid #313131;
            border-radius: 10px;
        }
        textarea { min-height: 120px; resize: vertical; }
        .city-box {
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 12px;
            margin-bottom: 12px;
        }
        .option-builder {
            margin-top: 22px;
        }
        .option-builder__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
        }
        .option-builder__head h3 {
            margin: 0 0 6px;
        }
        .option-builder__head .muted {
            margin: 0;
        }
        .option-builder__list {
            display: grid;
            gap: 12px;
        }
        .option-card {
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #111;
        }
        .option-card__summary {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .option-card.is-collapsed .option-card__summary {
            display: flex;
        }
        .option-card.is-collapsed .option-card__form {
            display: none;
        }
        .option-card__summary strong {
            display: block;
            margin-bottom: 4px;
        }
        .option-card__summary span {
            color: var(--muted);
            font-size: 14px;
        }
        .option-card__actions,
        .option-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
        .option-card__footer {
            margin-top: 4px;
        }
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .checkbox-row input { width: auto; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 9px;
            background: #242424;
            color: #f5f5f0;
            font-size: 12px;
            font-weight: 800;
            box-shadow: 0 0 16px rgba(185, 220, 61, .16);
        }
        .status-pill--stock {
            background: #3a1818;
            color: #ffaaaa;
            box-shadow: 0 0 16px rgba(227, 91, 91, .14);
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }
        .summary-box {
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #101010;
        }
        .summary-box strong {
            display: block;
            margin-top: 5px;
            font-size: 18px;
        }
        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            min-height: 38px;
            padding: 8px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #101010;
            color: var(--text);
            font-weight: 800;
            font-size: 14px;
        }
        .page-item.active .page-link {
            border-color: var(--green);
            background: var(--green);
            color: #111;
        }
        .page-item.disabled .page-link {
            cursor: not-allowed;
            opacity: .45;
        }
        img.thumb {
            width: 76px; height: 60px; object-fit: cover;
            border-radius: 8px; background: #222;
        }
        @media (max-width: 760px) {
            .form-grid { grid-template-columns: 1fr; }
            .summary-grid { grid-template-columns: 1fr; }
            table { display: block; overflow-x: auto; }
            .toolbar { align-items: flex-start; flex-direction: column; }
            .option-builder__head,
            .option-card__summary {
                flex-direction: column;
            }
            .option-builder__head,
            .option-card__summary,
            .option-card__actions,
            .option-card__footer {
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="container header">
        <a href="{{ route('admin.products.index') }}" class="brand">Avo<span>Cato</span> Admin</a>
        <nav class="admin-nav" aria-label="Адмін навігація">
            <a href="{{ route('admin.products.index') }}"
               @class(['active' => request()->routeIs('admin.products.*')])>
                Продукти
            </a>
            <a href="{{ route('admin.categories.index') }}"
               @class(['active' => request()->routeIs('admin.categories.*')])>
                Фільтри
            </a>
            <a href="{{ route('admin.orders.index') }}"
               @class(['active' => request()->routeIs('admin.orders.*')])>
                Замовлення
            </a>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="errors">
                <strong>Перевірте дані:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>
</body>
</html>
