<?php
namespace Modules\ContentModule\Core\DSL;

use Symfony\Component\Yaml\Yaml;

class Parser
{
    private array $dsl;
    private array $meta;

    /**
     * @param array|string $dslOrFile YAMLファイルパスまたはDSL設定配列
     * @param array $metaSchema config('content.meta_schema')
     */
    public function __construct(array|string $dslOrFile, array $metaSchema)
    {
        if (is_array($dslOrFile)) {
            // 既にパース済みのDSL定義を受け取る
            $this->dsl = $dslOrFile;
        } else {
            // ファイルパスからYAMLをパース
            $this->dsl = Yaml::parseFile($dslOrFile);
        }
        $this->meta = $metaSchema;
    }

    /**
     * セクションごとにフィールドをマージして返す
     * @return array<array{key:string,label:string,fields:array}>
     */
    public function parse(): array
    {
        // meta_schema に定義されている共通セクションキー一覧
        $commonKeys = array_keys($this->meta['schemas']);

        $sections = [];
        foreach ($this->dsl['sections'] as $sectionConfig) {
            $key   = $sectionConfig['key'];
            $label = $sectionConfig['label'] ?? ($this->meta['sets'][$key] ?? $key);

            // 共通セクションは PHP 側定義の fields、そうでなければ YAML 側 fields
            $fields = in_array($key, $commonKeys, true)
                ? $this->meta['schemas'][$key]['fields']
                : ($sectionConfig['fields'] ?? []);

            // ここで全プロパティをそのまま渡す
            // （必要に応じて 'label' が無い場合のみデフォルト補完などはできます）
            $sections[] = [
                'key'    => $key,
                'label'  => $label,
                'fields' => array_map(function(array $f) {
                    // 最低限 name, type は必須としつつ、
                    // 他プロパティはそのままマージして返す
                    return array_merge([
                        'name'  => $f['name'] ?? null,
                        'type'  => $f['type'] ?? 'string',
                    ], $f);
                }, $fields),
            ];
        }

        return $sections;
    }
}
