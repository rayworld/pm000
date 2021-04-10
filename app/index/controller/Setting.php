<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2021 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/ThinkAdmin
// | github 代码仓库：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\index\controller;

use think\admin\Controller;
use think\admin\service\adminService;
use think\admin\service\MenuService;

/* 合同管理设置
 * Class Index
 * @package app\index\controller
 */
class Setting extends Controller {

/**
 * 设置列表
 * @auth true
 * @menu true
 * @throws \ReflectionException
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\DbException
 * @throws \think\db\exception\ModelNotFoundException
 */
  public function index() {
    /*! 根据运行模式刷新权限 */
    AdminService::instance()->apply($this->app->isDebug());
    /*! 读取当前用户权限菜单树 */
    $this->menus = MenuService::instance()->getTree();
    /*! 判断当前用户的登录状态 */
    $this->login = AdminService::instance()->isLogin();
    /*! 菜单为空且未登录跳转到登录页 */
    if (empty($this->menus) && empty($this->login)) {
      $this->redirect(sysuri('admin/login/index'));
    } else {
      $this->title = '合同管理设置';
      $this->fetch();
    }
  }
}