<?php
namespace app\admin\controller;

use app\admin\model\Menu as MenuModel;
use app\admin\model\UserType as UserTypeModel;
use app\admin\validate\UserType as UserTypeValidate;
use think\facade\Config;

/**
 * @project  系统用户角色控制器
 * @author   千叶
 * @date     2018-03-28
 */
class Role extends Base
{
	/**
	 * 角色列表
	 * @param UserTypeModel $userTypeModel
	 * @return mixed|\think\response\Json
	 */
	public function index(UserTypeModel $userTypeModel)
	{
		if ($this->request->isAjax()) {
			$cur_page = input('page', 1, 'intval');
			$keyword = input('keyword', '', 'urldecode');
			$page_size = input('limit', Config::get('page_size'), 'intval');
			$map = $keyword ? "title like '%{$keyword}%' " : '';
			$json = $userTypeModel->getDataByWhere($map, $cur_page, $page_size);
			return json($json);
		} else {
			$page_size = Config::get('page_size');
			return $this->assign('page_size', $page_size)->fetch();
		}
	}

	/**
	 * 创建角色
	 * @param UserTypeModel    $userTypeModel
	 * @param UserTypeValidate $userTypeValidate
	 * @param MenuModel        $menuModel
	 * @return mixed|\think\response\Json
	 */
	public function add(UserTypeModel $userTypeModel, UserTypeValidate $userTypeValidate,
	                    MenuModel $menuModel)
	{
		if ($this->request->isAjax()) {
			$rules = implode(',', input('rules/a'));
			$data = [
				'title' => input('title', ''),
				'status' => input('status', 0, 'intval'),
				'rules' => $rules
			];
			if (!$userTypeValidate->check($data)) {
				return json(['code' => 0, 'msg' => $userTypeValidate->getError()]);
			}
			return json($userTypeModel->insertRole($data));
		} else {
			// 获取系统中所有配置的菜单
			$menuTree = $menuModel->getTree();
			return $this->assign('menuTree', $menuTree)->fetch();
		}
	}
}
