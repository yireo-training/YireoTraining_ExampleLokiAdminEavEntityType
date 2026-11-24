<?php declare(strict_types=1);

namespace YireoTraining\ExampleLokiAdminEavEntityType\Component\Grid;

use Loki\AdminComponents\Component\Grid\GridRepository;

class ReadOnlyGridRepository extends GridRepository
{
    public function saveValue(mixed $value): void
    {
        if (is_array($value)) {
            unset($value['inline_edit']);
        }

        parent::saveValue($value);
    }
}
