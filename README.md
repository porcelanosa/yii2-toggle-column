## Toggle Column component

### Installation


```php
composer require porcelanosa/yii2-toggle-column
```

### Usage

```php
 <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        *['class' => 'yii\grid\SerialColumn'],

        'id',
        [
            'class' => 'app\modules\admin\components\columns\ToggleColumn',
            'attribute' => 'active',
            // Uncomment if  you don't want AJAX
            'enableAjax' => true,
            'contentOptions' => ['style' => 'width:50px;']
        ],
        ['class' => ActionColumn::className()],
    ],
    ]); ?>
```

![Screenshot](https://s32.postimg.org/nbcmfc4g3/image.jpg)
