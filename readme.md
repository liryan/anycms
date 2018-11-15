# 通用CMS管理系统，基于Laravel框架+PHP+MySQL开发

# 运行

git clone https://github.com/liryan/anycms


# 通用CMS管理系统，基于Laravel框架+PHP+MySQL开发

# 下载

`git clone https://gitee.com/canbetter/anycms`

# 初始化安装

1.运行　composer dump

2.修改.env文件中的DB_HOST,DB_USER,DB_PASSWORD配置数据库

3.运行　./artisan install ./database/admlite.sql 倒入数据表,此处的dbname和.env中要保持一直

# Model表示数据表,频道表示这个数据表里的数据归类，一个频道关联这个数据表的一些数据(category)字段
# 要想展示数据表里的数据，必须建立频道绑定到某一个模型，内容管理会出现新建的频道菜单，点击展示对应的数据
# 字段有几个类型，可列表（出现在频道页面里的列表中），可编辑（可以修改，否则后台没有修改的入口），可批量修改（出现在频道页下侧，进行批量修改（主要针对，审核，上架等应用场景）
