<?php
/**
 * @name Bootstrap
 * @author KF
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf\Bootstrap_Abstract{

    public function _initLoader() {
        Yaf\Loader::getInstance()->registerLocalNamespace(['Foo', 'Bar']);
        Yaf\Loader::getInstance()->registerLocalNamespace('PHPMailer');
        Yaf\Loader::getInstance()->registerLocalNamespace('Smarty');
    }

    public function _initConfig() {
		//把配置保存起来
		$arrConfig = Yaf\Application::app()->getConfig();
		Yaf\Registry::set('config', $arrConfig);
        Event::addListener('test', function($uid, $o) {
            echo 'Init Config: <br />';
            echo '    $o->moduleName: '.$o->getModuleName().'<br />';
            echo '    $o->add(): '.$o->add($uid).'<br />';
        });
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

	public function _initFunction(Yaf\Dispatcher $dispatcher){
		Yaf\Loader::import('Fn.php');
		Event::addListener('test', function($uid, $o) {
            echo 'Init Function: <br />';
            echo '    uid: '.$uid.'<br />';
        });
	}

	public function _initSmarty(Yaf\Dispatcher $dispatcher){
		$smarty = new Smarty_Adapter(null , Yaf\Application::app()->getConfig()->smarty);
		try {
			Yaf\Dispatcher::getInstance()->setView($smarty);
		} catch (SmartyException $e) {
			throw new Yaf\Exception\LoadFailed\View($e->getMessage());
		}
	}
}
