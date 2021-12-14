<?php

use kartik\tree\TreeView;
use common\models\Tree;

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="category-index">
    <?php

    echo TreeView::widget([
        'query'             => Tree::find()->addOrderBy('root, lft'),
        'headingOptions'    => ['label' => 'Categories'],
        'isAdmin'           => true,
        'displayValue'      => 1,
        'softDelete'      => true,
        //'cacheSettings'   => ['enableCache' => true]
    ]);

    ?>
</div>
