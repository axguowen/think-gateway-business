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

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * @param string $client_id 连接id
     * @return void
     */
    public static function onConnect(string $client_id)
    {
        // 向当前client_id发送数据 
        Gateway::sendToClient($client_id, "Hello $client_id\r\n");
        // 向所有人发送
        Gateway::sendToAll("$client_id login\r\n");
    }

    /**
     * 当客户端发来消息时触发
     * @param string $client_id 连接id
     * @param mixed $message 具体消息
     * @return void
     */
    public static function onMessage(string $client_id, $message)
    {
        // 向所有人发送 
        Gateway::sendToAll("$client_id said $message\r\n");
    }
   
    /**
     * 当用户断开连接时触发
     * @param string $client_id 连接id
     * @return void
     */
    public static function onClose(string $client_id)
    {
        // 向所有人发送 
        GateWay::sendToAll("$client_id logout\r\n");
    }
}
