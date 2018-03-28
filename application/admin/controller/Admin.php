<?php
namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use app\admin\model\UserType as UserTypeModel;
use app\admin\validate\Admin as AdminValidate;
use think\facade\Config;

/**
 * @project  系统用户管理控制器
 * @author   千叶
 * @date     2018-03-27
 */
class Admin extends Base
{
	/**
	 * 系统用户列表
	 * @param AdminModel $adminModel
	 * @return mixed|\think\response\Json
	 */
	public function index(AdminModel $adminModel)
	{
		if ($this->request->isAjax()) {
			$cur_page = input('page', 1, 'intval');
			$keyword = input('keyword', '', 'urldecode');
			$page_size = input('limit', Config::get('page_size'), 'intval');
			$map = $keyword ? "real_name like '%{$keyword}%' " : '';
			$json = $adminModel->getDataByWhere($map, $cur_page, $page_size);
			return json($json);
		} else {
			$page_size = Config::get('page_size');
			return $this->assign('page_size', $page_size)->fetch();
		}
	}

	/**
	 * 创建系统用户，并分配角色
	 * @param AdminModel    $adminModel
	 * @param AdminValidate $adminValidate
	 * @param UserTypeModel $userTypeModel
	 * @return mixed|\think\response\Json
	 */
	public function add(AdminModel $adminModel, AdminValidate $adminValidate, UserTypeModel $userTypeModel)
	{
		if ($this->request->isAjax()) {
			$data = [
				'username' => input('username', ''),
				'real_name' => input('real_name', ''),
				'group_id' => input('group_id/a', ''),
				'status' => input('status', 0, 'intval'),
			];
			if (!$adminValidate->check($data)) {
				return json(['code' => 0, 'msg' => $adminValidate->getError()]);
			}
			$result = $adminModel->createSystemUser($data);
			return json($result);
		} else {
			$this->assign('roles', $userTypeModel->getAllSystemRoles());
			return $this->fetch();
		}
	}

	/**
	 * 系统用户修改
	 * @param AdminModel    $adminModel
	 * @param AdminValidate $adminValidate
	 * @param UserTypeModel $userTypeModel
	 * @return mixed|\think\response\Json
	 */
	public function edit(AdminModel $adminModel, AdminValidate $adminValidate, UserTypeModel $userTypeModel)
	{
		$id = input('param.id', 0, 'intval');
		if ($this->request->isAjax()) {
			$data = [
				'username' => input('username', ''),
				'real_name' => input('real_name', ''),
				'group_id' => input('group_id/a', ''),
				'status' => input('status', 0, 'intval'),
			];
			if (!$adminValidate->scene('edit')->check($data)) {
				return json(['code' => 0, 'msg' => $adminValidate->getError()]);
			}
			return json($adminModel->editSystemUser($id, $data));
		} else {
			$user = $adminModel->getUserById($id);
			$roles = $userTypeModel->getAllSystemRoles();
			if ($user === null || $roles === null) {
				$this->redirect(url('system/mistake'));
			}
			$this->assign(['user' => $user, 'roles' => $roles]);
			return $this->fetch('admin/add');
		}
	}

	/**
	 * 重置系统用户密码
	 * @param AdminModel $adminModel
	 * @return \think\response\Json
	 */
	public function resetpwd(AdminModel $adminModel)
	{
		$id = input('param.id', 0, 'intval');
		return json($adminModel->resetpwd($id));
	}

	/**
	 * 修改系统用户状态（开启或者禁用）
	 * @param AdminModel $adminModel
	 * @return \think\response\Json
	 */
	public function change_status(AdminModel $adminModel)
	{
		$id = input('param.id', 0, 'intval');
		$status = input('param.status', 1, 'intval');
		return json($adminModel->change_status($id, $status));
	}
}
