# 通用CMS管理系统，基于Laravel框架+PHP+MySQL开发

运行

git clone https://github.com/liryan/anycms

# 初始化安装

1.运行　

`composer dump`

`cp _env .env ` #修改.env文件中的 DB_USER,DB_PASSWORD ,此用户具有创建数据库权限

`./artisan install`

`chown -R xxx:xxx`  storage xxx为php运行身份

2.测试

访问 http://yourhost/admin

# 简要说明

Model表示数据表,频道表示这个数据表里的数据归类，一个频道关联这个数据表的一些数据(category)字段
要想展示数据表里的数据，必须建立频道绑定到某一个模型，内容管理会出现新建的频道菜单，点击展示对应的数据
