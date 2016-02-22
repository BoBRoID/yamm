<?php
namespace bobroid\yamm;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @author Nikolai Gilko
 * @since 1.0
 */
class Yamm extends Widget
{
	/**
	 * @var array|string логотип, отображаемый в левой части меню
	 * если array:
	 *          content - логотип (ссылка на изображение), или текст
	 *          link    - ссылка на логотипе (по умолчанию '#')
	 */
	public $logo = [];

	public $items = [];

	public $options = [];

	public $theme;
	/**
	 * @var array the dropdown widget options
	 */
	public $dropdownOptions = [];

	/**
	 * @var string the caret indicator to display for dropdowns
	 */
	public $dropdownIndicator = ' <span class="caret"></span>';



	protected $defaultMenuLabel = 'Меню';
	protected $defaultSearchLabel = 'Поиск';

	/**
	 * @inheritdoc
	 */
	public function init(){
		$this->options['headerOptions'] = isset($this->options['headerOptions']) ? $this->options['headerOptions'] : [];

		$this->options['headerOptions']['class'] = isset($this->options['headerOptions']['class']) ? 'cd-main-header ' . $this->options['headerOptions']['class'] : 'cd-main-header';

		$this->options['menuLabel'] = isset($this->options['menuLabel']) ? $this->options['menuLabel'] : $this->defaultMenuLabel;
		$this->options['searchLabel'] = isset($this->options['searchLabel']) ? $this->options['searchLabel'] : $this->defaultSearchLabel;

		\bobroid\yamm\YammAsset::register($this->getView());

		if(isset($this->theme) && !empty($this->theme)){
			$p = '\bobroid\yamm\\'.$this->theme.'ThemeAsset';
			$p::register($this->getView());
		}

		$this->findActive();
	}

	public function findActive(){
		foreach($this->items as $key => $item){
			if(isset($item['url']) && !empty($item['url']) && $item['url'] == \Yii::$app->request->url){
				if(!isset($item['options'])){
					$this->items[$key]['options'] = [];
					$item = $this->items[$key];
				}

				if(!isset($item['options']['class'])){
					$this->items[$key]['options']['class'] = 'active';
				}else{
					$this->items[$key]['options']['class'] = 'active '.$this->items[$key]['options']['class'];
				}

				return;
			}
		}
	}

	public static function begin($config = []){
		$tthis = parent::begin($config);

		echo Html::tag('header', $tthis->renderLogo().Html::tag('ul', Html::tag('li', Html::tag('a', $tthis->options['menuLabel'].Html::tag('span'), [
				'class' =>  'cd-nav-trigger',
				'href'  =>  '#cd-primary-nav'
			])), [
				'class' =>  'cd-header-buttons'
			]), $tthis->options['headerOptions']), '<main class="cd-main-content">';
	}

	public static function end(){
		echo '</main>';
		return parent::end();
	}

	/**
	 * @inheritdoc
	 */
	protected function isChildActive($items, &$active)
	{

	}

	protected function renderLogo(){
		$content = $link = '';

		if(is_array($this->logo) && !empty($this->logo)){
			$content = $this->logo['content'];
			$link = $this->logo['link'];
		}else{
			if(empty($this->logo)){
				return;
			}

			$content = $this->logo;
		}

		$link = empty($link) ? '#' : $link;

		if(preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $content)){
			$content = Html::tag('img', '', [
				'src'   =>  $content
			]);
		}

		return Html::tag('a', $content, [
			'class' =>  'cd-logo',
			'href'  =>  $link
		]);
	}

	/**
	 *
	 */
	public function renderSearch($configuration){
		return Html::tag('div', \kartik\typeahead\Typeahead::widget($configuration), [
			'class' =>  'search-inline',
			'id'    =>  'cd-search'
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function renderItem($item, $parent = [])
	{
		$dropdown = false;
		$options = [];

		if(isset($item['type'])){
			switch($item['type']){
				case 'search':
					return Html::tag('li', $this->renderSearch($item['pluginOptions']), $item['options']);
					break;
			}
		}

		if(isset($item['items']) && !empty($item['items'])){
			$dropdown = true;
			$options = [
				'class' =>  'has-children'
			];
		}

		if(isset($item['counter'])){
			$item['label'] = isset($item['label']) ? $item['label'] : '';
			$item['label'] = $item['label'].Html::tag('span', $item['counter'], [
					'class' =>  'counter'
				]);
		}

		$r = Html::a(!isset($item['label']) ? $item : $item['label'], isset($item['url']) ? $item['url'] : '#');

		if($dropdown){
			$subitems = '';

			if(empty($parent)){
				if(!isset($item['options'])){
					$item['options']['class'] = 'cd-secondary-nav is-hidden';
				}else{
					$item['options']['class'] .= isset($item['options']['type']) ? $item['options']['type'].' is-hidden' : 'cd-secondary-nav is-hidden';
				}
				$subitems .= Html::tag('li', Html::tag('a', \Yii::t('shop', 'Меню'), [
					'href'  =>  '#0'
				]), [
					'class' =>  'go-back'
				]);
			}else{
				if(!isset($item['options'])){
					$item['options']['class'] = 'is-hidden';
				}else{
					$item['options']['class'] = isset($item['options']['class']) ? $item['options']['class'].' is-hidden' : 'is-hidden';
				}

				$subitems .= Html::tag('li', Html::tag('a', isset($parent['label']) ? $parent['label'] : '', [
					'href'  =>  '#0'
				]), [
					'class' =>  'go-back'
				]);
			}

			foreach($item['items'] as $sItem){
				$subitems .= $this->renderItem($sItem, $item);
			}

			$r .= Html::tag('ul', $subitems, $item['options']);
		}

		$options = isset($item['options']) ? array_merge($item['options'], $options) : $options;

		$r = Html::tag('li', $r, $options);

		return $r;
	}

	protected function renderMenu(){
		$items = '';

		foreach($this->items as $item){
			$items .= $this->renderItem($item);
		}

		$overlay = Html::tag('div', '', [
			'class' =>  'cd-overlay'
		]);

		$nav = Html::tag('nav', Html::tag('ul', $items, [
			'class' =>  'cd-primary-nav is-fixed',
			'id'    =>  'cd-primary-nav'
		]), [
			'class' =>  'cd-nav'
		]);

		return $overlay.$nav;
	}

	public function run(){
		return self::renderMenu();
	}
}
