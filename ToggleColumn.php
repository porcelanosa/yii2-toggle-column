<?php
/**
 * @link https://github.com/porcelanosa/yii2-toggle-column
 * @copyright Copyright (c) 2016 Porcelanosa
 * @license MIT http://opensource.org/licenses/MIT
 *
 * USAGE: 
 */

namespace porcelanosa\yii2togglecolumn;

use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\web\View;
use Yii;

class ToggleColumn extends DataColumn
{
	/**
	 * Toggle action that will be used as the toggle action in your controller
	 * @var string
	 */
	public $action = 'toggle';


	/**
	 * @var string pk field name
	 */
	public $primaryKey = 'primaryKey';

	/**
	 * Whether to use ajax or not
	 * @var bool
	 */
	public $enableAjax = true;

	public function init()
	{
		if ($this->enableAjax) {
			$this->registerJs();
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function renderDataCellContent($model, $key, $index)
	{
		$url = [$this->action, 'id' => $model->{$this->primaryKey}];

		$attribute = $this->attribute;
		$value = $model->$attribute;

		if ($value === null || $value == true) {
			$icon = 'ok';
			$colorStyle = ' style="color:green" ';
			$title = Yii::t('yii', 'Off');
		} else {
			$icon = 'remove';
			$colorStyle = ' style="color:red" ';
			$title = Yii::t('yii', 'On');
		}
		return Html::a(
			'<span class="glyphicon glyphicon-' . $icon . '" ' . $colorStyle . '></span>',
			$url,
			[
				'title' => $title,
				'class' => 'toggle-column',
				'data-method' => 'post',
				'data-pjax' => '0',
			]
		);
	}

	/**
	 * Registers the ajax JS
	 */
	public function registerJs()
	{
		$js = <<<'JS'
$("a.toggle-column").on("click", function(e) {
    e.preventDefault();
    $.post($(this).attr("href"), function(data) {
        var pjaxId = $(e.target).closest(".grid-view").parent().attr("id");
        $.pjax.reload({container:"#" + pjaxId});
    });
    return false;
});
JS;
		$this->grid->view->registerJs($js, View::POS_READY, 'yii-toggle-column');
	}
}
