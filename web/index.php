<?php
session_start();
header("Content-type: text/html; charset=gb2312");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

require_once './Angular.php';
require_once './Db.php';

/**
 * [GmTools - TLLB GameMaster tools for PHP]
 * @auhor Wigiesen(心语难诉)
 * @code by 2020-9-22 15:03:09
 */
class GmTools
{
	/**
	 * [$loginPassworld 登录密码]
	 */
	public static $loginPassworld = '自定义登陆密码';

	/**
	 * [$privateKey 私密KEY]
	 * [服务端请求事件时必须与私密KEY对应才能请求成功事件]
	 */
	private static $privateKey = '自定义接口请求KEY';

	/**
	 * [$dbConfig GM工具数据库配置]
	 */
	private static $dbConfig = [
		'hostname' 	=> '数据库IP',
		'dbname'   	=> '数据库名称',
		'username' 	=> '数据库用户',
		'password' 	=> '数据库密码',
		'prefix'   	=> '',
		'charset'  	=> 'gbk'
	];

	/**
	 * [$webDbConfig 账号数据库配置]
	 */
	private static $webDbConfig = [
		'hostname' 	=> '数据库IP',
		'dbname'   	=> '数据库名称',
		'username' 	=> '数据库用户',
		'password' 	=> '数据库密码',
		'prefix'   	=> '',
		'charset'  	=> 'gbk'
	];

	/**
	 * [$tlbbDbConfig 游戏数据库配置]
	 */
	private static $tlbbDbConfig = [
		'hostname' 	=> '数据库IP',
		'dbname'   	=> '数据库名称',
		'username' 	=> '数据库用户',
		'password' 	=> '数据库密码',
		'prefix'   	=> '',
		'charset'  	=> 'gbk'
	];

	/**
	 * [$templateConfig 后台系统模板配置项]
	 */
	public static $templateConfig = [
	    'debug'            => true,
	    'tpl_path'         => './templates/',
	    'tpl_suffix'       => '.html',
	    'tpl_cache_path'   => './templates/cache/',
	    'tpl_cache_suffix' => '.php',
	    'attr'             => 'php-',
	    'max_tag'          => 10000,
	];

	/**
	 * [$DB 初始化账号数据库变量]
	 */
	private static $webDB = null;

	/**
	 * [$DB 初始化游戏数据库变量]
	 */
	private static $tlbbDB = null;

	/**
	 * [$DB 初始化GM工具数据库配置]
	 */
	private static $DB = null;

	/**
	 * [__construct 初始化类时实例化数据库]
	 */
	public function __construct(){
        if(!(self::$DB instanceof Db)){
            self::$DB = Db::getInstance(self::$dbConfig);
		}
		if(!(self::$webDB instanceof Db)){
            self::$webDB = Db::getInstance(self::$webDbConfig);
        }
	}

	/**
	 * [addEvent 添加执行事件]
	 * @param   [arr]  $data  [事件参数]
	 * @return  [json]        [反馈添加事件的结果]
	 */
	public static function addEvent($data){
		$res = self::$DB->create('eventlist',$data);
		return $res >= 1 
		? self::return_json('success', 'addEvent Success') 
		: self::return_json('fail', 'addEvent Fail!');
	}

	/**
	 * [fetchAllEvents 获取所有事件列表]
	 */
	public static function fetchAllEvents(){
		$res = self::$DB->fetchAll('eventlist',['order' => ['id', 'desc']]);
		return $res;
	}

	/**
	 * [fetchLastEvent 获取最新一条未执行事件]
	 */
	public static function fetchLastEvent($privateKey){
		if ($privateKey != self::$privateKey) {
			return '';
		}
		$res = self::$DB->fetch('eventlist',[
			'where'  => [
				['status', '=' , 0]
			],
			'order' => ['id', 'desc']
		]);
		self::$DB->update('eventlist',['status' => '1','requesttime' => time()],[
			'where' => [['id', '=', $res['id']]]
		]);
		if (!empty($res)) {
			$res = "{$res['id']},{$res['event']},{$res['param1']},{$res['param2']},{$res['param3']},{$res['param4']}";
		}else{
			$res = '';
		}
		return $res;
	}

