<?php

use Doctrine\DBAL\Schema\Schema;

$schema = new Schema();

$users = $schema->createTable('users');
$users->addColumn('id', 'guid');
$users->addColumn('createdAt', 'datetime');
$users->addColumn('updatedAt', 'datetime', ['notnull' => false]);
$users->addColumn('email', 'string');
$users->addColumn('username', 'string');
$users->addColumn('password', 'string');
$users->addColumn('roles', 'text');
$users->setPrimaryKey(['id']);
$users->addUniqueIndex(['email']);
$users->addUniqueIndex(['username']);

$comestibles = $schema->createTable('comestibles');
$comestibles->addColumn('id', 'guid');
$comestibles->addColumn('createdAt', 'datetime');
$comestibles->addColumn('updatedAt', 'datetime', ['notnull' => false]);
$comestibles->addColumn('userId', 'string');
$comestibles->addColumn('name', 'string');
$comestibles->addColumn('calorie', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibles->addColumn('protein', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibles->addColumn('carbohydrate', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibles->addColumn('fat', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibles->addColumn('defaultValue', 'decimal', ['precision' => 7, 'scale' => 1, 'notnull' => false]);
$comestibles->setPrimaryKey(['id']);
$comestibles->addUniqueIndex(['userId', 'name']);
$comestibles->addForeignKeyConstraint($users, ['userId'], ['id'], ['onDelete' => 'CASCADE']);

$days = $schema->createTable('days');
$days->addColumn('id', 'guid');
$days->addColumn('createdAt', 'datetime');
$days->addColumn('updatedAt', 'datetime', ['notnull' => false]);
$days->addColumn('userId', 'string');
$days->addColumn('date', 'date');
$days->addColumn('weight', 'decimal', ['precision' => 7, 'scale' => 1, 'notnull' => false]);
$days->setPrimaryKey(['id']);
$days->addUniqueIndex(['userId', 'date']);
$days->addForeignKeyConstraint($users, ['userId'], ['id'], ['onDelete' => 'CASCADE']);

$comestiblesWithinDays = $schema->createTable('comestibles_within_days');
$comestiblesWithinDays->addColumn('id', 'guid');
$comestiblesWithinDays->addColumn('createdAt', 'datetime');
$comestiblesWithinDays->addColumn('updatedAt', 'datetime', ['notnull' => false]);
$comestiblesWithinDays->addColumn('dayId', 'string');
$comestiblesWithinDays->addColumn('sorting', 'integer');
$comestiblesWithinDays->addColumn('comestibleId', 'string');
$comestiblesWithinDays->addColumn('amount', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestiblesWithinDays->setPrimaryKey(['id']);
$comestiblesWithinDays->addForeignKeyConstraint($days, ['dayId'], ['id'], ['onDelete' => 'CASCADE']);
$comestiblesWithinDays->addForeignKeyConstraint($comestibles, ['comestibleId'], ['id'], ['onDelete' => 'CASCADE']);

return $schema;
