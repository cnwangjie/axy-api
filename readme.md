axy-api
======
校园外卖项目后端接口

### 开发

###### 首次开发

 0. 克隆这个版本库
 0. 运行 `composer install` 安装依赖
 0. 在本地运行Mysql数据库，并创建一个字符集为 `utf8mb4_unicode_ci` 名字为 `axym` 的数据库
 0. 如果目录里没有.env文件则由.env.example复制一份并编辑好其中的数据库信息
 0. 运行 `php artisan key:generate` 创建一个app key
 0. 运行 `php artisan jwt:secret` 创建一个jwt密钥
 0. 运行 `php artisan migrate` 进行数据库迁移
 0. 运行 `php artisan db:seed` 插入测试数据

###### API文档

采用node开发的apidoc工具生成文档，文档以注释形式写在接口对应的控制器的方法之上
