<?php

namespace console\controllers;

use common\service\TreeService;
use yii\console\Controller;
use yii\helpers\FileHelper;

/**
 * Class JsonParserController
 * @package console\controllers
 */
class TestController extends Controller
{
    public function actionIndex()
    {
        $pathDirectory = '@frontend/uploads/' . 4444 . '/';
        FileHelper::createDirectory($pathDirectory);
    }
}