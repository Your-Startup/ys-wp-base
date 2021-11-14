<?php
namespace RB\Site\Repository;

use Symfony\Component\Validator\{
    Constraints as Assert,
    ConstraintViolationList,
    Validation,
    Validator\ValidatorInterface
};

use RB\Site\Exception\ValidationException;

abstract class Validator
{
    private ValidatorInterface $validator;
    private array              $fields = [];

    const EXTRA_FIELDS_MESSAGE   = 'Недопустимые параметры';
    const MISSING_FIELDS_MESSAGE = 'Отсутствуют обязательные параметры';

    private function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Отдает валидатор, создает если его нет
     *
     * @return Validator
     */
    public static function getValidator()
    {
        static $instance;

        if ($instance === null) {
            $instance = new static();
        }

       return $instance;
    }

    /**
     * Проверяет данные
     *
     * @param array $fields Данные которые необходимо проверить
     * @param string $group Группа валидации
     *
     * @return bool
     */
    final public function validate(array $fields = [], string $group = 'Default'): bool
    {
        // https://symfony.com/doc/current/reference/constraints/Collection.html#options
        // https://symfony.com/doc/current/validation/groups.html
        // https://symfony.com/doc/current/reference/constraints.html
        // https://symfony.com/doc/current/reference/constraints/Collection.html#required-and-optional-field-constraints

        if (!$fields) {
            $fields = $this->fields;
        }

        $violations = $this->validator->validate($fields, new Assert\Collection([
            'fields'               => $this->getRules($group),
            'allowExtraFields'     => false,
            'extraFieldsMessage'   => self::EXTRA_FIELDS_MESSAGE,
            'allowMissingFields'   => false,
            'missingFieldsMessage' => self::MISSING_FIELDS_MESSAGE
        ]), null);

        if ($violations->count()) {
            /** @var ConstraintViolationList $violations */
            throw new ValidationException($violations);
        }

        return true;
    }

    /**
     * Все что не разрешено, запрещено. Нужно описывать все параметры в массиве.
     *
     * @param string $group
     *
     * @return array
     */
    public function getRules(string $group = 'Default'): array
    {
        $rules = [
            'with_pagination' => new Assert\Optional([
                new Assert\Type(['type' => 'bool']),
            ]),
            'with_total' => new Assert\Optional([
                new Assert\Type(['type' => 'bool']),
            ]),
            'disable-bapsa' => new Assert\Optional([
                new Assert\Type(['type' => 'numeric']),
            ]),
            // TODO: добавить кастомный валидатор
            'sort' => new Assert\Optional([
                new Assert\Type(['type' => 'array']),
            ]),
            // TODO: добавить кастомный валидатор
            'filter' => new Assert\Optional([
                new Assert\Type(['type' => 'array']),
            ]),
            // TODO: добавить кастомный валидатор
            'fields' => new Assert\Optional([
                new Assert\Type(['type' => 'array']),
            ]),
        ];

        if (getenv('RB_ENV') !== 'live') {
            $rules['XDEBUG_SESSION_START'] = new Assert\Optional([
                new Assert\Choice([
                    'choices' => ['XDEBUG', 'PHPSTORM'],
                    'message' => 'Значение должно быть XDEBUG или PHPSTORM'
                ]),
            ]);
        }

        return $rules;
    }
}