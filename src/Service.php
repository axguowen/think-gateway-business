<?php
// +----------------------------------------------------------------------
// | ThinkPHP Gateway Business [Gateway Business Service For ThinkPHP]
// +----------------------------------------------------------------------
// | ThinkPHP Gateway Business 服务
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axguowen <axguowen@qq.com>
// +----------------------------------------------------------------------

namespace think\gateway\business;

class Service extends \think\Service
{
    /**
     * 注册服务
     * @access public
     * @return void
     */
    public function register()
    {
        // 设置命令
        $this->commands([
            'gateway:business' => Command::class,
        ]);
    }
}
