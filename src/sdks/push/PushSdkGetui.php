<?php
error_reporting(0);
header("Content-Type: text/html; charset=utf-8");

# 参考文档 http://docs.getui.com/getui/server/php/start/

/**
 *
 * 个推
 * @author ctocode-zhw
 *
 */
class GetuiPushBase
{
	protected $_AppID = 'nRnw8OyxEP6emyddJpcpx4';
	protected $_AppSecret = 'TuEtqLs5K9A17wBFDlbIr';
	protected $_AppKey = '9DyNPdVfmUAEHzdDaFxpO2';
	protected $_MasterSecret = 'mgR2zEUg7r9Uc2jobYQ3H2';
	protected $_HOST = 'http://sdk.open.api.igexin.com/apiex.htm';
	//
	public function bindAlias($alias, $cid)
	{
		$igt = new IGeTui($this->_HOST, $this->_AppKey, $this->_MasterSecret);
		// $bind_alias = $igt->queryAlias ( $this->_AppID, $cid );
		// $bind_cid_ = $igt->queryAliasByCID ( $this->_AppID, $alias );
		$rep = $igt->bindAlias($this->_AppID, $alias, $cid);
		return $rep;
	}
	public function pushAlias($alias_arr, $pushData)
	{
		putenv("gexin_pushList_needDetails=true");
		putenv("gexin_pushList_needAsync=true");
		$igt = new IGeTui($this->_HOST, $this->_AppKey, $this->_MasterSecret);
		// 消息模版：
		// 1.TransmissionTemplate:透传功能模板
		// 2.LinkTemplate:通知打开链接功能模板
		// 3.NotificationTemplate：通知透传功能模板
		// 4.NotyPopLoadTemplate：通知弹框下载功能模板

		// $template = IGtNotyPopLoadTemplateDemo();
		// $template = IGtLinkTemplateDemo();
		$template = $this->IGtNotificationTemplateDemo($pushData);
		// $template = $this->IGtTransmissionTemplateDemo ();
		// 个推信息体
		$message = new IGtListMessage();
		$message->set_isOffline(true); // 是否离线
		$message->set_offlineExpireTime(3600 * 12 * 1000); // 离线时间
		$message->set_data($template); // 设置推送消息类型

		// $message->set_PushNetWorkType(1); //设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
		// $contentId = $igt->getContentId($message);

		$contentId = $igt->getContentId($message, "toList任务别名功能"); // 根据TaskId设置组名，支持下划线，中文，英文，数字
		$targetList = array();
		foreach ($alias_arr as $val) {
			// 接收方1
			$target1 = new IGtTarget();
			$target1->set_appId($this->_AppID);
			// $target1->set_clientId ( CID );
			$target1->set_alias($val);
			$targetList[] = $target1;
		}
		$rep = $igt->pushMessageToList($contentId, $targetList);
		return $rep;
		// var_dump ( $rep );

		// echo ("<br><br>");
	}
	function IGtNotificationTemplateDemo($pushData = array())
	{
		$template = new IGtNotificationTemplate();
		$template->set_appId($this->_AppID); // 应用appid
		$template->set_appkey($this->_AppKey); // 应用appkey
		$template->set_transmissionType(1); // 透传消息类型
		$template->set_transmissionContent($pushData['transmissionContent']); // 透传内容
		$template->set_title($pushData['title']); // 通知栏标题
		$template->set_text($pushData['text']); // 通知栏内容

		// $template->set_logo ( "http://wwww.igetui.com/logo.png" ); // 通知栏logo
		$template->set_isRing(true); // 是否响铃
		$template->set_isVibrate(true); // 是否震动
		$template->set_isClearable(true); // 通知栏是否可清除

		// $template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
		return $template;
	}
	function IGtTransmissionTemplateDemo()
	{
		$template = new IGtTransmissionTemplate();
		$template->set_appId($this->_AppID); // 应用appid
		$template->set_appkey($this->_AppKey); // 应用appkey
		$template->set_transmissionType(1); // 透传消息类型
		$template->set_transmissionContent('{"page_name":"111"}'); // 透传内容

		// $template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
		// APN简单推送
		$apn = new IGtAPNPayload();
		$alertmsg = new SimpleAlertMsg();
		$alertmsg->alertMsg = "abcdefg3";
		$apn->alertMsg = $alertmsg;
		$apn->badge = 2;
		$apn->sound = "";
		$apn->add_customMsg("payload", "payload");
		$apn->add_customMsg("payload", "payload");
		$apn->contentAvailable = 1;
		$apn->category = "ACTIONABLE";
		$template->set_apnInfo($apn);

		// VOIP推送
		// $voip = new VOIPPayload();
		// $voip->setVoIPPayload("新浪");
		// $template->set_apnInfo($voip);

		// 第三方厂商推送透传消息带通知处理
		// $notify = new IGtNotify();
		// // $notify -> set_payload("透传测试内容");
		// $notify -> set_title("透传通知标题");
		// $notify -> set_content("透传通知内容");
		// $notify->set_url("https://www.baidu.com");
		// $notify->set_type(NotifyInfo_Type::_url);
		// $template -> set3rdNotifyInfo($notify);

		// APN高级推送
		$apn = new IGtAPNPayload();
		$alertmsg = new DictionaryAlertMsg();
		$alertmsg->body = "body";
		$alertmsg->actionLocKey = "ActionLockey";
		$alertmsg->locKey = "LocKey";
		$alertmsg->locArgs = array(
			"locargs"
		);
		$alertmsg->launchImage = "launchimage";
		// IOS8.2 支持
		$alertmsg->title = "Title";
		$alertmsg->titleLocKey = "TitleLocKey";
		$alertmsg->titleLocArgs = array(
			"TitleLocArg"
		);

		$apn->alertMsg = $alertmsg;
		$apn->badge = 7;
		$apn->sound = "";
		$apn->add_customMsg("payload", "payload");
		$apn->contentAvailable = 1;
		$apn->category = "ACTIONABLE";
		//
		// // IOS多媒体消息处理
		$media = new IGtMultiMedia();
		$media->set_url("http://docs.getui.com/start/img/pushapp_android.png");
		$media->set_onlywifi(false);
		$media->set_type(MediaType::pic);
		$medias = array();
		$medias[] = $media;
		$apn->set_multiMedias($medias);
		$template->set_apnInfo($apn);
		return $template;
	}
}
