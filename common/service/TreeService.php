<?php

namespace common\service;

use common\models\Tree;

/**
 * Class TreeService
 * @package common\service
 */
class TreeService
{
    /**
     * @return array
     */
    public function collectingTree()
    {
        $roots = Tree::find()->roots()->all();

        foreach ($roots as $root) {
            $tree[] = ['text' => $root->name, 'id' => $root->id, 'nodes' => $this->run($root)];
        }

        return $tree;
    }

    /**
     * @param Tree $root
     * @return array
     */
    private function run(Tree $root)
    {
        $children = $root->children()->all();
        $tree = [];

        if (count($children)) {

            foreach ($children as $child) {
                $partOfTree = $this->run($child);
                $tree[] = ['text' => $child->name, 'id' => $child->id, 'nodes' => $partOfTree];
            }
        }

        return $tree;
    }

}
