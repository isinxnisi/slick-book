@extends('content-module::layouts.front')

@section('title', $entity->getMetaValue('meta_title') ?: $entity->getTitle())

@section('content')
<article class="prose lg:prose-xl mx-auto py-8">
    <h1>{{ $entity->getTitle() }}</h1>

    {{-- メタ情報例 --}}
    @if($desc = $entity->getMetaValue('meta_description'))
        <p class="text-gray-500">{{ $desc }}</p>
    @endif

    {{-- 本文 --}}
    <div>{!! $entity->getBody() !!}</div>

    {{-- カスタムフィールド例（ギャラリーウィジェット埋め込み） --}}
    @if(method_exists($strategy, 'render'))
        {!! $strategy->render($entity) !!}
    @endif

    {{-- 任意でコメント欄やSNSシェアなど --}}
</article>
@endsection