	/**
	 * [AddGamePoint 充值游戏点数]
	 */
	public static function AddGamePoint($userAccount, $point){		
		$oldPoint = self::$webDB->getColumn('account', 'point', [
			'where'  => [
				['name', '=' , $userAccount.'@game.sohu.com']
			]	
		]);
		// 添加点数
		self::$webDB->update('account',['point' => $oldPoint + $point],[
			'where' => [
				['name', '=', $userAccount.'@game.sohu.com']
			]
		]);
		// 添加非执行事件
		$addEvent = self::addEvent([
			'event'			=> 'AddGamePoint',
			'eventnote'		=> '充值点数',
			'createtime'	=> time(),
			'requesttime' 	=> time(),
			'status'		=> 1,
			'param1'		=> iconv('utf-8', 'gb2312', $userAccount.'@game.sohu.com'),
			'param2'		=> iconv('utf-8', 'gb2312', $point),
			'param3'		=> "发放人：{$_SESSION['user']}",
			'param4'		=> 0,
		]);
		echo $addEvent;
	}

	/**
	 * [delAllEvents 清空事件日志]
	 */
	public static function delAllEvents(){
		$res = self::$DB->delete('eventlist',['where' => [['status', '>=', 0]]]);
		return $res >= 1 
		? self::return_json('success', 'delete AllEvents Success') 
		: self::return_json('fail', 'delete AllEvents Fail!');
	}

	/**
	 * [return_json 返回json数据]
	 * @param   [str]$status   [返回状态]
	 * @param   [str]$message  [返回信息]
	 * @param   [arr]$data     [数组,如果有的话]
	 * @return  [json]         [json]
	 */
	private static function return_json($status, $message, $data = []){
		return json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
	}

}



/**
 *====================================================
 *====================================================
 * 业务流程开始
 *====================================================
 *====================================================
 */

// 初始化 GmTools
$GmTools = new GmTools;

// 正常抓取流程
if (isset($_GET['privateKey']) && !empty($_GET['privateKey'])) {
	// 获取最新未执行事件
	$privateKey = $_GET['privateKey'];
	$res = GmTools::fetchLastEvent($privateKey);
	echo $res;
	exit;
}else{
	$view = new Angular(GmTools::$templateConfig);
	if (!$_SESSION['user']) {
		// 登录流程
		if (empty($_POST['password'])) {
			$view->display('login');
		}else{
			if ($_POST['password'] == GmTools::$loginPassworld) {
				$_SESSION['user'] = 'Admin';
				echo json_encode(['status' => 'success']);
			}else{
				echo json_encode(['status' => 'fail']);
			}
			exit;
		}
	}else if ($_SESSION['user'] && isset($_GET['r']) && !empty($_GET['r'])) {
		$r = $_GET['r'];
		if ($r == 'addEvent') {
			// 非抓取事件
			if ($_POST['event'] == 'AddGamePoint') {
				$res = GmTools::AddGamePoint($_POST['param1'], $_POST['param2']);
			}else{
				$res = GmTools::addEvent([
					'event'			=> $_POST['event'],
					'eventnote'		=> iconv('utf-8', 'gb2312', $_POST['eventnote']),
					'createtime'	=> time(),
					'status'		=> 0,
					'param1'		=> iconv('utf-8', 'gb2312', $_POST['param1']),
					'param2'		=> iconv('utf-8', 'gb2312', $_POST['param2']),
					'param3'		=> iconv('utf-8', 'gb2312', $_POST['param3']),
					'param4'		=> iconv('utf-8', 'gb2312', $_POST['param4']),
				]);
			}
			echo $res;
			exit;
		}elseif ($r == 'clearAllEvents') {
			$res = GmTools::delAllEvents();
			echo $res;
			exit;
		}elseif ($r == 'quickLogin') {
			unset($_SESSION['user']);
			echo json_encode(['status' => 'success']);
			exit;
		}
	}else{
		// 首页
		$eventsList = [
			'发公告'=> 'SendGlobalNews',
			'充值点数'=> 'AddGamePoint',
			'发物品'=> 'GivePlayerItem',
			'设置人物等级' => 'SetPlayerLevel',
			'发元宝' => 'GivePlayerYuanBao',
		];
		$res = GmTools::fetchAllEvents();
		$view->assign('eventsList', $eventsList);
		$view->assign('list', $res);
		$view->display('index');
	}
}