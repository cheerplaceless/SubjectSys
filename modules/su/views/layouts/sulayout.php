<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    $session = Yii::$app->session;
    $session->open();
    NavBar::begin([
        'brandLabel' => 'Selecting Subjects System',
        'brandUrl' => Url::toRoute('index/index'),
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            [
                'label' => 'Logout (' . $_SESSION['yii']['name'] . ')',
                'url' => ['/index/logout'],
//                    'linkOptions' => ['data-method' => 'post']
            ],
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <div style="margin-top: 5%;padding: 1px">
            <div style="float: left;width: 25%">
                <div class="list-group">
                    <a href="<?= Url::toRoute('index/index') ?>" target="_self" class="list-group-item active">
                        <h4 class="list-group-item-heading">
                            功能
                        </h4>
                    </a>
                    <a href="<?= Url::toRoute('index/editmsg') ?>" target="_self" class="list-group-item">
                        <h4 class="list-group-item-heading">
                            编辑信息界面
                        </h4>
                        <p class="list-group-item-text">
                            学生登录后的信息展示界面
                        </p>
                    </a>
                    <a href="<?= Url::toRoute('index/teachercurd') ?>" target="_self" class="list-group-item">
                        <h4 class="list-group-item-heading">
                            老师增删改
                        </h4>
                        <p class="list-group-item-text">
                            老师信息的CURD
                        </p>
                    </a>
                    <a href="<?= Url::toRoute('index/importstudent') ?>" target="_self" class="list-group-item">
                        <h4 class="list-group-item-heading">
                            导入学生
                        </h4>
                        <p class="list-group-item-text">
                            通过Excel导入学生
                        </p>
                    </a>
                    <a href="<?= Url::toRoute('index/deletedata') ?>" target="_self" class="list-group-item">
                        <h4 class="list-group-item-heading">
                            删除数据
                        </h4>
                        <p class="list-group-item-text">
                            删除老师,学生数据
                        </p>
                    </a>

                </div>
            </div>

            <div class="right" style="float: right;width: 75%;height: 500px;padding: 10px">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= $content ?>
            </div>

        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
