# PhpSpreadsheetCache
a cache component of PhpSpreadsheet

PhpSpreadsheet 在工作表中平均每个单元格使用约 1k，因此大型工作簿可以迅速用尽可用内存。

单元缓存提供了一种机制，该机制允许 PhpSpreadsheet 在较小的内存或非内存（例如，在磁盘上，APCu，内存缓存或 Redis、Memcache 中）中维护单元对象。

这使您可以减少大型工作簿的内存使用量，尽管以访问单元数据的速度为代价。 

自定义缓存必须先引入 psr/simple-cache:^1.0 包并实现 Psr\SimpleCache\CacheInterface 接口

获取更多信息，请参考[PhpSpreadsheet官方文档](https://phpspreadsheet.readthedocs.io/en/stable/)

#### PhpSpreadsheetCache 提供了自定义缓存组件的实现

Composer安装：
>composer require phpspreadsheetcache:^1.3

使用示例：
~~~
$reader = IOFactory::createReader('Csv');
$reader->setReadDataOnly(true);
$config = [
    'type' => 'Redis', // 或者File
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => '123456',
    'select' => '1',
    'path' => Yii::getAlias('@runtime/spreadsheet'), // 文件缓存路径
];
Settings::setCache(Cache::init($config));
foreach ($arr as $val) {
    $spreadSheet = $reader->load(Yii::getAlias('@runtime/a.csv'));
    $workSheet = $spreadSheet->getActiveSheet();
    // 释放内存
    $workSheet->disconnectCells();
    // 清理缓存
    // Cache::clear();
    // your code
}
~~~
