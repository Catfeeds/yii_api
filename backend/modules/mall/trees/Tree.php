<?php
namespace backend\modules\mall\trees;


class Tree
{
    // 生成树型结构所需要的二维数组
    public $arr = array();
    //存储最后返回结果
    public $ret = "";
    // 当前结构数组信息
    public $pid;
 // 当前栏目父类id
    public $cid;
 // 当前栏目id
    public $name;
 // 当前栏目名称
                  
    // 生成树型结构所需修饰符号，可以换成图片
    public $icon = array( '│','├','└'  );
 // 栏目分隔符
    public $nbsp = '　';
 // 分隔符 上级栏目与本级栏目之间
    
    /**
     * 构造函数，初始化类
     * 
     * @param
     *            array 二维数组，例如：
     *            array(
     *            1 => array('cid'=>'1','parentid'=>0,'name'=>'一级栏目一'),
     *            2 => array('cid'=>'2','parentid'=>0,'name'=>'一级栏目二'),
     *            3 => array('cid'=>'3','parentid'=>1,'name'=>'二级栏目一'),
     *            4 => array('cid'=>'4','parentid'=>1,'name'=>'二级栏目二'),
     *            5 => array('cid'=>'5','parentid'=>2,'name'=>'二级栏目三'),
     *            6 => array('cid'=>'6','parentid'=>3,'name'=>'三级栏目一'),
     *            7 => array('cid'=>'7','parentid'=>3,'name'=>'三级栏目二')
     *            )
     */
    public function init($arr = array(), $name = '商品分类')
    {
        $this->arr = $arr;
        $this->ret = " ";
        $this->pid = isset($this->pid) ? $this->pid : 'parentid';
        $this->cid = isset($this->cid) ? $this->cid : 'catid';
        $this->name = isset($this->name) ? $this->name : 'name';
        // 对数组数据修正-增加根栏目
        array_unshift($this->arr, array(
            $this->cid => 0,
            $this->name => $name,
            $this->pid => '-1'
        ));
        return is_array($arr);
    }

    /**
     * 得到下级栏目的信息数组
     * 
     * @param int $myid 当前栏目id
     * @return array 子级栏目数组
     */
    public function getChild($myid)
    {
        $v = $newArr = array();
        if (is_array($this->arr)) {
            foreach ($this->arr as $id => $v) {
                if ($v[$this->pid] == $myid)
                    $newArr[] = $v;
            }
        }
        return $newArr ? $newArr : false;
    }

    /**
     * 得到树形结构
     * @param int $sid
     *            初始栏目id (-1 为从根目录开始)
     * @param string $treeStr
     *            生成的结构形式
     *            支持参数列表：
     *            {cid} 当前条目cid 对应于传入数组的下标
     *            {name} 当前条目name 对应于传入数组的下标
     *            {url} 当前条目url 对应于传入数组的下标
     *            {spacer} 分隔符位置
     *            {selected=***} 选中样式
     *            例如
     *            <option value="{cid}" {selected}>{spacer}{name}</option>
     * @param string $selectStr
     *            selected的具体样式 selected="selected"
     * @param int $myid
     *            当前所在栏目id
     * @param string $adds
     *            前置分隔符
     * @return string
     */
    public function getTree($sid, $treeStr, $selectStr, $myid = 0, $adds = '')
    {
        $number = 1;
        $child = $this->getChild($sid);
        if (is_array($child)) {
            $total = count($child);
            foreach ($child as $id => $value) {
                $key = $prekey = ''; // 分隔符 前置分隔符
                if ($number == $total) {
                    $key .= $this->icon[2];
                } else {
                    $key .= $this->icon[1];
                    $prekey = $adds ? $this->icon[0] : '';
                }
                $spacer = $adds ? $adds . $key : '';
                // 判断是否选中状态
                if ($value[$this->cid] == $myid)
                    $nstr = str_replace('{selected}', $selectStr, $treeStr);
                else
                    $nstr = str_replace('{selected}', '', $treeStr);
                    // 替换分隔符
                $nstr = str_replace('{spacer}', $spacer, $nstr);
                // 替换数组数据
                foreach ($value as $k => $v) {
                    $nstr = str_replace('{' . $k . '}', $v, $nstr);
                }
                $this->ret .= $nstr; // 获取最后输出的结果
                $nstr .= $this->getTree($value[$this->cid], $treeStr, $selectStr, $myid, $adds . $prekey . $this->nbsp);
                $number ++;
            }
            foreach ($value as $k => $v) {
                $nstr = str_replace('{' . $k . '}', $v, $nstr);
            }
        }
        return $this->ret;
    }
}
?>