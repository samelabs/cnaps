# 银行公开信息数据源

银行机构代码查询服务，提供 Web 界面、REST API 和 MCP 接口，支持 Agent 和 AI bot 集成使用。

## 数据概览

| 类型 | 数据量 | 说明 |
|------|--------|------|
| CNAPS 联行号 | 154,570 | 银行联行号查询 |
| CDS 系统代码 | 154,685 | 电子汇票行号 |
| 票交所代码 | 114,843 | 承兑行号（含地址） |
| 清算行号 | 552 | 人行大额支付直接参与者 |
| 业务渠道 | 3,011 | CDS 业务渠道代码 |

**总计：427,661 条记录**

**最后更新：** 2026-02-05

## 使用方式

### Web 查询
访问 `index.php`，支持银行名称或代码模糊搜索。

### REST API
```
GET /api.php?q=关键词&type=类型
```
支持类型：`cnaps`, `cds`, `acceptor`, `clearing`, `channel`

### MCP 接口
```
POST /mcp.php
Content-Type: application/json

{
  "jsonrpc": "2.0",
  "method": "tools/call",
  "params": {
    "name": "query_cnaps",
    "arguments": { "query": "工商" }
  }
}
```

## 性能说明

- 数据库：SQLite3（带索引优化）
- 查询响应：< 100ms
- 结果限制：50-100 条

## 数据来源

中国人民银行、上海票据交易所、上海清算所公开数据。
