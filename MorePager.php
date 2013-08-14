<?php
/**
 * Class MorePager
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @version 0.1
 */
class MorePager extends CBasePager
{
	/**
	 * @var string
	 */
	public $label;

	/**
	 * @var array
	 */
	public $options = array();

	/**
	 * @var bool
	 */
	public $prepend = false;

	/**
	 * @var string
	 */
	public $listId;

	/**
	 * @var string
	 */
	public $itemsCssClass = 'items';

	/**
	 * @var string
	 */
	public $pagerCssClass = 'pager';

	public function __construct($owner = null)
	{
		if ($owner instanceof CBaseListView) {
			$this->listId = $owner->id;
			$this->itemsCssClass = $owner->itemsCssClass;
			$this->pagerCssClass = $owner->pagerCssClass;
		}

		parent::__construct($owner);
	}

	public function init()
	{
		parent::init();

		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->getId();
		}
	}

	public function run()
	{
		parent::run();

		if (empty($this->listId)) {
			throw new CException('Invalid list ID.');
		}

		$page = $this->getCurrentPage(false);

		if ($page + 1 < $this->getPageCount()) {
			$this->registerClientScript();

			echo CHtml::link(
				$this->label ?: Yii::t('yiiext', 'More'),
				$this->createPageUrl($page + 1),
				$this->options
			);
		}
	}

	protected function registerClientScript()
	{
		Yii::app()->getClientScript()->registerScript(
			__CLASS__ . $this->getId(),
			$this->clientScript(),
			CClientScript::POS_READY
		);
	}

	protected function clientScript()
	{
		$itemsSelector = CJavaScript::encode('.' . $this->itemsCssClass);
		$pagerSelector = CJavaScript::encode('.' . $this->pagerCssClass);
		$listId = CJavaScript::encode($this->listId);
		$method = $this->prepend ? 'prepend' : 'append';

		return <<<JS
$(document).on('click.morePager', '#' + {$listId} + ' ' + {$pagerSelector} + ' a', function(e) {
	e.preventDefault();
	$.fn.yiiListView.update({$listId}, {
		url: this.href,
		success: function(data) {
			$.each($.fn.yiiListView.settings[{$listId}].ajaxUpdate, function(i, v) {
				v = '#' + v;
				var ctx = $(v, '<div>' + data + '</div>');
				$({$itemsSelector}, v).{$method}($({$itemsSelector}, ctx).html());
				$({$pagerSelector}, v).html($({$pagerSelector}, ctx).html());
			});
			if ($.fn.yiiListView.settings[{$listId}].afterAjaxUpdate != undefined) {
				$.fn.yiiListView.settings[{$listId}].afterAjaxUpdate({$listId}, data);
			}
		}
	});
});
JS;
	}
}
