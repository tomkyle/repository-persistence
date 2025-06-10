<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Yaml\Yaml;

class FrontmatterFilePersistence extends FilePersistenceDecoratorAbstract
{
    /**
     * @var array<string,mixed>
     */
    public array $frontmatter = [];

    /**
     * @var int
     */
    public $yaml_options = Yaml::DUMP_OBJECT_AS_MAP | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE;

    /**
     * @param FilePersistence $persistence Inner persistence
     * @param array<string,mixed> $frontmatter
     */
    public function __construct(FilePersistence $persistence, array $frontmatter = [])
    {
        parent::__construct($persistence);
        $this->setFrontmatter($frontmatter);
    }


    /**
     * @return array<string,mixed>
     */
    public function getFrontmatter(): array
    {
        return $this->frontmatter;
    }


    /**
     * @param array<string,mixed> $frontmatter Frontmatter
     */
    public function setFrontmatter(array $frontmatter): self
    {
        $this->frontmatter = $frontmatter;
        return $this;
    }


    /**
     * @inheritDoc
     *
     * @param mixed $store Optional: Store frontmatter in variable passed by reference
     */
    public function read(string|int $id, &$store = null): array
    {
        $file_path = $this->persistence->getFilePath($id);
        $data = $this->readFromFile($file_path, $store);

        return $this->persistence->decode($data);
    }



    /**
     * Looks up files in basedir of inner persistence
     * and sends them to read() method.
     *
     * @inheritDoc
     */
    public function readAll(): array
    {
        $ext = $this->persistence->getExtension();
        $ext = '.' . ltrim($ext, '.');

        $base_dir = $this->persistence->getBaseDir();

        $files = glob($base_dir . '/*' . $ext) ?: [];

        $id_array = array_map(static fn($file) => basename($file, $ext), $files);

        $all_read = array_map(fn(string|int $id, &$store = null): array => $this->read($id, $store), $id_array);
        return array_filter($all_read);
    }


    /**
     * @inheritDoc
     * @param mixed $store Optional: Store frontmatter in variable passed by reference
     */
    public function readFromFile(string $file_path, &$store = null): string
    {
        $content = $this->persistence->readFromFile($file_path);
        $object = YamlFrontMatter::parse($content);

        $matter = $object->matter();
        $this->setFrontmatter($matter);
        $store = $matter;
        return trim($object->body());
    }

    /**
     * @inheritDoc
     * @param array<string,mixed> $custom Merge frontmatter data
     */
    public function writeToFile(string $file_path, \Stringable|string $file_content, array $custom = []): bool
    {
        $yaml = Yaml::dump(array_merge($this->frontmatter, $custom), flags: $this->yaml_options);
        $frontmatter = implode(PHP_EOL, ['---', $yaml, '---', PHP_EOL]);

        if ($file_content instanceof \Stringable) {
            $file_content = $file_content->__toString();
        }

        return $this->persistence->writeToFile($file_path, $frontmatter . $file_content);
    }

}
