<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author KF
 */
class ErrorController extends Yaf_Controller_Abstract {

	//从2.1开始, errorAction支持直接通过参数获取异常
//	public function errorAction($exception) {
//		//1. assign to view engine
//		$this->getView()->assign("exception", $exception);
//		//5. render by Yaf
//	}

	public function errorAction(Exception $exception) {

		$error = [
			'code' => $exception->getCode(),
			'message' => $exception->getMessage(),
		];
		switch($exception->getCode()) {

			case BASE_EXCEPTION:
			{
				header("HTTP/1.1 403 Forbidden");
				$error['info'] = '403 Forbidden';
				break;
			}
//            case YAF_ERR_LOADFAILD:
//            case YAF_ERR_LOADFAILD_MODULE:
//            case YAF_ERR_LOADFAILD_CONTROLLER:
//            case YAF_ERR_LOADFAILD_ACTION:
			default:
			{
				//404
                //header("HTTP/1.1 503 Server Error.");
				header("HTTP/1.1 404 Not Found");
				$error['info'] = '404 Not Found';
				break;
			}
		}
		if ($this->getRequest()->isXmlHttpRequest()) {
			Fn::ajaxError($error);
		}
		$this->getView()->assign('err', $error);
		//return false;
	}
}
