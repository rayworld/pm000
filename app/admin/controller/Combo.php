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

namespace app\admin\controller;

use think\admin\Controller;
use app\admin\model\DeptModel;
use think\facade\Db;
/**
 * 系统用户管理
 * Class User
 * @package app\admin\controller
 */
class Combo extends Controller
{
    /**
     * 绑定数据表
     * @var string
     */
    private $table = 'SystemUser';
    private $table_SystemLog = 'system_oplog';

    /**
     * 系统用户管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {

        $this->title = '系统用户管理';

        $query = $this->_query($this->table)
            ->alias('a')
            ->field('a.*, d.title')
            ->join('system_dept d','a.deptid=d.id');

        // 列表排序并显示
        $query->order('sort desc,id desc')->page();
    }

    /**
     * 系统用户列表
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function List()
    {
        //取得当前页码
        $page=request()->get('page');
        $limit = request()->get('limit');
        $where = request()->get('where');
        //取得每页条数
        $limit = $limit ? $limit :10; //分页个数
        $page = $page ? $page : 1; //当前页
        $start = ($page-1)*$limit;
        
        if($where)
        {
            //取得总记录数
            $count = Db::table($this->table_SystemLog)->select();
            //查询出的记录
            $query = Db::table($this->table_SystemLog)-> limit($start,$limit)->select(); 
        }
        else
        {
            //取得总记录数
            $count = Db::table($this->table_SystemLog)->select();
            //查询出的记录
            $query = Db::table($this->table_SystemLog)-> limit($start,$limit)->select(); 
        }
        //定义返回值
        $res['data'] = $query;
        $res['msg'] = '';
        $res['code'] = 0;
        $res['count'] = count($count);
        echo json_encode($res); 
    }

    /**
     * 编辑系统用户
     * @auth true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        //$DeptLogic = new DeptLogic();
        $depts = DeptModel::Select();
        $this->assign('depts',$depts);
        $this->_applyFormToken();
        $this->_form($this->table, 'form');
    }
}