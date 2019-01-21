<?php
/**
 * @name Bootstrap
 * @author KF
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _initConfig() {
		//把配置保存起来
		$arrConfig = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $arrConfig);
	}

//	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
//		//注册一个插件
//		$objSamplePlugin = new SamplePlugin();
//		$dispatcher->registerPlugin($objSamplePlugin);
//	}
//
//	public function _initRoute(Yaf_Dispatcher $dispatcher) {
//		//var_dump(); //在路由结束以后, 获取起作用的路由协议
//		//var_dump(getRoute());
//		//var_dump(getRoutes());
//		//在这里注册自己的路由协议,默认使用简单路由
//		//$router = Yaf_Dispatcher::getInstance()->getRouter();
//		/**
//		 * 添加配置中的路由
//		 */
//		//$router->addConfig(array());
//	}
//

	public function _initFunction(Yaf_Dispatcher $dispatcher){
		Yaf_Loader::import('Fn.php');
	}

	public function _initSmarty(Yaf_Dispatcher $dispatcher){
		$smarty = new Smarty_Adapter(null , Yaf_Application::app()->getConfig()->smarty);
		try {
			Yaf_Dispatcher::getInstance()->setView($smarty);
		} catch (SmartyException $e) {
			throw new Yaf_Exception_LoadFailed_View($e->getMessage());
		}
	}
}
