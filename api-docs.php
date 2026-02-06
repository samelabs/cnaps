<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API 文档 - 银行行号查询系统</title>
    <style>
        :root {
            --primary: #ff4d4d;
            --coral: #ff6b6b;
            --coral-dark: #ee5a5a;
            --primary-dark: #e60000;
            --primary-light: #fff0f0;
            --text: #1f2937;
            --text-light: #6b7280;
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e5e7eb;
            --code-bg: #1e293b;
            --code-text: #e2e8f0;
            --success: #10b981;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto,
                         'Noto Sans SC', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }
        .header {
            background: #ff4d4d;
            color: white;
            padding: 2rem 1rem;
        }
        .header h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .header p { opacity: 0.9; font-size: 0.9rem; }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .nav a {
            padding: 0.5rem 1rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            text-decoration: none;
            color: var(--text);
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .nav a:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .section {
            background: var(--card);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .section h2 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section h3 {
            font-size: 1rem;
            margin: 1rem 0 0.5rem;
            color: var(--text);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: var(--success);
            color: white;
            font-size: 0.75rem;
            border-radius: 0.25rem;
        }
        .badge-update {
            background: var(--primary);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 0.875rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        th {
            background: var(--bg);
            font-weight: 600;
        }
        .code {
            background: var(--code-bg);
            color: var(--code-text);
            padding: 1rem;
            border-radius: 0.5rem;
            font-family: 'SF Mono', Monaco, Consolas, monospace;
            font-size: 0.875rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .code-inline {
            background: var(--bg);
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: monospace;
            font-size: 0.875rem;
        }
        .endpoint {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: var(--bg);
            border-radius: 0.5rem;
            margin: 0.5rem 0;
            font-family: monospace;
        }
        .method {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .get { background: #10b981; color: white; }
        .param-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .param-card {
            padding: 1rem;
            background: var(--bg);
            border-radius: 0.5rem;
        }
        .param-name {
            font-family: monospace;
            color: var(--primary);
            font-weight: 600;
        }
        .required {
            color: var(--danger);
            font-size: 0.75rem;
        }
        .optional {
            color: var(--text-light);
            font-size: 0.75rem;
        }
        .try-btn {
            padding: 0.5rem 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .try-btn:hover { background: var(--primary-dark); }
        @media (max-width: 640px) {
            .param-grid { grid-template-columns: 1fr; }
            .nav { flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>开发者文档</h1>
        <p>银行行号查询系统 RESTful API 接口说明</p>
    </header>

    <div class="container">
        <nav class="nav">
            <a href="index.php">🏠 返回首页</a>
            <a href="mcp.php">🔌 MCP 接口</a>
            <a href="api.php">📡 API 信息</a>
        </nav>

        <div class="section">
            <h2>📋 基本信息 <span class="badge badge-update">每周更新</span></h2>
            <table>
                <tr><th>项目</th><th>说明</th></tr>
                <tr><td>接口地址</td><td><span class="code-inline">/api.php</span></td></tr>
                <tr><td>请求方式</td><td>GET / POST</td></tr>
                <tr><td>返回格式</td><td>JSON</td></tr>
                <tr><td>字符编码</td><td>UTF-8</td></tr>
                <tr><td>数据更新</td><td>每周一凌晨</td></tr>
                <tr><td>最后更新</td><td>2026-02-05</td></tr>
            </table>
        </div>

        <div class="section">
            <h2>🔗 接口端点</h2>
            <h3>主查询接口</h3>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span>/api.php?q=关键词&type=类型</span>
            </div>
            <h3>API 信息</h3>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span>/api.php?action=info</span>
            </div>
            <h3>数据统计</h3>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span>/api.php?action=stats</span>
            </div>
        </div>

        <div class="section">
            <h2>📝 请求参数</h2>
            <div class="param-grid">
                <div class="param-card">
                    <div class="param-name">q</div>
                    <span class="required">必填</span>
                    <p>查询关键词，支持银行名称或代码</p>
                </div>
                <div class="param-card">
                    <div class="param-name">type</div>
                    <span class="optional">可选</span>
                    <p>查询类型：cnaps(默认), cds, acceptor, clearing, channel</p>
                </div>
                <div class="param-card">
                    <div class="param-name">action</div>
                    <span class="optional">可选</span>
                    <p>特殊操作：info(接口信息), stats(数据统计)</p>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>📊 查询类型</h2>
            <table>
                <tr><th>类型</th><th>名称</th><th>数据量</th><th>说明</th></tr>
                <tr><td><span class="code-inline">cnaps</span></td><td>联行号查询</td><td>15.5万</td><td>标准CNAPS联行号</td></tr>
                <tr><td><span class="code-inline">cds</span></td><td>电子汇票行号</td><td>15.5万</td><td>CDS系统电票行号</td></tr>
                <tr><td><span class="code-inline">acceptor</span></td><td>承兑行号查询</td><td>11.5万</td><td>票交所参与者代码（含地址）</td></tr>
                <tr><td><span class="code-inline">clearing</span></td><td>清算行号查询</td><td>552</td><td>人行大额支付直接参与者</td></tr>
                <tr><td><span class="code-inline">channel</span></td><td>业务渠道查询</td><td>3,011</td><td>CDS业务渠道代码</td></tr>
            </table>
        </div>

        <div class="section">
            <h2>📦 返回格式</h2>
            <div class="code">
{
    "success": true,
    "query": "工商",
    "type": "cnaps",
    "type_label": "联行号",
    "count": 100,
    "has_more": true,
    "data": [
        {
            "code": "102100000001",
            "name": "中国工商银行股份有限公司"
        },
        {
            "code": "102100000003",
            "name": "中国工商银行股份有限公司营业部"
        }
    ]
}
            </div>
        </div>

        <div class="section">
            <h2>💡 使用示例</h2>
            <h3>cURL</h3>
            <div class="code">
# 查询工商银行联行号
curl "https://example.com/api.php?q=工商&type=cnaps"

# 查询清算行号
curl "https://example.com/api.php?q=102100099996&type=clearing"

# 获取数据统计
curl "https://example.com/api.php?action=stats"
            </div>

            <h3>JavaScript</h3>
            <div class="code">
fetch('/api.php?q=工商&type=cnaps')
    .then(res => res.json())
    .then(data => {
        console.log(`找到 ${data.count} 条结果`);
        data.data.forEach(item => {
            console.log(`${item.name}: ${item.code}`);
        });
    });
            </div>

            <h3>Python</h3>
            <div class="code">
import requests

response = requests.get('/api.php', params={
    'q': '工商',
    'type': 'cnaps'
})
data = response.json()
print(f"找到 {data['count']} 条结果")
            </div>
        </div>

        <div class="section">
            <h2>🧪 在线测试</h2>
            <p style="margin-bottom: 1rem; color: var(--text-light);">输入关键词测试 API 接口：</p>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <input type="text" id="apiQuery" placeholder="输入银行名称或代码"
                       style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                <select id="apiType" style="padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                    <option value="cnaps">联行号</option>
                    <option value="cds">电子汇票</option>
                    <option value="acceptor">承兑行号</option>
                    <option value="clearing">清算行号</option>
                    <option value="channel">业务渠道</option>
                </select>
                <button class="try-btn" onclick="testAPI()">测试</button>
            </div>
            <div id="apiResult" style="margin-top: 1rem;"></div>
        </div>
    </div>

    <script>
    function testAPI() {
        const q = document.getElementById('apiQuery').value || '工商';
        const type = document.getElementById('apiType').value;
        const resultDiv = document.getElementById('apiResult');
        resultDiv.textContent = '查询中...';

        fetch(`api.php?q=${encodeURIComponent(q)}&type=${type}`)
            .then(res => res.json())
            .then(data => {
                const pre = document.createElement('pre');
                pre.className = 'code';
                pre.textContent = JSON.stringify(data, null, 2);
                resultDiv.innerHTML = '';
                resultDiv.appendChild(pre);
                const p = document.createElement('p');
                p.textContent = data.count > 0 ? '✓ 查询成功' : '⚠ 未找到结果';
                p.style.color = data.count > 0 ? 'var(--success)' : 'var(--warning)';
                resultDiv.appendChild(p);
            })
            .catch(err => {
                resultDiv.textContent = '错误: ' + err.message;
            });
    }
    </script>
</body>
</html>
