<?php
/**
 * 银行行号查询 API
 * 支持联行号、电子汇票、承兑行号、清算行号、业务渠道查询
 *
 * 数据更新：每周一凌晨
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$dbPath = __DIR__ . '/cnaps.db';

// 获取请求参数
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$q = isset($_GET['q']) ? trim($_GET['q']) : (isset($_POST['q']) ? trim($_POST['q']) : '');
$type = $_GET['type'] ?? $_POST['type'] ?? 'cnaps';
$format = $_GET['format'] ?? $_POST['format'] ?? 'json';

// 验证类型
$validTypes = [
    'cnaps' => ['table' => 'cnaps', 'code' => 'cnaps_code', 'name' => 'bank_name', 'label' => '联行号'],
    'cds' => ['table' => 'cdsCnap', 'code' => 'cds_cnaps', 'name' => 'bank_name', 'label' => 'CDS行号'],
    'acceptor' => ['table' => 'cdsCnapNew', 'code' => 'exchange_code', 'name' => 'bank_name', 'label' => '票交所代码'],
    'clearing' => ['table' => 'pay2bank', 'code' => 'clearing_code', 'name' => 'bank_name', 'label' => '清算行号'],
    'channel' => ['table' => 'bsnDealwChnl', 'code' => 'channel_code', 'name' => 'bank_name', 'label' => '渠道代码']
];

if (!isset($validTypes[$type])) {
    http_response_code(400);
    echo json_encode(['error' => '无效的查询类型', 'valid_types' => array_keys($validTypes)]);
    exit;
}

// API 信息
if ($action === 'info') {
    echo json_encode([
        'name' => '银行行号查询 API',
        'version' => '1.0.0',
        'description' => '提供全国银行行号、联行号、承兑行号等查询服务',
        'update_frequency' => '每周一凌晨',
        'last_update' => '2026-02-05',
        'endpoints' => [
            '/api.php' => '主查询接口',
            '/api.php?action=info' => 'API信息',
            '/api.php?action=stats' => '数据统计'
        ],
        'parameters' => [
            'q' => '查询关键词（银行名称或代码）',
            'type' => '查询类型：cnaps, cds, acceptor, clearing, channel',
            'format' => '返回格式：json（默认）'
        ],
        'types' => [
            'cnaps' => '联行号查询（标准CNAPS）',
            'cds' => '电子汇票行号查询（CDS系统）',
            'acceptor' => '承兑行号查询（票交所代码）',
            'clearing' => '清算行号查询（人行直接参与者）',
            'channel' => '业务渠道查询（CDS业务渠道）'
        ],
        'examples' => [
            '/api.php?q=工商&type=cnaps',
            '/api.php?q=102100099996&type=clearing',
            '/api.php?q=000000001&type=acceptor'
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 数据统计
if ($action === 'stats') {
    try {
        $db = new SQLite3($dbPath, SQLITE3_OPEN_READONLY);
        $stats = [];
        foreach ($validTypes as $key => $config) {
            $result = $db->querySingle("SELECT COUNT(*) FROM {$config['table']}");
            $stats[$key] = [
                'label' => $config['label'],
                'count' => $result
            ];
        }
        echo json_encode([
            'total' => array_sum(array_column($stats, 'count')),
            'by_type' => $stats,
            'last_update' => '2026-02-05'
        ], JSON_UNESCAPED_UNICODE);
        $db->close();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// 执行查询
if ($q === '') {
    http_response_code(400);
    echo json_encode([
        'error' => '缺少查询参数',
        'message' => '请提供查询关键词 q'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = new SQLite3($dbPath, SQLITE3_OPEN_READONLY);
    $config = $validTypes[$type];
    $table = $config['table'];
    $codeField = $config['code'];
    $nameField = $config['name'];

    // 构建查询
    $hasAddress = ($type === 'acceptor');
    if ($hasAddress) {
        $stmt = $db->prepare(
            "SELECT $codeField, $nameField, address FROM $table
             WHERE $codeField LIKE :q OR $nameField LIKE :q
             LIMIT 100"
        );
    } else {
        $stmt = $db->prepare(
            "SELECT $codeField, $nameField FROM $table
             WHERE $codeField LIKE :q OR $nameField LIKE :q
             LIMIT 100"
        );
    }

    $qParam = "%$q%";
    $stmt->bindValue(':q', $qParam, SQLITE3_TEXT);
    $result = $stmt->execute();

    $data = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $item = [
            'code' => $row[$codeField],
            'name' => $row[$nameField]
        ];
        if ($hasAddress && !empty($row['address'])) {
            $item['address'] = $row['address'];
        }
        $data[] = $item;
    }

    echo json_encode([
        'success' => true,
        'query' => $q,
        'type' => $type,
        'type_label' => $config['label'],
        'count' => count($data),
        'has_more' => count($data) >= 100,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);

    $db->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
