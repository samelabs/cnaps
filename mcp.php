<?php
/**
 * MCP (Model Context Protocol) Server for Bank Code Query
 * 提供银行行号查询的 MCP 工具
 *
 * 数据更新：每周一凌晨
 */
header('Content-Type: application/json; charset=utf-8');

$dbPath = __DIR__ . '/cnaps.db';

// 获取 MCP 请求
$input = file_get_contents('php://input');
$request = json_decode($input, true);

// 处理 MCP 请求
function handleMCP($request) {
    global $dbPath;

    // 初始化
    if (isset($request['jsonrpc']) && $request['method'] === 'initialize') {
        return [
            'jsonrpc' => '2.0',
            'id' => $request['id'] ?? null,
            'result' => [
                'protocolVersion' => '2024-11-05',
                'capabilities' => [
                    'tools' => [
                        'listChanged' => false
                    ]
                ],
                'serverInfo' => [
                    'name' => 'bank-code-query',
                    'version' => '1.0.0',
                    'description' => '银行行号查询 MCP 服务器',
                    'updateFrequency' => '每周一凌晨',
                    'lastUpdate' => '2026-02-05'
                ]
            ]
        ];
    }

    // 列出可用工具
    if (isset($request['jsonrpc']) && $request['method'] === 'tools/list') {
        return [
            'jsonrpc' => '2.0',
            'id' => $request['id'] ?? null,
            'result' => [
                'tools' => [
                    [
                        'name' => 'query_cnaps',
                        'description' => '查询银行联行号（CNAPS代码）- 支持银行名称或12位联行号查询，数据量15.5万条',
                        'inputSchema' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => '查询关键词：银行名称或联行号'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'query_cds',
                        'description' => '查询电子汇票行号（CDS系统）- 用于电子商业汇票业务，数据量15.5万条',
                        'inputSchema' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => '查询关键词：银行名称或CDS行号'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'query_acceptor',
                        'description' => '查询承兑行号（票交所参与者代码）- 用于开具电子银行承兑汇票，包含地址信息，数据量11.5万条',
                        'inputSchema' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => '查询关键词：银行名称或票交所代码'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'query_clearing',
                        'description' => '查询清算行号（人行大额支付直接参与者）- 查询在央行开立清算账户的银行机构，数据量552条',
                        'inputSchema' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => '查询关键词：银行名称或清算行号'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'query_channel',
                        'description' => '查询业务渠道代码（CDS业务渠道）- 查询业务渠道路由信息，数据量3011条',
                        'inputSchema' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => '查询关键词：银行名称或渠道代码'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'get_stats',
                        'description' => '获取数据库统计信息 - 返回各查询类型的数据量统计',
                        'inputSchema' => [
                            'type' => 'object',
                            'properties' => []
                        ]
                    ]
                ]
            ]
        ];
    }

    // 调用工具
    if (isset($request['jsonrpc']) && $request['method'] === 'tools/call') {
        $params = $request['params'] ?? [];
        $toolName = $params['name'] ?? '';
        $arguments = $params['arguments'] ?? [];

        try {
            $db = new SQLite3($dbPath, SQLITE3_OPEN_READONLY);

            switch ($toolName) {
                case 'query_cnaps':
                    return queryTool($db, 'cnaps', 'cnaps_code', 'bank_name', $arguments['query'] ?? '', false, $request['id'] ?? null);

                case 'query_cds':
                    return queryTool($db, 'cdsCnap', 'cds_cnaps', 'bank_name', $arguments['query'] ?? '', false, $request['id'] ?? null);

                case 'query_acceptor':
                    return queryTool($db, 'cdsCnapNew', 'exchange_code', 'bank_name', $arguments['query'] ?? '', true, $request['id'] ?? null);

                case 'query_clearing':
                    return queryTool($db, 'pay2bank', 'clearing_code', 'bank_name', $arguments['query'] ?? '', false, $request['id'] ?? null);

                case 'query_channel':
                    return queryTool($db, 'bsnDealwChnl', 'channel_code', 'bank_name', $arguments['query'] ?? '', false, $request['id'] ?? null);

                case 'get_stats':
                    $stats = [];
                    $tables = [
                        'cnaps' => '联行号',
                        'cdsCnap' => '电子汇票',
                        'cdsCnapNew' => '承兑行号',
                        'pay2bank' => '清算行号',
                        'bsnDealwChnl' => '业务渠道'
                    ];
                    $total = 0;
                    foreach ($tables as $table => $label) {
                        $count = $db->querySingle("SELECT COUNT(*) FROM $table");
                        $total += $count;
                        $stats[] = [
                            'type' => $label,
                            'table' => $table,
                            'count' => $count
                        ];
                    }
                    $db->close();
                    return [
                        'jsonrpc' => '2.0',
                        'id' => $request['id'] ?? null,
                        'result' => [
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => json_encode([
                                        'total' => $total,
                                        'by_type' => $stats,
                                        'last_update' => '2026-02-05',
                                        'update_frequency' => '每周一凌晨'
                                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                                ]
                            ]
                        ]
                    ];

                default:
                    return [
                        'jsonrpc' => '2.0',
                        'id' => $request['id'] ?? null,
                        'error' => [
                            'code' => -32601,
                            'message' => "未知工具: $toolName"
                        ]
                    ];
            }
        } catch (Exception $e) {
            return [
                'jsonrpc' => '2.0',
                'id' => $request['id'] ?? null,
                'error' => [
                    'code' => -32603,
                    'message' => $e->getMessage()
                ]
            ];
        }
    }

    // Ping
    if (isset($request['jsonrpc']) && $request['method'] === 'ping') {
        return [
            'jsonrpc' => '2.0',
            'id' => $request['id'] ?? null,
            'result' => ['pong']
        ];
    }

    return [
        'jsonrpc' => '2.0',
        'id' => $request['id'] ?? null,
        'error' => [
            'code' => -32601,
            'message' => '未知方法'
        ]
    ];
}

function queryTool($db, $table, $codeField, $nameField, $query, $hasAddress, $id) {
    if (empty($query)) {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => -32602,
                'message' => '缺少查询参数'
            ]
        ];
    }

    if ($hasAddress) {
        $stmt = $db->prepare(
            "SELECT $codeField, $nameField, address FROM $table
             WHERE $codeField LIKE :q OR $nameField LIKE :q
             LIMIT 50"
        );
    } else {
        $stmt = $db->prepare(
            "SELECT $codeField, $nameField FROM $table
             WHERE $codeField LIKE :q OR $nameField LIKE :q
             LIMIT 50"
        );
    }

    $qParam = "%$query%";
    $stmt->bindValue(':q', $qParam, SQLITE3_TEXT);
    $result = $stmt->execute();

    $results = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $item = [
            'code' => $row[$codeField],
            'name' => $row[$nameField]
        ];
        if ($hasAddress && !empty($row['address'])) {
            $item['address'] = $row['address'];
        }
        $results[] = $item;
    }

    $db->close();

    $output = [
        'query' => $query,
        'count' => count($results),
        'results' => $results
    ];

    return [
        'jsonrpc' => '2.0',
        'id' => $id,
        'result' => [
            'content' => [
                [
                    'type' => 'text',
                    'text' => json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                ]
            ]
        ]
    ];
}

// 处理请求并返回
$response = handleMCP($request ?? []);
echo json_encode($response, JSON_UNESCAPED_UNICODE);
