More Pager
===============

Pager to load pages at a time, one after the other.

## Usage ##

```php
$this->widget('zii.widgets.CListView', array(
	'dataProvider' => $dataProvider,
	'itemView' => '_item',
	'id' => 'list-id',
	'pager' => array(
		'class' => 'ext.more-pager.MorePager',
		// Button label
		'label' => Yii::t('app', 'read more'),
		// HTML tag options
		'options' => array(
			'class' => 'button',
		),
		// Prepend loaded items or append if false
		// Defaults to false.
		'prepend' => true,
	),
));
```
