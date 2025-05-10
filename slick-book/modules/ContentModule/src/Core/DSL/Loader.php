<?php

namespace Modules\ContentModule\Core\DSL;

use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\File;

class Loader
{
    /**
     * 指定ディレクトリ配下の YAML を全てパースし DslDefinition を返す
     * @param string $dir モジュール内DSL定義ディレクトリ
     * @param array $metaSchema config('content.meta_schema')
     * @return DslDefinition[]
     */
    public function loadAll(string $dir, array $metaSchema): array
    {
        $definitions = [];
        // ディレクトリが存在しない場合は空
        if (! File::isDirectory($dir)) {
            return $definitions;
        }

        foreach (File::glob(rtrim($dir, '/') . '/*.yml') as $file) {
            // YAML を配列として読み込む
            $config = Yaml::parseFile($file);
            if (!empty($config)) {
                // 配列から DslDefinition インスタンスを生成
                $definitions[] = DslDefinition::fromArray($config, $metaSchema);
            }
        }

        return $definitions;
    }
}
