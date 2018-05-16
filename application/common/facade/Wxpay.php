<?php
namespace app\common\facade;

use think\Facade;

/**
 * @project  微信支付外观
 * @author   千叶
 * @date     2018-05-14
 */
class Wxpay extends Facade
{
	/**
	 * 获取当前Facade对应类名
	 * @return string
	 */
	protected static function getFacadeClass()
	{
		return '\\app\\common\\service\\Wxpay';
	}

}