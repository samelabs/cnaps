<?php
/**
 * 银行行号查询系统 - 新版
 * 支持5种查询场景：联行号、电子汇票、承兑行号、清算行号、业务渠道
 */
$dbPath = __DIR__ . '/cnaps.db';

// 获取查询参数
$type = $_GET['type'] ?? 'cnaps';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// 验证查询类型
$validTypes = ['cnaps', 'cds', 'acceptor', 'clearing', 'channel'];
if (!in_array($type, $validTypes)) {
    $type = 'cnaps';
}

// 配置各查询类型
$config = [
    'cnaps' => [
        'title' => '联行号查询',
        'subtitle' => '大额支付行号 / CNAPS / 开户行号',
        'placeholder' => '输入银行名称或12位联行号',
        'table' => 'cnaps',
        'codeField' => 'cnaps_code',
        'nameField' => 'bank_name',
        'hasAddress' => false
    ],
    'cds' => [
        'title' => '电子汇票行号查询',
        'subtitle' => 'CDS系统电票行号',
        'placeholder' => '输入银行名称或CDS行号',
        'table' => 'cdsCnap',
        'codeField' => 'cds_cnaps',
        'nameField' => 'bank_name',
        'hasAddress' => false
    ],
    'acceptor' => [
        'title' => '承兑行号查询',
        'subtitle' => '票交所参与者代码',
        'placeholder' => '输入银行名称或票交所代码',
        'table' => 'cdsCnapNew',
        'codeField' => 'exchange_code',
        'nameField' => 'bank_name',
        'hasAddress' => true
    ],
    'clearing' => [
        'title' => '清算行号查询',
        'subtitle' => '人行大额支付直接参与者',
        'placeholder' => '输入银行名称或清算行号',
        'table' => 'pay2bank',
        'codeField' => 'clearing_code',
        'nameField' => 'bank_name',
        'hasAddress' => false
    ],
    'channel' => [
        'title' => '业务渠道查询',
        'subtitle' => 'CDS业务渠道代码',
        'placeholder' => '输入银行名称或渠道代码',
        'table' => 'bsnDealwChnl',
        'codeField' => 'channel_code',
        'nameField' => 'bank_name',
        'hasAddress' => false
    ]
];

$currentConfig = $config[$type];
$results = [];
$resultCount = 0;

