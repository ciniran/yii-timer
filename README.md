# Yii,timer,tool
-  本工具主要用于调试PHP代码的执行时间。
对于自己写出来的代码，感觉执行效率有问题的时候可以用本工具来进行一个辅助分析。
- 本工具是单例运行，以YII的组件的形式开发，在YII应用实例化完成之后就可以调用运行
- 本工具默认调用YII的log记录输出时间，也可以配置为文件存储
- 安装方法
````
composer require ciniran/yii-timer
````
- 配置方法如下,(配置到Yii组件 common/config/main-local.php文件中)：
````
'components'=>[
   'timer'  => [
        'class' => \ciniran\timer\Timer::class,
        'saveFile' => true,
        'logFileName'=>'tlog.txt'
        ],
    ],
````
- 使用方法如下：
````

\Yii::$app->timer->start(); //计时开始
doSomething…… //程序代码块

\Yii::$app->timer->point(); //中间时间切分点
doSomething…… //程序代码块


\Yii::$app->timer->end(); //计时结束

````
-日志最终输出结果如下：
````
[
    [
        '开始时间' => 1534153709.012573,
        '结束时间' => 1534153709.013561,
        '当前用时' => 0.98799999999999999,
        '总用时' => 0.98799999999999999,
        '开始文件' => 'C:\\workspace\\brdc\\vendor\\yiisoft\\yii2\\base\\Application.php',
        '开始行' => 384,
        '结束文件' => 'C:\\workspace\\brdc\\api\\web\\index.php',
        '结束行' => 28,
    ],
    [
        '开始时间' => 1534153709.012573,
        '结束时间' => 1534153709.04952,
        '当前用时' => 35.959000000000003,
        '总用时' => 36.947000000000003,
        '开始文件' => 'C:\\workspace\\brdc\\common\\components\\Timer.php',
        '开始行' => 97,
        '结束文件' => 'C:\\workspace\\brdc\\api\\web\\index.php',
        '结束行' => 32,
    ],
]
````