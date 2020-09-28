# TLBB-GM在线管理工具

## 工具说明

本工具是由`PHP+Lua`编写的一套`TLBB GM在线管理工具`，本工具以非常巧妙的思路实现了几个基础的TLBB在线管理功能，GM可以通过`电脑PC端网页`、`手机端网页`就可以实时对游戏内相关功能、玩家做出不同功能的设定，过程中`GM无需登陆游戏`，`玩家也无需下线等待`，没错，这一切的操作都是`实时`进行的。当然有能力的朋友可以自行二次开发完善。

## 部署说明

### 1.部署条件
需要自行搭建可`外部访问的WEB服务器`，推荐`Nginx+PHP+MySQL`组合，确保可以`正常执行PHP`与`数据库连接`。

### 2.在线管理工具配置
1.配置`index.php`
```php
$loginPassworld #你自定义的登陆密码

$privateKey #TLBB服务端请求数据时需要验证的KEY，这里也需要自定义

$dbConfig #在线管理工具数据库，配置看第三部

$webDbConfig #TLBB账号数据库

$tlbbDbConfig #TLBB角色数据库
```

### 3.在线管理工具数据库配置
1.创建数据库,数据库名自定义,编码格式**GBK**

2.导入数据表
```sql
CREATE TABLE `eventlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(255) NOT NULL COMMENT '事件标识',
  `eventnote` varchar(255) NOT NULL COMMENT '事件说明',
  `createtime` int(11) NOT NULL COMMENT '事件创建时间',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '执行状态',
  `requesttime` int(11) unsigned DEFAULT '0' COMMENT '事件请求回执时间',
  `param1` varchar(255) DEFAULT NULL COMMENT '参数1',
  `param2` varchar(255) DEFAULT NULL COMMENT '参数2',
  `param3` varchar(255) DEFAULT NULL COMMENT '参数3',
  `param4` varchar(255) DEFAULT NULL COMMENT '参数4',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=gbk;
```

