<?php
namespace Modules\ContentModule\Core\DSL;

use Illuminate\Support\Facades\File;

class DslRegistry
{
    /** @var DslDefinition[] */
    private array $definitions;

    public function __construct(Loader $loader, array $metaSchema)
    {
        $this->definitions = [];
        foreach (config('content.dsl_paths') as $dir) {
            if (! is_dir($dir)) {
                continue;
            }
            foreach ($loader->loadAll($dir, $metaSchema) as $def) {
                $key = "{$def->getType()}.{$def->getKind()}";
                if (! isset($this->definitions[$key])) {
                    $this->definitions[$key] = $def;
                }
            }
        }
        // 重複排除後、数値インデックスの配列に
        $this->definitions = array_values($this->definitions);
    }

    /**
     * TYPE×KIND に対応する DslDefinition を返す
     * @throws \InvalidArgumentException
     */
    public function get(string $type, string $kind): DslDefinition
    {
        $key     = "{$type}.{$kind}";
        $mapping = config('meta_schema.mapping');
        $metaSets = $mapping[$key] ?? $mapping['default'];

        // ① 外部DSL定義があればそれを返す
        foreach ($this->definitions as $def) {
            if ($def->getType() === $type && $def->getKind() === $kind) {
                return $def;
            }
        }

        // ② 外部DSLがなければ、meta_schema.mapping をもとに
        //    ConfigArray → Parser（DslDefinition::fromArray）でマージ
        $configArray = [
            'type'     => $type,
            'kind'     => $kind,
            'strategy' => '',
            'sections' => array_map(
                fn(string $setKey) => ['key' => $setKey],
                $metaSets
            ),
        ];

        return DslDefinition::fromArray($configArray, config('meta_schema'));
    }

    /** @return DslDefinition[] */
    public function all(): array
    {
        return $this->definitions;
    }
}
