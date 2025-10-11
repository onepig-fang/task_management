<?php
/**
 * 简洁通用的 PDO 数据库封装，提供 CRUD + 事务，使用预处理防注入
 *
 * 使用示例:
 * $db = new DB('mysql:host=127.0.0.1;dbname=test;charset=utf8mb4', 'root', 'password');
 * $db->insert('users', ['name' => 'Tom', 'age' => 18]);
 * $user = $db->fetch('SELECT * FROM `users` WHERE `id` = :id', ['id' => 1]);
 * $db->update('users', ['age' => 19], ['id' => 1]);
 * $db->delete('users', ['id' => 1]);
 * $list = $db->select('users', ['id','name'], ['age >=' => 18], 'id DESC', 10, 0);
 */

class DB
{
    /** @var PDO */
    protected $pdo;

    /**
     * @param string $dsn      e.g. mysql:host=127.0.0.1;dbname=test;charset=utf8mb4
     * @param string $user
     * @param string $pass
     * @param array  $dbInfo
     *               - dsn(string) e.g. mysql:host=127.0.0.1;dbname=test;charset=utf8mb4
     *               - user(string)
     *               - pass(string)
     * @param array  $options
     *               - persistent(bool) 是否长连接，默认 false
     *               - errmode(int) PDO::ERRMODE_EXCEPTION
     *               - timeout(int) 连接超时秒数
     */
    public function __construct(array $dbInfo, array $options = [])
    {
        $pdoOptions = [
            PDO::ATTR_ERRMODE            => $options['errmode'] ?? PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // 使用原生预处理，提升安全性
        ];

        if (!empty($options['persistent'])) {
            $pdoOptions[PDO::ATTR_PERSISTENT] = true;
        }
        if (!empty($options['timeout'])) {
            $pdoOptions[PDO::ATTR_TIMEOUT] = (int)$options['timeout'];
        }

        $this->pdo = new PDO($dbInfo['dsn'], $dbInfo['user'], $dbInfo['pass'], $pdoOptions);
    }

