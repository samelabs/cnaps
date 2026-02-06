<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCP 接口文档 - 银行行号查询系统</title>
    <style>
        :root {
            --primary: #ff4d4d;
            --coral: #ff6b6b;
            --coral-dark: #ee5a5a;
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e5e7eb;
            --code-bg: #1e293b;
            --text-light: #6b7280;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans SC', sans-serif;
            background: var(--bg);
            color: #1f2937;
            line-height: 1.6;
        }
        .header {
            background: #ff4d4d;
            color: white;
            padding: 2rem 1rem;
        }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem 1rem; }
        .nav { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .nav a {
            padding: 0.5rem 1rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            text-decoration: none;
            color: #1f2937;
            font-size: 0.875rem;
        }
        .section {
            background: var(--card);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .code {
            background: var(--code-bg);
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 0.5rem;
            font-family: monospace;
            font-size: 0.875rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .tool-card {
            padding: 1rem;
            background: var(--bg);
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .tool-name {
            font-family: monospace;
            color: var(--primary);
            font-weight: 600;
        }
        .tool-desc { margin-top: 0.5rem; color: var(--text-light); font-size: 0.875rem; }
        .try-btn {
            padding: 0.5rem 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
        }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.875rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: var(--bg); }
    </style>
</head>
<body>
    <header class="header">
        <h1>MCP 集成文档</h1>
        <p>Model Context Protocol 银行行号查询服务器</p>
    </header>
    <div class="container">
        <nav class="nav">
            <a href="index.php">🏠 返回首页</a>
            <a href="api-docs.php">📡 API 文档</a>
        </nav>
        <div class="section">
            <h2>📋 基本信息</h2>
            <table>
                <tr><th>项目</th><th>说明</th></tr>
                <tr><td>MCP 服务器</td><td>/mcp.php</td></tr>
                <tr><td>协议版本</td><td>2024-11-05</td></tr>
                <tr><td>数据更新</td><td>每周一凌晨</td></tr>
            </table>
        </div>
        <div class="section">
            <h2>🔧 可用工具</h2>
            <div class="tool-card">
                <div class="tool-name">query_cnaps</div>
                <div class="tool-desc">查询银行联行号（15.5万条）</div>
            </div>
            <div class="tool-card">
                <div class="tool-name">query_cds</div>
                <div class="tool-desc">查询电子汇票行号（15.5万条）</div>
            </div>
            <div class="tool-card">
                <div class="tool-name">query_acceptor</div>
                <div class="tool-desc">查询承兑行号含地址（11.5万条）</div>
            </div>
            <div class="tool-card">
                <div class="tool-name">query_clearing</div>
                <div class="tool-desc">查询清算行号（552条）</div>
            </div>
            <div class="tool-card">
                <div class="tool-name">query_channel</div>
                <div class="tool-desc">查询业务渠道（3011条）</div>
            </div>
            <div class="tool-card">
                <div class="tool-name">get_stats</div>
                <div class="tool-desc">获取数据统计信息</div>
            </div>
        </div>
        <div class="section">
            <h2>⚙️ Claude Desktop 配置</h2>
            <p style="margin: 0 0 1rem; color: #6b7280;">配置文件：</p>
            <div class="code">~/Library/Application Support/Claude/claude_desktop_config.json</div>
            <p style="margin: 1rem 0 0.5rem; color: #6b7280;">配置示例：</p>
            <div class="code">{
  "mcpServers": {
    "bank-code": {
      "url": "http://localhost:8080/mcp.php",
      "transport": "http"
    }
  }
}</div>
        </div>
        <div class="section">
            <h2>🧪 在线测试</h2>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <select id="mcpTool" style="padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                    <option value="query_cnaps">query_cnaps</option>
                    <option value="query_cds">query_cds</option>
                    <option value="query_acceptor">query_acceptor</option>
                    <option value="query_clearing">query_clearing</option>
                    <option value="query_channel">query_channel</option>
                </select>
                <input type="text" id="mcpQuery" placeholder="查询关键词" value="工商"
                       style="flex: 1; min-width: 150px; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                <button class="try-btn" onclick="testMCP()">测试</button>
            </div>
            <div id="mcpResult"></div>
        </div>
    </div>
    <script>
    function testMCP() {
        const tool = document.getElementById('mcpTool').value;
        const query = document.getElementById('mcpQuery').value;
        const resultDiv = document.getElementById('mcpResult');
        const payload = {
            jsonrpc: '2.0',
            id: 1,
            method: 'tools/call',
            params: { name: tool, arguments: { query: query } }
        };
        const pre = document.createElement('pre');
        pre.className = 'code';
        pre.textContent = '查询中...';
        resultDiv.innerHTML = '';
        resultDiv.appendChild(pre);
        fetch('mcp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => { pre.textContent = JSON.stringify(data, null, 2); })
        .catch(err => { pre.textContent = 'Error: ' + err.message; });
    }
    </script>
</body>
</html>
