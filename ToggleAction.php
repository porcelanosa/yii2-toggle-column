<?php
/**
 * @link https://github.com/porcelanosa/yii2-toggle-column
 * @copyright Copyright (c) 2016 Porcelanosa
 * @license MIT http://opensource.org/licenses/MIT
 */

namespace porcelanosa\yii2togglecolumn;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\MethodNotAllowedHttpException;

class ToggleAction extends Action
{
	/**
	 * @var string name of the model
	 */
	public $modelClass;

	/**
	 * @var string model attribute
	 */
	public $attribute = 'active';

	/**
	 * @var string scenario model
	 */
	public $scenario = null;

	/**
	 * @var string|array additional condition for loading the model
	 */
	public $andWhere;

	/**
	 * @var string|int|boolean what to set active models to
	 */
	public $onValue = 1;

	/**
	 * @var string|int|boolean what to set inactive models to
	 */
	public $offValue = 0;

	/**
	 * @var bool whether to set flash messages or not
	 */
	public $setFlash = false;

	/**
	 * @var string flash message on success
	 */
	public $flashSuccess = "Model saved";

	/**
	 * @var string flash message on error
	 */
	public $flashError = "Error saving Model";

	/**
	 * @var string|array URL to redirect to
	 */
	public $redirect;

	/**
	 * @var string pk field name
	 */
	public $primaryKey = 'id';

	/**
	 * Run the action
	 * @param $id integer id of model to be loaded
	 *
	 * @throws \yii\web\MethodNotAllowedHttpException
	 * @throws \yii\base\InvalidConfigException
	 * @return mixed
	 */
	public function run($id)
	{
		if (!Yii::$app->request->getIsPost()) {
			throw new MethodNotAllowedHttpException();
		}
		$id = (int)$id;
		$result = null;

		if (empty($this->modelClass) || !class_exists($this->modelClass)) {
			throw new InvalidConfigException("Model class doesn't exist");
		}
		/* @var $modelClass \yii\db\ActiveRecord */
		$modelClass = $this->modelClass; //  текущая модель


		$attribute = $this->attribute;
		$model = $modelClass::find()->where([$this->primaryKey => $id]);

		/* если есть дополнительное условие */
		if (!empty($this->andWhere)) {
			$model->andWhere($this->andWhere);
		}

		/*получение записи*/
		$model = $model->one();

		// если есть сценарий - добавляем к модели сценарий
		if (!is_null($this->scenario)) {
			$model->scenario = $this->scenario;
		}

		if (!$model->hasAttribute($this->attribute)) { //  ошибка в конфигурации - не указан (или неправильно указан) атрибут (имя поля)
			throw new InvalidConfigException("Attribute ".$this->attribute." doesn't exist");
		}

		/* переключаем значение */
		if ($model->$attribute == $this->onValue) {
			$model->$attribute = $this->offValue;
		} else {
			$model->$attribute = $this->onValue;
		}

		/*  сохраняем модель */
		if ($model->save(false , [$this->attribute])) {
			if ($this->setFlash) {
				Yii::$app->session->setFlash('success', $this->flashSuccess);
			}
		} else {
			if ($this->setFlash) {
				Yii::$app->session->setFlash('error', $this->flashError);
			}
		}
		if (Yii::$app->request->getIsAjax()) {
			Yii::$app->end();
		}
		/* @var $controller \yii\web\Controller */
		$controller = $this->controller;
		if (!empty($this->redirect)) {
			return $controller->redirect($this->redirect);
		}
		return $controller->redirect(Yii::$app->request->getReferrer());
	}
}
