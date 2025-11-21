<?= "<?php\n" ?>

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace <?= $namespace ?>;

<?php foreach ($imports as $import): ?>
use <?= $import ?>;
<?php endforeach ?>

class <?= $class_name ?> extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
<?php foreach ($fields as $field_name => $field_info): ?>
<?php if (is_array($field_info)): ?>
        $builder->add('<?= $field_name ?>', <?= $field_info['type'] ?>::class, [
            'class' => <?= $field_info['target_entity_short'] ?>::class,
        ]);
<?php else: ?>
        $builder->add('<?= $field_name ?>', <?= $field_info ?>::class);
<?php endif ?>
<?php endforeach ?>
    }
}
