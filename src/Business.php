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

use think\App;
use think\console\Input;
use think\console\Output;
use Workerman\Worker;
use GatewayWorker\BusinessWorker;

class Business
{
    /**
     * 配置参数
     * @var array
     */
	protected $options = [
        // BusinessWorker进程名称, 方便status命令中查看统计
        'name' => 'think-gateway-business',
        // BusinessWorker进程数量, 根据业务是否有阻塞式IO设置进程数为CPU核数的1倍-4倍即可。
        'count' => 2,
        // 注册服务地址, 格式类似于 '127.0.0.1:1236'。
        // 如果是部署了多个register服务则格式是数组，类似['192.168.0.1:1236','192.168.0.2:1236']
        'register_address' => '127.0.0.1:1236',
        // Gateway通讯密钥
        'secret_key' => '',
        // 业务处理类，业务类至少要实现onMessage静态方法，onConnect和onClose静态方法可以不用实现。
        'event_handler' => '',
        // 是否以守护进程启动
        'daemonize' => false,
	];

    /**
     * App实例
     * @var App
     */
    protected $app;

    /**
     * Input实例
     * @var Input
     */
    protected $input;

    /**
     * Output实例
     * @var Output
     */
    protected $output;

    /**
     * 架构函数
     * @access public
	 * @param App $app 应用实例
     * @param Input $input 输入
     * @param Output $output 输出
     * @return void
     */
    public function __construct(App $app, Input $input, Output $output)
    {
        $this->app = $app;
        $this->input = $input;
        $this->output = $output;
        // 合并配置
		$this->options = array_merge($this->options, $this->app->config->get('gatewaybusiness'));
        // 如果业务处理类是空
        if(empty($this->options['event_handler'])){
            throw new \Exception('business event handler can not be empty');
        }
        // 初始化
		$this->init();
    }

    /**
     * 初始化
     * @access protected
	 * @return void
     */
	protected function init()
	{
		// BussinessWorker 进程
        $businessWorker = new BusinessWorker();
        // worker名称
        $businessWorker->name = $this->options['name'];
        if(empty($businessWorker->name)){
            $businessWorker->name = 'think-gateway-business';
        }
        // 设置runtime路径
        $this->app->setRuntimePath($this->app->getRuntimePath() . $businessWorker->name . DIRECTORY_SEPARATOR);
        // BussinessWorker进程数量
        $businessWorker->count = $this->options['count'];
        // 服务注册地址
        $businessWorker->registerAddress = $this->options['register_address'];
        // Gateway通讯密钥
        $businessWorker->secretKey = $this->options['secret_key'];
        // 业务处理类
        $businessWorker->eventHandler = $this->options['event_handler'];
        // 如果指定以守护进程方式运行
        if ($this->input->hasOption('daemon') || true === $this->options['daemonize']) {
            Worker::$daemonize = true;
        }
	}

    /**
     * 启动
     * @access public
	 * @return void
     */
	public function start()
	{
        // 启动
		Worker::runAll();
	}

    /**
     * 停止
     * @access public
     * @return void
     */
    public function stop()
    {
        Worker::stopAll();
    }
}