    /**
     * 核心执行：预处理 + 绑定参数
     * @param string $sql
     * @param array  $params 形如 ['id' => 1, 'name' => 'Tom']
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            // 自动检测类型绑定
            $type = is_int($v) ? PDO::PARAM_INT : (is_bool($v) ? PDO::PARAM_BOOL : (is_null($v) ? PDO::PARAM_NULL : PDO::PARAM_STR));
            // 允许 :name 或 name 两种键
            $paramName = is_int($k) ? $k + 1 : (str_starts_with((string)$k, ':') ? (string)$k : ':' . $k);
            $stmt->bindValue($paramName, $v, $type);
        }
        $stmt->execute();
        return $stmt;
    }

    public function fetch(string $sql, array $params = [], int $mode = PDO::FETCH_ASSOC)
    {
        return $this->query($sql, $params)->fetch($mode);
    }

    public function fetchAll(string $sql, array $params = [], int $mode = PDO::FETCH_ASSOC): array
    {
        return $this->query($sql, $params)->fetchAll($mode);
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * INSERT
     * @param string $table
     * @param array  $data  ['col' => val, ...]
     * @return string 新增自增ID（若有），否则返回影响行数
     */
    public function insert(string $table, array $data)
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Insert data cannot be empty');
        }
        $table = $this->quoteIdentifier($table);

        $cols = [];
        $placeholders = [];
        $params = [];
        foreach ($data as $col => $val) {
            $qcol = $this->quoteIdentifier((string)$col);
            $cols[] = $qcol;
            $ph = ':i_' . $this->normalizeName((string)$col);
            $placeholders[] = $ph;
            $params[substr($ph, 1)] = $val; // 移除冒号，统一给 query 处理
        }

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(',', $cols),
            implode(',', $placeholders)
        );

        $this->query($sql, $params);
        $id = $this->lastInsertId();
        return $id;
    }

    /**
     * UPDATE
     * @param string $table
     * @param array  $data  ['col' => val, ...]
     * @param array  $where 见 buildWhere 用法
     * @return int 影响行数
     */
    public function update(string $table, array $data, array $where): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Update data cannot be empty');
        }
        $table = $this->quoteIdentifier($table);

        $sets = [];
        $params = [];
        foreach ($data as $col => $val) {
            $qcol = $this->quoteIdentifier((string)$col);
            $ph = ':u_' . $this->normalizeName((string)$col);
            $sets[] = $qcol . ' = ' . $ph;
            $params[substr($ph, 1)] = $val;
        }

        [$whereSql, $whereParams] = $this->buildWhere($where, 'w_');
        $params = array_merge($params, $whereParams);

        $sql = sprintf('UPDATE %s SET %s%s', $table, implode(', ', $sets), $whereSql ? ' WHERE ' . $whereSql : '');
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * DELETE
     * @param string $table
     * @param array  $where
     * @return int 影响行数
     */
    public function delete(string $table, array $where): int
    {
        $table = $this->quoteIdentifier($table);
        [$whereSql, $whereParams] = $this->buildWhere($where, 'w_');
        $sql = sprintf('DELETE FROM %s%s', $table, $whereSql ? ' WHERE ' . $whereSql : '');
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    /**
     * 通用 SELECT
     * @param string $table
     * @param array  $columns 默认 ['*']
     * @param array  $where   见 buildWhere
     * @param string $orderBy e.g. "id DESC, created_at ASC" (内部不做转义，请只传可信值或自行处理)
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function select(
        string $table,
        array $columns = ['*'],
        array $where = [],
        string $orderBy = '',
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $table = $this->quoteIdentifier($table);

        if ($columns === ['*']) {
            $colsSql = '*';
        } else {
            $qcols = array_map(function ($c) {
                return $this->quoteIdentifier((string)$c);
            }, $columns);
            $colsSql = implode(',', $qcols);
        }

        [$whereSql, $params] = $this->buildWhere($where, 'w_');
        $sql = sprintf('SELECT %s FROM %s', $colsSql, $table);
        if ($whereSql) {
            $sql .= ' WHERE ' . $whereSql;
        }
        if ($orderBy) {
            // 为安全起见，这里仅做基础校验，建议只传入硬编码可信的 orderBy 字段
            if (!preg_match('/^[a-zA-Z0-9_,\s`\.]+(ASC|DESC)?[\s,`a-zA-Z0-9_\.]*$/', $orderBy)) {
                throw new InvalidArgumentException('Invalid orderBy');
            }
            $sql .= ' ORDER BY ' . $orderBy;
        }
        if ($limit !== null) {
            if ($limit < 0) throw new InvalidArgumentException('limit must be non-negative');
            $sql .= ' LIMIT ' . (int)$limit;
            if ($offset !== null) {
                if ($offset < 0) throw new InvalidArgumentException('offset must be non-negative');
                $sql .= ' OFFSET ' . (int)$offset;
            }
        }

        return $this->fetchAll($sql, $params);
    }

    /**
     * 统计记录总数
     * @param string $table
     * @param array $where 见 buildWhere 用法
     * @param string $column 要统计的列名，默认 '*' 表示统计所有行
     * @return int
     */
    public function count(string $table, array $where = [], string $column = '*'): int
    {
        $table = $this->quoteIdentifier($table);
        
        // 处理列名，允许使用 DISTINCT
        if ($column === '*') {
            $countExpr = 'COUNT(*)';
        } elseif (stripos($column, 'DISTINCT') === 0) {
            // 支持 DISTINCT 统计，如 'DISTINCT user_id'
            $countExpr = 'COUNT(' . $column . ')';
        } else {
            $countExpr = 'COUNT(' . $this->quoteIdentifier($column) . ')';
        }

        [$whereSql, $params] = $this->buildWhere($where, 'c_');
        $sql = sprintf('SELECT %s FROM %s', $countExpr, $table);
        if ($whereSql) {
            $sql .= ' WHERE ' . $whereSql;
        }

        $result = $this->fetch($sql, $params, PDO::FETCH_NUM);
        return (int)($result[0] ?? 0);
    }

     /**
     * 求和
     * @param string $table
     * @param string $column
     * @param array $where
     * @return int
     */
    public function sum(string $table, string $column, array $where = []): int
    {
        $table = $this->quoteIdentifier($table);
        $column = $this->quoteIdentifier($column);
        [$whereSql, $params] = $this->buildWhere($where, 's_');
        $sql = sprintf('SELECT SUM(%s) FROM %s', $column, $table);
        if ($whereSql) {
            $sql .= ' WHERE ' . $whereSql;
        }
        $result = $this->fetch($sql, $params, PDO::FETCH_NUM);
        return (int)($result[0] ?? 0);
    }

    /**
     * 生成 WHERE 子句（支持 = > < >= <= != LIKE IN）
     * where 的写法支持：
     * - ['id' => 1, 'status' => 'ok']          // 等于
     * - ['age >' => 18, 'score >=' => 90]     // 指定操作符
     * - ['name LIKE' => '%Tom%']               // LIKE
     * - ['id' => [1,2,3]]                      // IN
     * - 组合：['age >=' => 18, 'status' => 'ok', 'id' => [1,2]]
     * 注意：所有值均通过预处理绑定，避免注入；键中的字段名会被校验并加反引号
     *
     * @param array  $where
     * @param string $paramPrefix 参数名前缀，避免与其他部分冲突
     * @return array [sql, params]
     */
    protected function buildWhere(array $where, string $paramPrefix = 'w_'): array
    {
        if (empty($where)) return ['', []];

        $parts = [];
        $params = [];
        $i = 0;

        foreach ($where as $key => $val) {
            $i++;
            $key = (string)$key;

            // 解析键中的字段与操作符
            $op = '=';
            $field = $key;
            if (preg_match('/^([a-zA-Z0-9_\.]+)\s+(=|>=|<=|>|<|!=|LIKE)$/', $key, $m)) {
                $field = $m[1];
                $op = $m[2];
            }

            $qField = $this->quoteIdentifierPath($field); // 允许 table.field

            if (is_array($val)) {
                if (empty($val)) {
                    // IN () 空集，无结果，可以用 1=0
                    $parts[] = '1=0';
                    continue;
                }
                // IN 子句
                $inPlaceholders = [];
                foreach ($val as $j => $v) {
                    $ph = ':' . $paramPrefix . $i . '_in_' . $j;
                    $inPlaceholders[] = $ph;
                    $params[substr($ph, 1)] = $v;
                }
                $parts[] = sprintf('%s IN (%s)', $qField, implode(',', $inPlaceholders));
            } else {
                $ph = ':' . $paramPrefix . $i;
                $parts[] = sprintf('%s %s %s', $qField, $op, $ph);
                $params[substr($ph, 1)] = $val;
            }
        }

        return [implode(' AND ', $parts), $params];
    }

    /**
     * 安全包装字段或表名，仅允许 [a-zA-Z0-9_]
     * 返回形如 `name`
     */
    protected function quoteIdentifier(string $name): string
    {
        $name = $this->normalizeName($name);
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            throw new InvalidArgumentException('Invalid identifier: ' . $name);
        }
        return '`' . $name . '`';
    }

    /**
     * 允许 table.field 形式，分别加反引号
     */
    protected function quoteIdentifierPath(string $path): string
    {
        $parts = explode('.', $path);
        $quoted = array_map(function ($p) {
            return $this->quoteIdentifier($p);
        }, $parts);
        return implode('.', $quoted);
    }

    /**
     * 归一化名称：去除多余反引号与空白
     */
    protected function normalizeName(string $name): string
    {
        $name = trim($name);
        $name = str_replace('`', '', $name);
        return $name;
    }
}