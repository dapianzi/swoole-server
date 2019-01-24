<?php
/**
 * @name SamplePlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author KF
 */
class SamplePlugin extends Yaf\Plugin_Abstract {

	public function routerStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {

	}

	public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
		//$router = Yaf_Dispatcher::getInstance()->getCurrentRoute();
		//var_dump($router);
		//var_dump(getCurrentRoute()); //在路由结束以后, 获取起作用的路由协议
		//var_dump(getRoute());
		//var_dump(getRoutes());
	}

	public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {

	}

	public function preDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {

	}

	public function postDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}

	public function dispatchLoopShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {

	}
}
