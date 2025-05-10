# DSL対応構成と方針整理

外部DSL（YAML）を中心とし、PHP設定は汎用セットのみを保持する構成に統一します。
以下の構成・フローで、**collection／series／widget（quiz, slider, chart…）** 等の多様なTYPE×KINDを取り扱い、実用的なコンテンツ作成・動作確認を可能にします。

---

## 1. フォルダ構成イメージ

```
modules/ContentModule/
├─ dsl/                                      ← 外部DSL定義管理
│    ├─ definitions/                         ← 各TYPE×KINDごとの定義ファイル (YAML)
│    │    ├─ collection.static.v1.yml
│    │    ├─ collection.dynamic.v1.yml
│    │    ├─ series.manual.v1.yml
│    │    ├─ series.auto.v1.yml
│    │    ├─ widget.quiz.v1.yml
│    │    ├─ widget.slider.v1.yml
│    │    └─ widget.chart.v1.yml
│    └─ schemas/                             ← JSON Schema 等、DSL検証用
│         └─ v1.json
│
├─ src/
│    ├─ Core/                               ← ■ Core層（エンジン）
│    │    ├─ DSL/
│    │    │    ├─ Loader.php               ← YAML読み込み
│    │    │    ├─ Validator.php            ← JSON Schemaによる検証
│    │    │    ├─ Parser.php               ← DSL定義→中間データ変換
│    │    │    └─ Renderer.php             ← フォーム／レンダリング生成
│    │    ├─ Infrastructure/
│    │    │    └─ Config/
│    │    │         └─ meta_schema.php     ← basic/seo/social のみ保持
│    │    └─ Application/Services/
│    │         └─ ContentService.php      ← Strategy解決など汎用処理
│    │
│    └─ Custom/                             ← ■ Custom層（アプリ側公開入口）
│         ├─ Config/
│         │    └─ content.php              ← basicセットのみ保持し mapping は default まで
│         ├─ Providers/
│         │    └─ ContentModuleServiceProvider.php  ← Core+DSL+Custom統合登録
│         └─ Resources/
│              └─ views/                   ← オーバーライド用Blade
└─ README.md
```

---

## 2. PHP設定の最小化方針

### 2.1 meta\_schema.php （fieldsは3汎用セットのみ）

```php
return [
  'sets' => [
    'basic'  => '基本フィールド',
    'seo'    => 'SEO設定用',
    'social' => 'OG/Twitterカード用',
  ],
  'schemas' => [
    'basic'  => [/* 既存basicフィールド */],
    'seo'    => [/* 既存seoフィールド   */],
    'social' => [/* 既存socialフィールド*/],
  ],
  'mapping' => [
    'default' => ['basic'],
  ],
];
```

### 2.2 content.php （TYPE一覧・KINDはDSL定義側へ移行）

```php
return [
  'types'      => [],   // PHP設定から除外
  'kinds'      => [],
  'strategies' => [],
];
```

※ 全TYPE×KINDはdsl/definitions/\*.yml で定義し、Strategy登録もYAMLの `strategy` を元に動的解決します。

---

## 3. 外部DSL（YAML）定義例

### 3.1 Collection (static)

```yaml
# dsl/definitions/collection.static.v1.yml
type: collection
kind: static
strategy: Modules\ContentModule\Samples\Domain\Strategies\Collection\StaticCollectionStrategy
sections:
  - key: collection
    label: コレクション情報
  - key: basic
    label: 基本情報
  - key: seo
    label: SEO設定
  - key: social
    label: SNS共有設定
```

### 3.2 Widget (quiz & slider)

```yaml
# dsl/definitions/widget.quiz.v1.yml
type: widget
kind: quiz
strategy: Modules\ContentModule\Samples\Domain\Strategies\Widget\QuizWidgetStrategy
sections:
  - key: widget
    label: ウィジェット設定
  - key: basic
  - key: seo

# dsl/definitions/widget.slider.v1.yml
type: widget
kind: slider
strategy: Modules\ContentModule\Samples\Domain\Strategies\Widget\SliderWidgetStrategy
sections:
  - key: widget
    label: スライダ設定
  - key: basic
  - key: social
```

---

## 4. 動作フロー

1. **読み込み**

   * CustomServiceProvider → Core DSL Loader で `dsl/definitions/*.yml` を一括ロード
   * Validator → Parser → 中間データとして登録
2. **フォーム生成**

   * Parser 出力の `sections` 情報で Blade テンプレート内にセクション分けしつつ `basic`/`seo`/`social` とTYPE専用フィールドを出力
3. **保存／バリデーション**

   * フォームRequest → DSL定義の `validation` と Strategy.validate() を組み合わせ
4. **レンダリング**

   * FrontController → DSL Renderer が同定DEFをもとに view() レンダリング

---

## 5. マスタ要件・拡張ポイント

* **Series/Collectionのマスタ**: YAML内に `master: { table: series, key: id }` を追加し、Rendererでテーブル生成/API連携可
* **Widget拡張**: `dsl/definitions/widget.*.yml` を増やすだけで quiz/slider/chart...を追加可能

---

このように、**PHP設定は汎用セットだけに絞り、あらゆるTYPE×KINDは完全に外部DSL化**する構成です。
実装の軸が明確化され、追加要件にも柔軟に対応できます。ご確認ください。
