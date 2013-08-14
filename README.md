Queue Pager
===============

Pager to load pages at a time, one after the other.

## Usage ##

```php
$this->widget('zii.widgets.CListView', array(
	'dataProvider' => $dataProvider,
	'itemView' => '_item',
	'id' => 'list-id',
	'pager' => array(
		'class' => 'ext.yii-queue-pager.QueuePager',
		'options' => array(
			'class' => 'button',
		),
		// List id required, it is copy from list widget.
		'listId' => 'list-id',
	),
));
```