<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2021 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: https://gitee.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/ThinkLibrary
// | github 代码仓库：https://github.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace think\admin\service;

use think\admin\extend\DataExtend;
use think\admin\Service;

/**
 * 系统菜单管理服务
 * Class deptService
 * @package app\admin\service
 */
class DeptService extends Service
{

    /**
     * 获取可选菜单节点
     * @return array
     * @throws \ReflectionException
     */
    public function getList(): array
    {
        static $nodes = [];
        if (count($nodes) > 0) return $nodes;
        foreach (NodeService::instance()->getMethods() as $node => $method) {
            //if ($method['isdept']) $nodes[] = ['node' => $node, 'title' => $method['title']];
        }
        return $nodes;
    }

    /**
     * 获取系统菜单树数据
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTree(): array
    {
        $query = $this->app->db->name('SystemDept');
        $query->where(['status' => '1'])->order('sort desc,id asc');
        return $this->_buildData(DataExtend::arr2tree($query->select()->toArray()));
    }

    /**
     * 后台主菜单权限过滤
     * @param array $depts 当前菜单列表
     * @return array
     * @throws \ReflectionException
     */
    private function _buildData(array $depts): array
    {
        $service = AdminService::instance();
        foreach ($depts as $key => &$dept) {
            if (!empty($dept['sub'])) {
                $dept['sub'] = $this->_buildData($dept['sub']);
            }
            if (!empty($dept['sub'])) {
                $dept['url'] = '#';
            } elseif ($dept['url'] === '#') {
                unset($depts[$key]);
            } elseif (preg_match('|^https?://|i', $dept['url'])) {
                if (!!$dept['node'] && !$service->check($dept['node'])) {
                    unset($depts[$key]);
                } elseif ($dept['params']) {
                    $dept['url'] .= (strpos($dept['url'], '?') === false ? '?' : '&') . $dept['params'];
                }
            } elseif (!!$dept['node'] && !$service->check($dept['node'])) {
                unset($depts[$key]);
            } else {
                $node = join('/', array_slice(explode('/', $dept['url']), 0, 3));
                $dept['url'] = url($dept['url'])->build() . ($dept['params'] ? '?' . $dept['params'] : '');
                if (!$service->check($node)) unset($depts[$key]);
            }
        }
        return $depts;
    }
}