<?php

// echo json_encode(array(
// 'title' => '这个是标题',
// 'text' => '这个是描述',
// 'transmissionContent' => json_encode ( array(
// 'type' => 'open_news_detail',
// 'news_id' => '535'
// ), true )
// )) ;
exit();
// 推送
include __DIR__ . '/GetuiPushBase.php';
$GetuiPushBase = new \GetuiPushBase();
$GetuiPushBase->pushAlias(array(
	'getui_630',
	'getui_536'
), array(
	'title' => '这个是标题',
	'text' => '这个是描述',
	'transmissionContent' => json_encode(array(
		'type' => 'open_news_detail',
		'news_id' => '635'
	), true)
));
