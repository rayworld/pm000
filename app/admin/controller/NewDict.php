<?php

namespace app\admin\controller;

use think\admin\controller;
use think\admin\extend\DataExtend;
use think\facade\Db;

/**
 * 新数据字典管理
 * Class NewDict
 * @package app\admin\controller
 */
class NewDict extends controller {

  /**
   * 当前操作数据库
   * @var string
   */
  private $table = 'SystemDict';

  /**
   * 新数据字典管理
   * @auth true
   * @throws \think\db\exception\DataNotFoundException
   * @throws \think\db\exception\DbException
   * @throws \think\db\exception\ModelNotFoundException
   */
  public function index() {
    $this->title = ' 新数据字典管理';
    $reqpid      = $this->request->get('pid');
    $pid         = $reqpid ? $reqpid : 0;
    $ids         = $this->getAllNextId($pid);
    $query       = $this->_query($this->table);
    $query->where(['id' => $ids])->page(false);
  }

  /**
   * 列表数据处理
   * @param array $data
   */
  protected function _index_page_filter(array &$data) {
    foreach ($data as &$vo) {
      $vo['ids'] = join(',', DataExtend::getArrSubIds($data, $vo['id']));
    }
    $data = DataExtend::arr2table($data);
  }

  /**
   * 获取指定节点的所有子节点ID
   *
   * @param int $id 父节点ID
   * @param array $data
   * @return void
   */
  public function getAllNextId($id, $data = []) {
    $pids = db::name('SystemDict')->where('pid', $id)->column('id');
    if (count($pids) > 0) {
      foreach ($pids as $v) {
        $data[] = $v;
        $data   = $this->getAllNextId($v, $data); //注意写$data 返回给上级
      }
    }
    if (count($data) > 0) {
      return $data;
    } else {
      return false;
    }
  }
}