// 执行查询
if ($q !== '') {
    try {
        $db = new SQLite3($dbPath, SQLITE3_OPEN_READONLY);
        $table = $currentConfig['table'];
        $codeField = $currentConfig['codeField'];
        $nameField = $currentConfig['nameField'];

        if ($currentConfig['hasAddress']) {
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

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $results[] = $row;
        }
        $resultCount = count($results);
        $db->close();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $currentConfig['title'] ?> - 银行公开信息数据源</title>
    <meta name="keywords" content="联行号查询,大额支付行号,CNAPS,承兑行号,清算行号,电票行号">
    <meta name="description" content="全国银行行号查询系统，支持联行号、电子汇票行号、承兑行号、清算行号、业务渠道查询">
    <style>
        :root {
            --primary: #ff4d4d;
            --primary-dark: #e60000;
            --primary-light: #fff0f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text: #1f2937;
            --text-light: #6b7280;
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e5e7eb;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto,
                         'Noto Sans SC', 'PingFang SC', 'Microsoft YaHei', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: #ff4d4d;
            color: white;
            padding: 2rem 1rem 3rem;
            text-align: center;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        /* Hot Links */
        .hot-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: -1.5rem auto 0;
            padding: 0 1rem;
            max-width: 600px;
        }

        .hot-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--card);
            border-radius: 2rem;
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            position: relative;
        }

        .hot-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .hot-icon {
            font-size: 1.25rem;
        }

        .hot-text {
            font-size: 0.95rem;
        }

        .hot-badge {
            background: var(--primary);
            color: white;
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Main Container */
        .container {
            max-width: 800px;
            margin: 0.5rem auto 2rem;
            padding: 0 1rem;
        }

        .card {
            background: var(--card);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        /* Tabs */
        .tabs {
            display: flex;
            background: var(--primary-light);
            padding: 0.5rem;
            gap: 0.25rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .tab {
            flex: 1;
            min-width: 80px;
            padding: 0.75rem 0.5rem;
            border: none;
            background: transparent;
            color: var(--text-light);
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .tab:hover {
            color: var(--primary);
        }

        .tab.active {
            background: var(--card);
            color: var(--primary);
            box-shadow: var(--shadow);
        }

        /* Tab Icons */
        .tab-icon {
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        /* Search Box */
        .search-box {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .search-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-subtitle {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .search-form {
            display: flex;
            gap: 0.75rem;
        }

        .search-input {
            flex: 1;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.2s;
            background: var(--bg);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--card);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .search-btn {
            padding: 0.875rem 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .search-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .search-btn:active {
            transform: translateY(0);
        }

        /* Results */
        .results {
            padding: 1.5rem;
        }

        .query-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: var(--primary-light);
            border-radius: 0.75rem;
            color: var(--primary);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .result-count {
            text-align: center;
            padding: 0.75rem;
            background: var(--bg);
            border-radius: 0.5rem;
            color: var(--text-light);
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .result-item {
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }

        .result-item:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .result-item:last-child {
            margin-bottom: 0;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .result-name {
            font-size: 1rem;
            font-weight: 500;
            word-break: break-all;
        }

        .copy-btn {
            padding: 0.375rem 0.75rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .copy-btn:hover {
            background: var(--primary-dark);
        }

        .copy-btn.copied {
            background: var(--primary);
        }

        .result-code {
            font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
            font-size: 0.9rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .code-value {
            background: var(--bg);
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            color: var(--primary);
        }

        .result-address {
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px dashed var(--border);
            font-size: 0.875rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-text {
            color: var(--text-light);
        }

        /* Info Section */
        .info-section {
            padding: 1.5rem;
            background: var(--bg);
            margin-top: 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            color: var(--text-light);
            line-height: 1.8;
        }

        .info-section strong {
            color: var(--text);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.75rem;
        }

        .quick-btn {
            padding: 0.375rem 0.75rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-light);
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .header h1 {
                font-size: 1.5rem;
            }

            .container {
                margin-top: -1.5rem;
            }

            .card {
                border-radius: 1rem;
            }

            .search-form {
                flex-direction: column;
            }

            .search-btn {
                width: 100%;
            }

            .result-header {
                flex-direction: column;
            }

            .copy-btn {
                width: 100%;
                text-align: center;
            }

            .tab {
                font-size: 0.75rem;
                padding: 0.625rem 0.5rem;
            }
        }

        @media (min-width: 640px) {
            .tabs {
                justify-content: center;
            }

            .tab {
                min-width: 100px;
            }
        }

        /* Loading Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loading {
            text-align: center;
            padding: 3rem;
            color: var(--text-light);
            animation: pulse 1.5s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>🏦 银行公开信息数据源</h1>
        <p>银行行号、电票行号、承兑行号查询服务</p>
    </header>

    <!-- Hot Links -->
    <div class="hot-links">
        <a href="api-docs.php" class="hot-link">
            <span class="hot-icon">📡</span>
            <span class="hot-text">开发者 API</span>
        </a>
        <a href="mcp-docs.php" class="hot-link">
            <span class="hot-icon">🔌</span>
            <span class="hot-text">MCP 集成</span>
        </a>
    </div>

    <div class="container">
        <div class="card">
            <!-- Tabs -->
            <nav class="tabs">
                <button class="tab <?= $type === 'cnaps' ? 'active' : '' ?>"
                        onclick="switchTab('cnaps')">
                    <span class="tab-icon">🔢</span>
                    联行号
                </button>
                <button class="tab <?= $type === 'cds' ? 'active' : '' ?>"
                        onclick="switchTab('cds')">
                    <span class="tab-icon">📄</span>
                    电子汇票
                </button>
                <button class="tab <?= $type === 'acceptor' ? 'active' : '' ?>"
                        onclick="switchTab('acceptor')">
                    <span class="tab-icon">✍️</span>
                    承兑行号
                </button>
                <button class="tab <?= $type === 'clearing' ? 'active' : '' ?>"
                        onclick="switchTab('clearing')">
                    <span class="tab-icon">🏛️</span>
                    清算行号
                </button>
                <button class="tab <?= $type === 'channel' ? 'active' : '' ?>"
                        onclick="switchTab('channel')">
                    <span class="tab-icon">🔀</span>
                    业务渠道
                </button>
            </nav>

            <!-- Search Box -->
            <div class="search-box">
                <h2 class="search-title"><?= $currentConfig['title'] ?></h2>
                <p class="search-subtitle"><?= $currentConfig['subtitle'] ?></p>
                <form class="search-form" method="get" action="">
                    <input type="hidden" name="type" value="<?= $type ?>">
                    <input type="text"
                           name="q"
                           class="search-input"
                           placeholder="<?= $currentConfig['placeholder'] ?>"
                           value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
                           autofocus
                           id="searchInput">
                    <button type="submit" class="search-btn">查询</button>
                </form>

                <!-- Quick Actions for CNAPS -->
                <?php if ($type === 'cnaps' && $q === ''): ?>
                <div class="quick-actions">
                    <button class="quick-btn" onclick="quickSearch('工商')">工商银行</button>
                    <button class="quick-btn" onclick="quickSearch('农业')">农业银行</button>
                    <button class="quick-btn" onclick="quickSearch('中国银行')">中国银行</button>
                    <button class="quick-btn" onclick="quickSearch('建设')">建设银行</button>
                    <button class="quick-btn" onclick="quickSearch('招商')">招商银行</button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Results -->
            <div class="results">
                <?php if ($q !== ''): ?>
                    <div class="query-info">
                        <span>🔍</span>
                        <span>查询 "<strong><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></strong>" 的结果</span>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">⚠️</div>
                            <div class="empty-text">数据库连接失败，请稍后重试</div>
                        </div>
                    <?php elseif ($resultCount > 0): ?>
                        <?php
                        $codeField = $currentConfig['codeField'];
                        $nameField = $currentConfig['nameField'];
                        foreach ($results as $row):
                            $code = htmlspecialchars($row[$codeField] ?? '', ENT_QUOTES, 'UTF-8');
                            $name = htmlspecialchars($row[$nameField] ?? '', ENT_QUOTES, 'UTF-8');
                            $address = htmlspecialchars($row['address'] ?? '', ENT_QUOTES, 'UTF-8');
                            $codeLabel = $type === 'acceptor' ? '票交所代码' :
                                        ($type === 'clearing' ? '清算行号' :
                                        ($type === 'channel' ? '渠道代码' : '联行号'));
                        ?>
                        <div class="result-item">
                            <div class="result-header">
                                <div class="result-name"><?= $name ?></div>
                                <button class="copy-btn" onclick="copyCode(this, '<?= $code ?>')">
                                    复制
                                </button>
                            </div>
                            <div class="result-code">
                                <span><?= $codeLabel ?>:</span>
                                <span class="code-value"><?= $code ?></span>
                            </div>
                            <?php if ($currentConfig['hasAddress'] && $address): ?>
                            <div class="result-address">
                                <span>📍</span>
                                <span><?= $address ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>

                        <?php if ($resultCount >= 100): ?>
                        <div class="result-count">
                            找到超过100条结果，仅显示前100条
                        </div>
                        <?php else: ?>
                        <div class="result-count">
                            共找到 <strong><?= $resultCount ?></strong> 条结果
                        </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">🔍</div>
                            <div class="empty-text">未找到匹配结果</div>
                            <div class="empty-text" style="font-size: 0.8rem; margin-top: 0.5rem">
                                请尝试输入更简短的关键词
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">🔎</div>
                        <div class="empty-text">输入银行名称或代码开始查询</div>
                    </div>
                <?php endif; ?>

                <!-- Info Section -->
                <?php if ($type === 'cnaps'): ?>
                <div class="info-section">
                    <p><strong>数据更新：</strong>2026年2月，合计 154,570 家银行网点。</p>
                    <p><strong>联行号说明：</strong>联行号（CNAPS）是12位编码，由3位银行代码+4位地区代码+4位网点序号+1位校验码组成，用于跨行支付结算。</p>
                </div>
                <?php elseif ($type === 'cds'): ?>
                <div class="info-section">
                    <p><strong>数据更新：</strong>2026年2月，合计 154,685 条记录。</p>
                    <p><strong>CDS系统：</strong>上海清算所信用违约互换清算系统，用于电子商业汇票业务。</p>
                </div>
                <?php elseif ($type === 'acceptor'): ?>
                <div class="info-section">
                    <p><strong>数据更新：</strong>2026年2月，合计 114,843 条记录（含地址）。</p>
                    <p><strong>承兑行号：</strong>票交所机构参与者代码，用于开具电子银行承兑汇票时选择承兑银行。</p>
                </div>
                <?php elseif ($type === 'clearing'): ?>
                <div class="info-section">
                    <p><strong>数据更新：</strong>2026年2月，合计 552 家直接参与者。</p>
                    <p><strong>清算行号：</strong>人民银行大额支付系统直接参与者，在央行开立清算账户的银行机构。</p>
                </div>
                <?php elseif ($type === 'channel'): ?>
                <div class="info-section">
                    <p><strong>数据更新：</strong>2026年2月，合计 3,011 条业务渠道记录。</p>
                    <p><strong>业务渠道：</strong>CDS系统业务渠道代码，用于业务请求分发到对应银行总行。</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p style="font-size: 0.85rem; opacity: 0.8;">数据每周一凌晨更新 | 最后更新：2026-02-05</p>
        <p style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.6;">数据来源：中国人民银行 | 上海票据交易所 | 上海清算所</p>
    </footer>

    <script>
        function switchTab(type) {
            window.location.href = '?type=' + type;
        }

        function quickSearch(keyword) {
            document.getElementById('searchInput').value = keyword;
            document.querySelector('.search-form').submit();
        }

        function copyCode(btn, code) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(() => {
                    btn.textContent = '已复制';
                    btn.classList.add('copied');
                    setTimeout(() => {
                        btn.textContent = '复制';
                        btn.classList.remove('copied');
                    }, 2000);
                }).catch(() => {
                    fallbackCopy(btn, code);
                });
            } else {
                fallbackCopy(btn, code);
            }
        }

        function fallbackCopy(btn, code) {
            const textarea = document.createElement('textarea');
            textarea.value = code;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                btn.textContent = '已复制';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = '复制';
                    btn.classList.remove('copied');
                }, 2000);
            } catch (err) {
                console.error('复制失败', err);
            }
            document.body.removeChild(textarea);
        }

        // Auto-focus search input on desktop
        if (window.innerWidth > 640) {
            document.getElementById('searchInput').focus();
        }
    </script>
</body>
</html>
