<?php
namespace app\admin\controller;

use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\admin\service\AdminService;

/**
 * 数据字典管理
 * Class Dict
 * @package app\admin\controller
 */
class Dict extends controller{

    /**
     * 当前操作数据库
     * @var string
     */
    private $table = 'SystemDict';

    /**
     * 数据字典管理
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '数据字典管理';
        $this->_page($this->table, false);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_page_filter(array &$data)
    {
        foreach ($data as &$vo) {
            $vo['ids'] = join(',', DataExtend::getArrSubIds($data, $vo['id']));
        }
        $data = DataExtend::arr2table($data);
    }

    /**
     * 添加数据字典
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 表单数据处理
     * @param array $vo
     * @throws \ReflectionException
     */
    protected function _form_filter(array &$vo)
    {
        if ($this->request->isGet()) {
            /* 清理权限节点 */
            if ($this->app->isDebug()) {
                AdminService::instance()->clearCache();
            }
            
            /* 选择自己的上级菜单 */
            $vo['pid'] = $vo['pid'] ?? input('pid', '0');

            /* 列出可选上级菜单 */
            $dicts = $this->app->db->name($this->table)->order('sort desc,id asc')->column('id,pid,title,icon', 'id');
            $this->dicts = DataExtend::arr2table(array_merge($dicts, [['id' => '0', 'pid' => '-1', 'title' => '顶级数据字典']]));
            if (isset($vo['id'])) foreach ($this->dicts as $dict) if ($dict['id'] === $vo['id']) $vo = $dict;
            foreach ($this->dicts as $key => $dict) if ($dict['spt'] >= 4) unset($this->dicts[$key]);
            if (isset($vo['spt']) && isset($vo['spc']) && in_array($vo['spt'], [1, 2]) && $vo['spc'] > 0) {
                foreach ($this->dicts as $key => $dict) if ($vo['spt'] <= $dict['spt']) unset($this->dicts[$key]);
            }
        }
    }

    /**
     * 表单结果处理
     * @param bool $result
     */
    protected function _add_form_result(bool $result)
    {
        if ($result) {
            $id = $this->app->db->name($this->table)->getLastInsID();
            sysoplog('数据字典管理', "添加数据字典[{$id}]成功");
        }
    }

        /**
     * 编辑数据字典
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }

    /**
     * 编辑成功后刷新页面
     * @param bool $state
     */
    protected function _form_result(bool $state)
    {
        if ($state) {
            $this->success('数据字典修改成功！', 'javascript:location.reload()');
        }
    }

    /**
     * 删除数据字典
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function remove()
    {
        $this->_applyFormToken();
        $this->_delete($this->table);
    }
    
    /**
     * 删除结果处理
     * @param boolean $result
     */
    protected function _remove_delete_result(bool $result)
    {
        if ($result) {
            $id = input('id') ?: 0;
            sysoplog('数据字典管理', "删除数据字典[{$id}]成功");
        }
    }

    /**
     * 修改数据字典状态
     * @auth true
     * @throws \think\db\exception\DbException
     */
    public function state()
    {
        $this->_applyFormToken();
        $this->_save($this->table, $this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 状态结果处理
     * @param boolean $result
     */
    protected function _state_save_result(bool $result)
    {
        if ($result) {
            [$id, $state] = [input('id'), input('status')];
            sysoplog('数据字典管理', ($state ? '激活' : '禁用') . "数据字典[{$id}]成功");
        }
    }

}