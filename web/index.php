<?php
session_start();
header("Content-type: text/html; charset=gb2312");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

require_once './Angular.php';
require_once './Db.php';

/**
 * [GmTools - TLLB GameMaster tools for PHP]
 * @auhor Wigiesen(��������)
 * @code by 2020-9-22 15:03:09
 */
class GmTools
{
	/**
	 * [$loginPassworld ��¼����]
	 */
	public static $loginPassworld = '�Զ����½����';

	/**
	 * [$privateKey ˽��KEY]
	 * [����������¼�ʱ������˽��KEY��Ӧ��������ɹ��¼�]
	 */
	private static $privateKey = '�Զ���ӿ�����KEY';

	/**
	 * [$dbConfig GM�������ݿ�����]
	 */
	private static $dbConfig = [
		'hostname' 	=> '���ݿ�IP',
		'dbname'   	=> '���ݿ�����',
		'username' 	=> '���ݿ��û�',
		'password' 	=> '���ݿ�����',
		'prefix'   	=> '',
		'charset'  	=> 'gbk'
	];

	/**
	 * [$webDbConfig �˺����ݿ�����]
	 */
	private static $webDbConfig = [
		'hostname' 	=> '���ݿ�IP',
		'dbname'   	=> '���ݿ�����',
		'username' 	=> '���ݿ��û�',
		'password' 	=> '���ݿ�����',
		'prefix'   	=> '',
		'charset'  	=> 'gbk'
	];

	/**
	 * [$tlbbDbConfig ��Ϸ���ݿ�����]
	 */
	private static $tlbbDbConfig = [
		'hostname' 	=> '���ݿ�IP',
		'dbname'   	=> '���ݿ�����',
		'username' 	=> '���ݿ��û�',
		'password' 	=> '���ݿ�����',
		'prefix'   	=> '',
		'charset'  	=> 'gbk'
	];

	/**
	 * [$templateConfig ��̨ϵͳģ��������]
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
	 * [$DB ��ʼ���˺����ݿ����]
	 */
	private static $webDB = null;

	/**
	 * [$DB ��ʼ����Ϸ���ݿ����]
	 */
	private static $tlbbDB = null;

	/**
	 * [$DB ��ʼ��GM�������ݿ�����]
	 */
	private static $DB = null;

	/**
	 * [__construct ��ʼ����ʱʵ�������ݿ�]
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
	 * [addEvent ���ִ���¼�]
	 * @param   [arr]  $data  [�¼�����]
	 * @return  [json]        [��������¼��Ľ��]
	 */
	public static function addEvent($data){
		$res = self::$DB->create('eventlist',$data);
		return $res >= 1 
		? self::return_json('success', 'addEvent Success') 
		: self::return_json('fail', 'addEvent Fail!');
	}

	/**
	 * [fetchAllEvents ��ȡ�����¼��б�]
	 */
	public static function fetchAllEvents(){
		$res = self::$DB->fetchAll('eventlist',['order' => ['id', 'desc']]);
		return $res;
	}

	/**
	 * [fetchLastEvent ��ȡ����һ��δִ���¼�]
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
	 * [AddGamePoint ��ֵ��Ϸ����]
	 */
	public static function AddGamePoint($userAccount, $point){		
		$oldPoint = self::$webDB->getColumn('account', 'point', [
			'where'  => [
				['name', '=' , $userAccount.'@game.sohu.com']
			]	
		]);
		// ��ӵ���
		self::$webDB->update('account',['point' => $oldPoint + $point],[
			'where' => [
				['name', '=', $userAccount.'@game.sohu.com']
			]
		]);
		// ��ӷ�ִ���¼�
		$addEvent = self::addEvent([
			'event'			=> 'AddGamePoint',
			'eventnote'		=> '��ֵ����',
			'createtime'	=> time(),
			'requesttime' 	=> time(),
			'status'		=> 1,
			'param1'		=> iconv('utf-8', 'gb2312', $userAccount.'@game.sohu.com'),
			'param2'		=> iconv('utf-8', 'gb2312', $point),
			'param3'		=> "�����ˣ�{$_SESSION['user']}",
			'param4'		=> 0,
		]);
		echo $addEvent;
	}

	/**
	 * [delAllEvents ����¼���־]
	 */
	public static function delAllEvents(){
		$res = self::$DB->delete('eventlist',['where' => [['status', '>=', 0]]]);
		return $res >= 1 
		? self::return_json('success', 'delete AllEvents Success') 
		: self::return_json('fail', 'delete AllEvents Fail!');
	}

	/**
	 * [return_json ����json����]
	 * @param   [str]$status   [����״̬]
	 * @param   [str]$message  [������Ϣ]
	 * @param   [arr]$data     [����,����еĻ�]
	 * @return  [json]         [json]
	 */
	private static function return_json($status, $message, $data = []){
		return json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
	}

}



/**
 *====================================================
 *====================================================
 * ҵ�����̿�ʼ
 *====================================================
 *====================================================
 */

// ��ʼ�� GmTools
$GmTools = new GmTools;

// ����ץȡ����
if (isset($_GET['privateKey']) && !empty($_GET['privateKey'])) {
	// ��ȡ����δִ���¼�
	$privateKey = $_GET['privateKey'];
	$res = GmTools::fetchLastEvent($privateKey);
	echo $res;
	exit;
}else{
	$view = new Angular(GmTools::$templateConfig);
	if (!$_SESSION['user']) {
		// ��¼����
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
			// ��ץȡ�¼�
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
		// ��ҳ
		$eventsList = [
			'������'=> 'SendGlobalNews',
			'��ֵ����'=> 'AddGamePoint',
			'����Ʒ'=> 'GivePlayerItem',
			'��������ȼ�' => 'SetPlayerLevel',
			'��Ԫ��' => 'GivePlayerYuanBao',
		];
		$res = GmTools::fetchAllEvents();
		$view->assign('eventsList', $eventsList);
		$view->assign('list', $res);
		$view->display('index');
	}
}