<?php

namespace ctocode\lang;

class ResponseCodeZh
{
	public static $statusData = [
		100 => '继续中',
		101 => '交换协议',
		200 => '请求成功', // OK [GET]：服务器成功返回用户请求的数据，该操作是幂等的（Idempotent）。',
		201 => '操作成功', // CREATED - [POST/PUT/PATCH]：用户新建或修改数据成功。',
		202 => '认可的', // - [*]：表示一个请求已经进入后台排队（异步任务）',
		203 => '非授权信息',
		204 => '没有内容', // - [DELETE]：用户删除数据成功。',
		205 => '重置内容',
		206 => '部分内容',
		300 => '多项选择',
		301 => '永久移除',
		302 => '找到',
		303 => '查看其他',
		304 => '没有改变',
		305 => '使用代理',
		306 => '未使用',
		307 => '临时重定向',
		400 => '请求无效',
		400 => '无效请求', // [POST/PUT/PATCH]：用户发出的请求有错误，服务器没有进行新建或修改数据的操作，该操作是幂等的。',
		401 => '未经授权', // - [*]：表示用户没有权限（令牌、用户名、密码错误）。',// 401: "由于长时间未操作，登录已超时，请重新登录",
		402 => '支付请求',
		403 => '拒绝访问', // Forbidden - [*] 表示用户得到授权（与401错误相对），但是访问是被禁止的。',
		405 => '未授权的Method',
		404 => '求地址出错', // NOT FOUND - [*]：用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的。',
		406 => '请求资源不可访问', // - [GET]：用户请求的格式不可得（比如用户请求JSON格式，但是只有XML格式）。
		407 => '需要代理验证',
		408 => '请求超时',
		409 => '不一致',
		410 => '不复存在', // -[GET]：用户请求的资源被永久删除，且不会再得到的。
		411 => '所需长度',
		412 => '预处理失败',
		413 => '请求实体过大',
		414 => '请求URI太长',
		415 => '不支持的媒体类型',
		416 => '请求范围不满足',
		417 => '执行失败',
		422 => '验证错误', // - [POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误。',
		500 => '服务器内部错误', // [*]：服务器发生错误，用户将无法判断发出的请求是否成功。',
		501 => '服务未实现',
		502 => '网关错误',
		503 => '服务不可用',
		504 => '网关超时',
		505 => 'HTTP版本不受支持'
	];
}
