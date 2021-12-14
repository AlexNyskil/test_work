<?php

namespace common\models;

use kartik\tree\Module;
use kartik\tree\TreeView;
use Yii;

/**
 * This is the model class for table "tree".
 *
 * @property int $id
 * @property int $root
 * @property int $lft
 * @property int $rgt
 * @property int $lvl
 * @property string $name
 * @property string $icon
 * @property int $icon_type
 * @property int $active
 * @property int $selected
 * @property int $disabled
 * @property int $readonly
 * @property int $visible
 * @property int $collapsed
 * @property int $movable_u
 * @property int $movable_d
 * @property int $movable_l
 * @property int $movable_r
 * @property int $removable
 * @property int $removable_all
 * @property int $child_allowed
 * @property int $activeOrig
 */
class Tree extends \yii\db\ActiveRecord
{
    use \kartik\tree\models\TreeTrait {
        removeNode as protected removeNodeTree;
    }

    public $nodeRemovalErrors;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tree';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['root', 'lft', 'rgt', 'lvl', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', 'child_allowed'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 60],
            [['icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'root' => 'Root',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'lvl' => 'Lvl',
            'name' => 'Name',
            'icon' => 'Icon',
            'icon_type' => 'Icon Type',
            'active' => 'Active',
            'selected' => 'Selected',
            'disabled' => 'Disabled',
            'readonly' => 'Readonly',
            'visible' => 'Visible',
            'collapsed' => 'Collapsed',
            'movable_u' => 'Movable U',
            'movable_d' => 'Movable D',
            'movable_l' => 'Movable L',
            'movable_r' => 'Movable R',
            'removable' => 'Removable',
            'removable_all' => 'Removable All',
            'child_allowed' => 'Child Allowed',
        ];
    }

    public function removeNode($softDelete = true, $currNode = true)
    {
        $this->removeNodeTree(true, true);
    }

    protected function removeNodeTree($softDelete = true, $currNode = true)
    {
        /**
         * @var Module $module
         * @var \kartik\tree\models\Tree $child
         */
        if ($softDelete) {
            $this->nodeRemovalErrors = [];
            $module = TreeView::module();
            extract($module->dataStructure);
            if ($this->isRemovableAll()) {
                /** @noinspection PhpUndefinedMethodInspection */
                $children = $this->children()->all();
                foreach ($children as $child) {
                    $child->active = 0;
                    if (!$child->save()) {
                        /** @noinspection PhpUndefinedVariableInspection */
                        $this->nodeRemovalErrors[] = [
                            'id' => $child->$keyAttribute,
                            'name' => $child->$nameAttribute,
                            'error' => $child->getFirstErrors(),
                        ];
                    }
                }
            }
            if ($currNode) {
                $this->active = 0;
                if (!$this->save()) {
                    /** @noinspection PhpUndefinedVariableInspection */
                    $this->nodeRemovalErrors[] = [
                        'id' => $this->$keyAttribute,
                        'name' => $this->$nameAttribute,
                        'error' => $this->getFirstErrors(),
                    ];
                    return false;
                }
            }
            return true;
        } else {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->removable_all || $this->isRoot() && $this->children()->count() == 0 ?
                $this->deleteWithChildren() : $this->delete();
        }
    }
}
