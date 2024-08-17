# sequel-pdo

## SQL文の実行

SequelPDO::exeQueryにSQL文とバインドしたい値の配列を渡す。  
返り値がPDOStatementなので、あとは標準のPDOの世界。

```PHP
$sql = <<< SQL
    SELECT *
    FROM items
    WHERE
        deleted_at IS NULL
        AND id = :item_id
    SQL;
$values = [
    'item_id' => 1
];

$stmt = $db->exeQuery($sql, $values);
$item = $stmt->fetch();
```

名前なしのプレースホルダーを使いたい場合は、第3引数にtrueを渡す。

```PHP
$sql = <<< SQL
    SELECT *
    FROM items
    WHERE
        deleted_at IS NULL
        AND id = ?
    SQL;
$values = [
    'item_id' => 1
];

$stmt = $db->exeQuery($sql, $values, SequelPDO::QUESTION);
$item = $stmt->fetch();
```
