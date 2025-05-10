<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>管理画面</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- スタイルシート読み込み例 -->
</head>
<body class="bg-gray-100">
  <nav class="bg-white shadow p-4">
    <a href="{{ route('admin.contents.index') }}" class="font-bold">コンテンツ管理</a>
    <!-- 他メニュー -->
  </nav>
  <main class="p-6">
    @yield('content')
  </main>
  @stack('scripts')
</body>
</html>
