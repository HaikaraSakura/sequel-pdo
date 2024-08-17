<?php

declare(strict_types=1);

namespace Haikara\SequelPdo;

use Haikara\SequelPdo\Exceptions\LastInsertIdException;
use Haikara\SequelPdo\Exceptions\RollBackException;
use PDO;
use PDOStatement;
use Stringable;

/**
 * PDOのラッパー
 */
interface SequelPDOInterface
{
    public const PDO_MYSQL_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // エラー発生時に例外を投げる。
        PDO::ATTR_EMULATE_PREPARES => false, // 静的プレースホルダを利用。
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false, // 複文の実行を禁止。
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // デフォルトのフェッチモードをFETCH_ASSOCに設定。
    ];

    public const PDO_SQLITE_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // エラー発生時に例外を投げる。
        PDO::ATTR_EMULATE_PREPARES => false, // 静的プレースホルダを利用。
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // デフォルトのフェッチモードをFETCH_ASSOCに設定。
    ];

    public const QUESTION = true;
    public const NAMED = false;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo);

    /**
     * SQLをステートメントとして登録、値をバインドし、実行する
     *
     * @param string|Stringable $sql
     * @param iterable<BindValue|string|Stringable|int|float|bool|null> $values
     * @param bool $placeholderType
     * @return PDOStatement
     */
    public function exeQuery(
        string|Stringable $sql,
        iterable $values = [],
        bool $placeholderType = self::QUESTION
    ): PDOStatement;

    /**
     * @return int
     * @throws LastInsertIdException
     */
    public function getLastInsertId(): int;

    /**
     * @return bool
     */
    public function begin(): bool;

    /**
     * @return bool
     */
    public function commit(): bool;

    /**
     * @return bool
     */
    public function rollBack(): bool;

    /**
     * @param callable $fn
     * @param mixed ...$args
     * @return void
     * @throws RollBackException
     */
    public function transaction(callable $fn, mixed ...$args): void;

    /**
     * デフォルトフェッチモードを変更する。
     *
     * @param int $fetchMode
     * @return void
     */
    public function setFetchMode(int $fetchMode): void;

    /**
     * デフォルト設定でPDOをインスタンス化。それをもとにKnsPDO自身をインスタンス化して返す。
     * 独自に設定する必要があれば、外でインスタンス化したPDOをコンストラクタに渡してnewする。
     *
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     * @return static
     */
    public static function connect(
        string $dsn,
        string|null $username = null,
        string|null $password = null
    ): self;
}
