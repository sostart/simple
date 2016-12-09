一个简单的PHP开发框架

最初起源于一款web游戏,后来用来做了款O2O应用

主要是围绕一个字 懒.. 主要特点是项目继承(类, 配置)
	simple/core 是核心
	applications/base 是项目基础,继承自核心
	applications/extend 是项目扩展,继承自base,可以继续扩展 extend2, 3, admin.. 项目下的类默认继承自base,也可单独指定(项目间可以相互继承)

类名非继承或覆盖整个项目中不可出现重复,所有框架内的类使用\调用 \DB \Storage
框架功能通过组件提供(通过config配置), 组件快速访问通过helper提供(相当于Facade)

\Controller::run();  // MVC模式运行
\Logic::run();       // API模式运行 同是要创建 rule定义接口访问规则 和 logic实现接口逻辑
\AR('user')->find()  // 数据库操作

约定
	所有文件使用UTF-8编码(无BOM)
	类名非继承或覆盖整个项目中不可出现重复,所有框架内的类使用\调用 \DB \Storage
	目录名全小写(view和vendor下的目录除外)
	文件名即类名 区分大小写(vendor第三方类可以不受此约定限制)
	类名驼峰命名(User,UserModel,TestClassName)
	表名列名全小写,下划线分割,主键自增id
	字符串使用单引号

目录
	目录说明一
		applications
			base  基础项目
			admin 管理后台(自定义 的 #扩展目录)
		simple
			core  核心目录
			simple.php 引导文件(从入口文件引用此文件)
		public 可以被外部直接访问的目录
			index.php 定义 APPLICATION_PATH 及 工作目录 APP_NAME(默认为 base), 引入 引导文件(simple.php), \Logic::Run 或 \Controller::Run
			admin.php 定义 工作目录(APP_NAME admin),其它参照index.php
	目录说明二
		核心目录 core
		基础目录 base (可修改,自定义)
		扩展目录 admin,..(自定义,可以和基础目录相同即基础目录就是扩展目录)
	目录说明三
		api        定义API 严格模式下必须定义
		component  组件
		interfaces 组件接口
		controller 控制器 应用逻辑
		data       SQL文件等
		helper     助手类 一般用来便捷调用组件等
		libraries  类库
		logic      API实现
		model      模型 业务逻辑
		rule       API规则
		vendor     第三方类库
		view       视图

类自动载入会先从 扩展项目 找起,然后是 基础项目,然后是 核心项目
目录搜索顺序为 helper,component,logic,controller,model,rule,api

组件
	通过Config->Component配置组件
	如果需要统一的接口,可以定义在component\interfaces下

关于Controller/Logic和Model
	原则上 C/L层放应用逻辑,M层放业务逻辑+数据访问
	当业务逻辑简单 无需复用 快速开发 调试 一些临时工具等 也可直接写在C/L层甚至不用C/L

框架架构
	数据存储层( MySQL MemCache/KVDB Storage )
	数据访问层( AR Cache Storage )
	业务逻辑层( Model )
	应用逻辑层( Logic Controller )
	格式数据层( Response )
	视图逻辑层( View )( WEB应用Ajax请求完成后处理返回的数据也属于视图层 安卓/iOS等请求接口处理返回数据也属于视图层 )
