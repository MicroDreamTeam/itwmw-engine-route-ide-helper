## 说明
此扩展用于生成软擎路由中定义的控制器对应的URI

此扩展将生成如下注释
```php
/**
 * @uri /user/login
 */
```
## 安装
```shell
composer require itwmw/engine-route-ide-helper --dev
```
## 使用
```shell
bin/gerent make:validate-ide [完整命名空间或者完整文件名] --dir [文件目录]
```

## WebStorm集成
文件->设置->工具->外部工具->添加
- 名称：`Route-Ide-Helper`或者随意
- 程序:`bin\gerent` Windows选择bat文件
- 参数:`make:route-ide $FilePath$`
- 工作目录:`$ProjectFileDir$`

## 快捷键
为了更方便的使用，可以给此工具设置一个快捷键

文件->设置->键盘映射->外部工具->Route-Ide-Helper->右键点击->添加键盘快捷键，作者这里是`ALT+G`