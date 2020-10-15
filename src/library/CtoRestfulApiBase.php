<?php

namespace ctocode\library;

/**
 * restful - api 接口风格基类
 * @author ctocode-zhw
 * @link
 */

/**
 * REST的全称是REpresentational State Transfer，表示表述性无状态传输，无需session，所以每次请求都得带上身份认证信息。
 * rest是基于http协议的，也是无状态的。只是一种架构方式，所以它的安全特性都需我们自己实现，没有现成的。
 * 建议所有的请求都通过https协议发送。REST ful web services 概念的核心就是“资源”。
 * 资源可以用 URI 来表示。
 * 客户端使用 HTTP 协议定义的方法来发送请求到这些 URIs，当然可能会导致这些被访问的”资源“状态的改变。HTTP请求对应关系如下：
 *
 */

// ========== ===================== ========================
// HTTP 方法 行为 示例
// ========== ===================== ========================

// GET 用来获取资源， http://xx.com/api/orders
// GET 获取某个特定资源的信息 http://xx.com/api/orders/123
// POST 用来新建资源（也可以用于更新资源）， http://xx.com/api/orders
// PUT 用来更新资源，http://xx.com/api/orders/123
// DELETE 用来删除资源。 http://xx.com/api/orders/123
//
// API的身份认证应该使用OAuth 2.0框架
// 服务器返回的数据格式，应该尽量使用JSON，避免使用XML
// 对于请求的数据一般用json或者xml形式来表示，推荐使用json。
class CtoRestfulApiBase
{
	/**
	 * 一、协议
	 * API与用户的通信协议，总是使用HTTPs协议。
	 */
	/**
	 * 二、域名
	 * 应该尽量将API部署在专用域名之下。
	 * https://api.example.com
	 * 如果确定API很简单，不会有进一步扩展，可以考虑放在主域名下。
	 * https://example.org/api/
	 */
	/**
	 * 三、版本（Versioning）
	 * 应该将API的版本号放入URL。
	 * https://api.example.com/v1/
	 * 另一种做法是，将版本号放在HTTP头信息中，但不如放入URL方便和直观。Github采用这种做法。
	 */
	/**
	 * 四、路径（Endpoint）
	 * 路径又称"终点"（endpoint），表示API的具体网址。
	 * 在RESTful架构中，每个网址代表一种资源（resource），所以网址中不能有动词，只能有名词，而且所用的名词往往与数据库的表格名对应。
	 * 一般来说，数据库中的表都是同种记录的"集合"（collection），所以API中的名词也应该使用复数。
	 * 举例来说，有一个API提供动物园（zoo）的信息，还包括各种动物和雇员的信息，则它的路径应该设计成下面这样。
	 *
	 * https://api.example.com/v1/zoos
	 * https://api.example.com/v1/animals
	 * https://api.example.com/v1/employees
	 *
	 */

	/**
	 * 五、HTTP动词,允许的请求方式
	 * 对于资源的具体操作类型，由HTTP动词表示。
	 * 常用的HTTP动词有下面五个（括号里是对应的SQL命令）。
	 */
	protected $httpMethod = array(
		'GET' => ' （SELECT）：从服务器取出资源（一项或多项）。',
		'POST' => ' （CREATE）：在服务器新建一个资源。',
		'PUT' => ' （UPDATE）：在服务器更新资源（客户端提供改变后的完整资源）。',
		'PATCH' => ' （UPDATE）：在服务器更新资源（客户端提供改变的属性）。',
		'DELETE' => ' （DELETE）：从服务器删除资源。',
		/*还有两个不常用的HTTP动词*/
		'HEAD' => ' 获取资源的元数据。',
		'OPTIONS' => ' 获取信息，关于资源的哪些属性是客户端可以改变的。'
	);
	/**
	 * 七、状态码（Status Codes）
	 * 服务器向用户返回的状态码和提示信息，常见的有以下一些（方括号中是该状态码对应的HTTP动词）。
	 * @var array
	 * 状态码的完全列表参见这里。
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
	 */
	protected $statusCodes = array();
	protected function getHttpStatusMessage($statusCode)
	{
		$httpStatus = $this->statusCodes;
		return ($httpStatus[$statusCode]) ? $httpStatus[$statusCode] : $httpStatus[500];
	}
	/**
	 * 六、过滤信息（Filtering）
	 *
	 * 如果记录数量很多，服务器不可能都将它们返回给用户。API应该提供参数，过滤返回结果。
	 * 下面是一些常见的参数。
	 */
	protected function filtering()
	{
		// ?limit=10：指定返回记录的数量
		// ?offset=10：指定返回记录的开始位置。
		// ?page=2&per_page=100：指定第几页，以及每页的记录数。
		// ?sortby=name&order=asc：指定返回结果按照哪个属性排序，以及排序顺序。
		// ?animal_type_id=1：指定筛选条件

		// 参数的设计允许存在冗余，即允许API路径和URL参数偶尔有重复。
		// 比如，GET /zoo/ID/animals 与 GET /animals?zoo_id=ID 的含义是相同的。
	}
	/**
	 * 八、错误处理（Error handling）
	 * 如果状态码是4xx，就应该向用户返回出错信息。一般来说，返回的信息中将error作为键名，出错信息作为键值即可。
	 */
	protected function returnError()
	{
		$error = array();
		$error['error'] = "Invalid API key";
		// {"error":"get from image source failed: E403"}
	}
	/**
	 * 九、返回结果
	 * 针对不同操作，服务器向用户返回的结果应该符合以下规范。
	 */
	protected function returnResult()
	{
		// GET /collection：返回资源对象的列表（数组）
		// GET /collection/resource：返回单个资源对象
		// POST /collection：返回新生成的资源对象
		// PUT /collection/resource：返回完整的资源对象
		// PATCH /collection/resource：返回完整的资源对象
		// DELETE /collection/resource：返回一个空文档
	}
	/**
	 * 十、Hypermedia API
	 * @remarks
	 * RESTful API最好做到Hypermedia，即返回结果中提供链接，连向其他API方法，使得用户不查文档，也知道下一步应该做什么。
	 * 比如，当用户向 api.example.com 的根目录发出请求，会得到这样一个文档。
	 * * ----------
	 * Hypermedia API的设计被称为HATEOAS。
	 * Github的API就是这种设计，访问api.github.com会得到一个所有可用API的网址列表。
	 */
	protected function Hypermedia()
	{
		// 下面代码表示，文档中有一个link属性，用户读取这个属性就知道下一步该调用什么API了。
		$link = array();
		$link['rel'] = "collection https://www.example.com/zoos"; // rel表示这个API与当前网址的关系（collection关系，并给出该collection的网址）
		$link['href'] = "https://api.example.com/zoos"; // href表示API的路径
		$link['title'] = "List of zoos"; // title表示API的标题
		$link['type'] = "application/vnd.yourformat+json"; // type表示返回类型
		return $link;
	}
}