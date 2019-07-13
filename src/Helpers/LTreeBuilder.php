<?php

declare(strict_types=1);

namespace Umbrellio\LTree\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Umbrellio\LTree\Exceptions\LTreeReflectionException;
use Umbrellio\LTree\Exceptions\LTreeUndefinedNodeException;

class LTreeBuilder
{
    private $pathField;
    private $idField;
    private $parentIdField;
    private $nodes = [];
    private $root = null;

    public function __construct(
        string $pathField = 'path',
        string $idField = 'id',
        string $parentIdField = 'parent_id'
    ) {
        $this->pathField = $pathField;
        $this->idField = $idField;
        $this->parentIdField = $parentIdField;
    }

    public function build(Collection $items): LTreeNode
    {
        $items = $items->sortBy($this->pathField);
        $this->root = new LTreeNode();

        foreach ($items as $item) {
            $node = new LTreeNode($item);

            [$id, $parentId] = $this->getNodeIds($item);

            $parentNode = $this->getNode($parentId);
            $parentNode->addChild($node);

            $this->nodes[$id] = $node;
        }
        return $this->root;
    }

    private function getNodeIds($item): array
    {
        $parentId = $item->{$this->parentIdField};
        $id = $item->{$this->idField};

        if ($id === $parentId) {
            throw new LTreeReflectionException($id);
        }
        return [$id, $parentId];
    }

    private function getNode(?int $id): LTreeNode
    {
        if ($id === null) {
            return $this->root;
        }
        if (!isset($this->nodes[$id])) {
            throw new LTreeUndefinedNodeException($id);
        }
        return $this->nodes[$id];
    }
}
