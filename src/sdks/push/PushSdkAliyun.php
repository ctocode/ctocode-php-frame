<?php
/*
 *
 */

function ctoAliyunAppPush($config = '', $setData = '', $kvData = '')
{
    // 设置你自己的AccessKeyId/AccessSecret/AppKey/地区id
    $accessKeyId = $config['push_appkey'];
    $accessKeySecret = $config['push_appsecret'];
    $regionId = $config['push_regionid'];
    $appKey = $config['push_key'];

    $iClientProfile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
    $client = new DefaultAcsClient($iClientProfile);
    $request = new Push\PushRequest();

    // 推送目标
    $request->setAppKey($appKey);

    // php注释： 推送目标: DEVICE:推送给设备;ALIAS : 按别名推送 ACCOUNT:推送给指定帐号,TAG:推送给自定义标签; ALL: 广播推送
    // java注释：推送目标: DEVICE:按设备推送 ALIAS : 按别名推送 ACCOUNT:按帐号推送 TAG:按标签推送; ALL: 广播推送
    $request->setTarget($setData['set_target']);

    // 根据Target来设定，如Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
    $request->setTargetValue($setData['set_target_value']);

    $request->setDeviceType($setData['set_device_type']); // 设备类型 ANDROID iOS ALL.
    $request->setPushType($setData['set_push_type']); // 消息类型 MESSAGE NOTICE
    $request->setTitle($setData['set_title']); // 消息的标题
    $request->setBody($setData['set_body']); // 消息的内容

    if ($setData['set_device_type'] == 'iOS') {
        // 推送配置: iOS

        // iOS应用图标右上角角标
        // $request->setiOSBadge ( 1 );

        // 是否开启静默通知
        $request->setiOSSilentNotification("false");

        // iOS通知声音
        $request->setiOSMusic("default");

        // iOS的通知是通过APNs中心来发送的，需要填写对应的环境信息。"DEV" : 表示开发环境 "PRODUCT" : 表示生产环境
        $request->setiOSApnsEnv("DEV");

        // 推送时设备不在线（既与移动推送的服务端的长连接通道不通），则这条推送会做为通知，通过苹果的APNs通道送达一次(发送通知时,Summary为通知的内容,Message不起作用)。注意：离线消息转通知仅适用于生产环境
        $request->setiOSRemind("false");

        // iOS消息转通知时使用的iOS通知内容，仅当iOSApnsEnv=PRODUCT && iOSRemind为true时有效
        // $request->setiOSRemindBody("iOSRemindBody");

        // 自定义的kv结构,开发者扩展用 针对iOS设备
        if (!empty($kvData)) {
            // $request->setiOSExtParameters ( "{\"k1\":\"ios\",\"k2\":\"v2\"}" );
            $request->setiOSExtParameters(json_encode($kvData, true));
        }
    } elseif ($setData['set_device_type'] == 'ANDROID') {
        // 推送配置: Android

        // 通知的提醒方式 "VIBRATE" : 震动 "SOUND" : 声音 "BOTH" : 声音和震动 NONE : 静音
        $request->setAndroidNotifyType("NONE");

        // 通知栏自定义样式0-100
        $request->setAndroidNotificationBarType(1);

        // 点击通知后动作 "APPLICATION" : 打开应用 "ACTIVITY" : 打开AndroidActivity "URL" : 打开URL "NONE" : 无跳转
        $request->setAndroidOpenType("ACTIVITY");

        // Android收到推送后打开对应的url,仅当AndroidOpenType="URL"有效
        //$request->setAndroidOpenUrl ( "http://www.aliyun.com" );

        // 设定通知打开的activity，仅当AndroidOpenType="Activity"有效
        //$request->setAndroidActivity ( "com.alibaba.push2.demo.XiaoMiPushActivity" );

        $request->setAndroidNotificationChannel(1);
        // Android通知音乐
        $request->setAndroidMusic("default");

        $request->setAndroidPopupActivity("com.hrhg.schoolbusiness.push.AliPushReceiverActivity"); //设置该参数后启动辅助托管弹窗功能, 此处指定通知点击后跳转的Activity（辅助弹窗的前提条件：1. 集成第三方辅助通道；2. StoreOffline参数设为true
        $request->setAndroidPopupTitle($setData['set_title']);
        $request->setAndroidPopupBody($setData['set_body']);
        // $request->setAndroidExtParameters("{\"k1\":\"android\",\"k2\":\"v2\"}"); // 设定android类型设备通知的扩展属性

        // 设置该参数后启动小米托管弹窗功能, 此处指定通知点击后跳转的Activity（托管弹窗的前提条件：1. 集成小米辅助通道；2. StoreOffline参数设为true
        //$request->setAndroidXiaoMiActivity ( "com.ali.demo.MiActivity" );
        //$request->setAndroidXiaoMiNotifyTitle ( $setData['set_title'] );
        //$request->setAndroidXiaoMiNotifyBody ( $setData['set_body'] );
        // 设定android类型设备通知的扩展属性
        if (!empty($kvData)) {
            // $request->setAndroidExtParameters ( "{\"k1\":\"android\",\"k2\":\"v2\"}" );
            //$request->setAndroidExtParameters ( json_encode ( $kvData, true ) );
            $request->setAndroidExtParameters(json_encode($kvData, true));
        }
    }

    // 推送控制
    $pushTime = gmdate('Y-m-d\TH:i:s\Z', strtotime('+3 second')); // 延迟3秒发送
    $request->setPushTime($pushTime);
    $expireTime = gmdate('Y-m-d\TH:i:s\Z', strtotime('+1 day')); // 设置失效时间为1天
    $request->setExpireTime($expireTime);
    $request->setStoreOffline("true"); // 离线消息是否保存,若保存, 在推送时候，用户即使不在线，下一次上线则会收到

    try {
        // 开始推送
        $response = $client->getAcsResponse($request);
    } catch (Exception $e) {
        return array(
            'status' => 404,
            'msg' => $e->getMessage()
        );
    }
    return array(
        'status' => 200
    );
}
