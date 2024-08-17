<?php

declare(strict_types=1);

namespace Haikara\SequelPdo;

use Haikara\SequelPdo\Exceptions\LastInsertIdException;
use Haikara\SequelPdo\Exceptions\RollBackException;
use PDO;
use PDOException;
use PDOStatement;

use Stringable;

use function filter_var;
use function is_int;

/**
 * PDOのラッパー
 * 取得した値の型に注意
 */
class SequelPDO implements SequelPDOInterface
{
    public function __construct(protected PDO $pdo)
    {
    }

    /**
     * @inheritDoc
     */
    public function exeQuery(
        string|Stringable $sql,
        iterable $values = [],
        bool $placeholderType = self::QUESTION
    ): PDOStatement {
        $sql = (string) $sql;

        $values = is_array($values)
            ? $values
            : iterator_to_array($values);

        if ($values !== []) {
            $stmt = $this->pdo->prepare($sql);
            $this->bindValues($stmt, $values, $placeholderType);
            $stmt->execute();
        } else {
            $stmt = $this->pdo->query($sql);
        }

        return $stmt;
    }

    /**
     * @inheritDoc
     */
    public function getLastInsertId(): int
    {
        $id = filter_var(
            $this->pdo->lastInsertId(),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if (!is_int($id)) {
            throw new LastInsertIdException('AUTO INCREMENTされた履歴がありません。');
        }

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function begin(): bool
    {
        return !$this->pdo->inTransaction() && $this->pdo->beginTransaction();
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->pdo->inTransaction() && $this->pdo->commit();
    }

    /**
     * @inheritDoc
     */
    public function rollBack(): bool
    {
        return $this->pdo->inTransaction() && $this->pdo->rollback();
    }

    /**
     * @inheritDoc
     */
    public function transaction(callable $fn, mixed ...$args): void
    {
        try {
            $this->begin();
            $fn(...$args);
            $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            throw new RollBackException('RollBackが発生しました。', 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function setFetchMode(int $fetchMode): void
    {
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $fetchMode);
    }

    /**
     * @inheritDoc
     */
    public static function connect(
        string $dsn,
        string|null $username = null,
        string|null $password = null,
        array|null $options = null
    ): SequelPDOInterface {
        $pdo = new PDO($dsn, $username, $password, $options);
        return new static($pdo);
    }

    /**
     * @param PDOStatement $stmt
     * @param iterable<BindValue|string|Stringable|int|float|bool|null> $values
     * @param bool $placeholderType
     * @return void
     */
    protected function bindValues(
        PDOStatement $stmt,
        iterable $values,
        bool $placeholderType = self::QUESTION
    ): void {
        $i = 1;
        foreach ($values as $name => $value) {
            $placeholder = $placeholderType ? $i : $name;
            $this->bindValue($stmt, $placeholder, $value);
            $i++;
        }
    }

    /**
     * PDOStatementに値をバインドする。
     * $questionsがfalseなら名前付き、trueなら通常のハテナのプレースホルダー。
     *
     * @param PDOStatement $stmt
     * @param positive-int|string $placeholder
     * @param string|Stringable|int|float|bool|null $value
     * @return SequelPDOInterface
     */
    protected function bindValue(
        PDOStatement $stmt,
        int|string $placeholder,
        string|int|float|bool|object|null $value
    ): SequelPDOInterface {
        $bindValueObj = $value instanceof BindValueInterface
            ? $value
            : new BindValue($value);

        $stmt->bindValue(
            $placeholder,
            $bindValueObj->getValue(),
            $bindValueObj->getParamType()
        );

        return $this;
    }
}
