# TLBB-GM在线管理工具

## 工具说明

本工具是由`PHP+Lua`编写的一套`TLBB GM在线管理工具`，本工具以非常巧妙的思路实现了几个基础的TLBB在线管理功能，GM可以通过`电脑PC端网页`、`手机端网页`就可以实时对游戏内相关功能、玩家做出不同功能的设定，过程中`GM无需登陆游戏`，`玩家也无需下线等待`，没错，这一切的操作都是`实时`进行的。当然有能力的朋友可以自行二次开发完善。

## 部署说明

### 部署条件
需要自行搭建可`外部访问的WEB服务器`，推荐`Nginx+PHP+MySQL`组合，确保可以`正常执行PHP`与`数据库连接`。

### 在线管理工具配置
1.配置`index.php`中 `$loginPassworld`、`$privateKey`、`$dbConfig`、`$webDbConfig`、`$tlbbDbConfig`
```
**$loginPassworld** 你自定义的登陆密码

**$privateKey** TLBB服务端请求数据时需要验证的KEY，这里也需要自定义

**$dbConfig** 在线管理工具数据库

**$webDbConfig** TLBB账号数据库

**$tlbbDbConfig** TLBB角色数据库
```
