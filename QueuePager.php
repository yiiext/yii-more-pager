<?php
/**
 * Class QueuePager
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class QueuePager extends CBasePager
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

	public function init()
	{
		parent::init();

		if (empty($this->listId)) {
			throw new CException('Invalid config. Widget required the list ID.');
		}

		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->getId();
		}
	}

	public function run()
	{
		parent::run();
		$this->registerClientScript();

		$page = $this->getCurrentPage(false);

		if ($page + 1 < $this->getPageCount()) {
			$this->options['data-url'] = $this->createPageUrl($page + 1);
			echo CHtml::htmlButton(
				$this->label ?: Yii::t('yiiext', 'More'),
				$this->options
			);
		}
	}

	protected function registerClientScript()
	{
		$cs = Yii::app()->getClientScript();
		$cs->registerScript(
			__CLASS__ . $this->getId(),
			$this->clientScript(),
			$cs::POS_READY
		);
	}

	protected function clientScript()
	{
		$itemsSelector = CJavaScript::encode('.' . $this->itemsCssClass);
		$listId = CJavaScript::encode($this->listId);
		$listSettingsVar = preg_replace('/[^a-z]/i', '', $this->listId) . 'Settings';
		$method = $this->prepend ? 'prepend' : 'append';

		return <<<JS
var {$listSettingsVar} = $.fn.yiiListView.settings[{$listId}];
$(document).on('click.stepPager', '#' + {$listId} + ' .' + {$listSettingsVar}.pagerClass + ' button', function(e) {
	e.preventDefault();
	var sorterClass = '.' + {$listSettingsVar}.sorterClass;
	var pagerClass = '.' + {$listSettingsVar}.pagerClass;
	$.fn.yiiListView.update({$listId}, {
		url: $(this).data('url'),
		success: function(data) {
			$.each({$listSettingsVar}.ajaxUpdate, function(i, v) {
				v = '#' + v;
				var ctx = $(v, '<div>' + data + '</div>');
				$({$itemsSelector}, v).{$method}($({$itemsSelector}, ctx).html());
				$(sorterClass, v).html($(sorterClass, ctx).html());
				$(pagerClass, v).html($(pagerClass, ctx).html());
			});
			if ({$listSettingsVar}.afterAjaxUpdate != undefined) {
				{$listSettingsVar}.afterAjaxUpdate({$listId}, data);
			}
		}
	});
});
JS;
	}
}
