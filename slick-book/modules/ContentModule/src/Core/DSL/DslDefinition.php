<?php

namespace Modules\ContentModule\Core\DSL;

/**
 * DSL定義を表すオブジェクト
 */
class DslDefinition
{
    private string $type;
    private string $kind;
    private string $strategy;
    private array $sections;

    /**
     * @param array $sections セクション情報の配列
     */
    public function __construct(string $type, string $kind, string $strategy, array $sections)
    {
        $this->type      = $type;
        $this->kind      = $kind;
        $this->strategy  = $strategy;
        $this->sections  = $sections;
    }

    /**
     * 配列定義から DslDefinition を生成
     */
    public static function fromArray(array $config, array $metaSchema): self
    {
        // Parser を利用してセクションをマージ
        $parser   = new Parser($config, $metaSchema);
        $sections = $parser->parse();
        return new self(
            $config['type'],
            $config['kind'],
            $config['strategy'],
            $sections
        );
    }

    public function getType(): string
    {
        return $this->type;
    }
    public function getKind(): string
    {
        return $this->kind;
    }
    public function getStrategy(): string
    {
        return $this->strategy;
    }
    /** @return array<array{key:string,label:string,fields:array}> */
    public function getSections(): array
    {
        return $this->sections;
    }
}
